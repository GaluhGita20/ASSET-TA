<?php

namespace App\Models\Pengajuan;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Approval;
use App\Models\Globals\MenuFlow;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use App\Models\Model;
use App\Models\Master\Aset\AsetRs;
use App\Models\Traits\ResponseTrait;
use App\Models\Inventaris\Aset;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Traits\HasApprovals;
use App\Models\Traits\HasFiles;
use App\Support\Base;
use Telegram\Bot\Laravel\Facades\Telegram;
use Carbon\Carbon;

class Penghapusan extends Model
{
    use HasFiles, HasApprovals;

    protected $table = 'trans_penghapusan';

    protected $fillable = [
        'code',
        'departemen_id',
        'kib_id',
        'submmission_date',
        'desc_del',
        'status',
    ];

    protected $casts = [
        'submmission_date'   => 'date',
    ];

    /*******************************
     ** MUTATOR
     *******************************/

    /*******************************
     ** ACCESSOR
     *******************************/

    /*******************************
     ** RELATION
     *******************************/

    public function deps()
    {
        return $this->belongsTo(OrgStruct::class, 'departemen_id');
    }

    public function asets()
    {
        return $this->belongsTo(Aset::class, 'kib_id');
    }



    /*******************************
     ** SCOPE
     *******************************/

     public function scopeGrid($query)
    {
        $user = auth()->user();
        return $query->when(!in_array($user->position->location->id, [8,17]), 
            function ($q) use ($user) { 
            return $q->when($user->position->imKepalaDeparetemen(), 
                function ($qq) use ($user) {
                    return $qq->whereIn('departemen_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
                },
                function ($qq) use ($user) {
                    return $qq->where('departemen_id', $user->position->location->id); 
                }
            );
        })->when(auth()->user()->roles->pluck('id')->contains(7), function ($query) {
            $query->orWhereHas('approvals', function ($q) {
                $q
                    ->where('order', 1)
                    ->where('status', 'approved');
            });
        });

    }

    public function scopeGridStatusCompleted($query)
    {
        return $query->where('status', 'completed')->latest();
    }

    public function scopeFilters($query)
    {
        return $query->filterBy(['code','procurement_year'])
        ->filterBy(['struct_id'])->latest();
        
    }

    /*******************************
     ** SAVING
     *******************************/
    public function handleStore($request,$statusOnly = false){
        $this->beginTransaction();
        try {

            // dd($request->all());
            $data = $request->all();
            $this->fill($data);
            
            $idMax = Penghapusan::where('departemen_id', $request->departemen_id)->count('id');
            $dep = OrgStruct::where('id', $request->departemen_id)->first(['name']);

            $uid= Aset::where('id',$request->kib_id)->pluck('usulan_id');
            $aset = PerencanaanDetail::where('id',$uid)->pluck('ref_aset_id');
            $name= AsetRs::where('id',$aset[0])->pluck('name');
      
            // dd(Carbon::createFromFormat('Y/m/d',now()));
            $this->submmission_date = Carbon::now();

 
            $format_angka = str_pad(($idMax + 1) < 10 ? '0' . ($idMax + 1) : ($idMax + 1), 3, '0', STR_PAD_LEFT);

            $this->code = $format_angka."/Penghapusan Aset/".$name[0]."/".$dep->name."/".now()->format('d/m/Y');
            $this->save();
            
            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');

            if($request->is_submit == 1 ){
                $this->handleSubmitSave($request);
            }

         
            $this->saveLogNotify();
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    // public function handleStoreOrUpdate($request,$statusOnly = false){
     
    //     $data = $request->all();
    //     $this->fill($data);
    //        // dd($request->all());
    //         // $idMax = Penghapusan::where('departemen_id',$request->departemen_id)->count('id');
    //         // $dep = OrgStruct::where('id',$request->departemen_id)->first('name');
    //         // $format_angka = str_pad(($idMax+1) < 10 ? '0' . ($idMax+1) : ($idMax+1), 3, '0', STR_PAD_LEFT);
    //         // $this->code = $format_angka."/ Penghapusan Aset /".$dep->name."/".now()->format('d/m/Y');
    //         // $this->dates = now()->format('d/m/Y');
    //         $this->save();
    //         $this->saveLogNotify();
    //         $redirect = route(request()->get('routes') . '.index');
    //         return $this->commitSaved(compact('redirect'));
    // }


    public function handleStoreOrUpdate($request, $statusOnly = false)
    {
        $this->beginTransaction();
        try {

            $data = $request->all();
            $this->fill($data);
            $this->save();

            if($request->is_submit == 1 ){
                $this->handleSubmitSave($request);
            }
         
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleReject($request)
    {
        $this->beginTransaction();
        try {
            $this->rejectApproval($request->module, $request->note);
            $this->update(['status' => 'rejected']);
            $this->saveLogNotify();

            $redirect = route(request()->get('routes').'.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }


    public function handleDestroy()
    {
        $this->beginTransaction();
        try {
            $this->saveLogNotify();
            $this->delete();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }


    public function handleSubmitSave($request)
    {
        $this->beginTransaction();
        try {

            $this->update(['status' => 'waiting.approval']);
            //dd($this->status);
            $this->generateApproval($request->module);
            $this->saveLogNotify();
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));

        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }



    public function handleApprove($request)
    {
        $this->beginTransaction();
        try {
            // dd($request);
                if ($this->status === 'waiting.approval.revisi') {
                    $this->approveApproval($request->module . '_upgrade');
                    if ($this->firstNewApproval($request->module . '_upgrade')) {
                        $this->update(['status' => 'waiting.approval.revisi']);
                        $this->saveLogNotify();
                    } else {
                        $this->update(
                            [
                                'status' => 'draft',
                                'version' => $this->version + 1,
                            ]
                        );
                        $this->saveLogNotify();
                    }
                } else {
                    $this->approveApproval($request->module);
                    if ($this->firstNewApproval($request->module)) {
                        $this->update(['status' => 'waiting.approval']);
                        $this->saveLogNotify();
                    } else {
                        // dd($this->kib_id);
                        $this->update(['status' => 'completed']);
                        Aset::where('id',$this->kib_id)->update(['status'=>'notactive']);
                        $this->saveLogNotify();

                    }
                }
    
                $redirect = route(request()->get('routes') . '.index');
                return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            $customMessage = 'This is a custom error message';
            return $this->rollback($customMessage);
          //  return $this->rollbackSaved($e);
            
        }
    }

    public function saveLogNotify()
    {
        $user = auth()->user()->name;
        $data = 'Pengajuan Penghapusan Aset dengan No Surat : ' . $this->code;
        $routes = request()->get('routes');
      //  dd(request()->route()->getName());
        switch (request()->route()->getName()) {
            case $routes . '.store':
                $this->addLog('Membuat ' . $data);
                $pesan = $user.' Membuat ' . $data;
                $this->sendNotification($pesan);
                if (request()->is_submit) {
                    $this->addLog('Submit ' . $data);
                    
                    $this->addNotify([
                        'message' => 'Waiting Approval ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);

                    $pesan = $user.' Menunggu Approval ' . $data;
                    $this->sendNotification($pesan);
                }
                break;
            case $routes . '.update':
                $this->addLog('Mengubah ' . $data);
                if (request()->is_submit == 1) {
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Approval ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        // dd('tes'),

                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);

                   
                    $pesan = $user.' Menunggu Approval ' . $data;
                    $this->sendNotification($pesan);
                }

                break;
            case $routes . '.destroy':
                $this->addLog('Menghapus ' . $data);
                break;
            case $routes . '.verify':
                $this->addNotify([
                    'message' => 'Waiting Approval ' . $data,
                    'url' => route($routes . '.approval', $this->id),
                    'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                ]);
                break;
            case $routes . '.approve':
                if (in_array($this->status, ['draft', 'waiting.approval.revisi'])) {
                    $this->addLog('Menyetujui Revisi ' . $data);

                    $this->addNotify([
                        'message' => 'Waiting Approval Revisi ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);

                    $pesan = $user. ' Waiting Approval Revisi ' . $data;
                    $this->sendNotification($pesan);

                } else {
                    $this->addLog('Menyetujui ' . $data);

                    $this->addNotify([
                        'message' => 'Waiting Approval ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);
                    $pesan = $user. ' Menyetujui ' . $data;
                    $this->sendNotification($pesan);
                }
                break;
            case $routes . '.reject':
                if (in_array($this->status, ['rejected'])) {
                    $this->addLog('Menolak ' . $data . ' dengan alasan: ' . request()->get('note'));

                    $this->addNotify([
                        'message' => 'Menolak ' . $data . ' dengan alasan: ' . request()->get('note'),
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => [$this->created_by],
                    ]);

                    $pesan = $user .' Menolak Pengajuan ' . $data;
                    $this->sendNotification($pesan);
                } else {
                    $this->addLog('Menolak Revisi ' . $data . ' dengan alasan: ' . request()->get('note'));

                    $this->addNotify([
                        'message' => 'Menolak Revisi ' . $data . ' dengan alasan: ' . request()->get('note'),
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => [$this->created_by],
                    ]);
                }
                break;
            case $routes . '.revisi':
                $this->addLog('Revisi ' . $data);
                $this->addNotify([
                    'message' => 'Waiting Approval Revisi ' . $data,
                    'url' => route($routes . '.approval', $this->id),
                    'user_ids' => $this->getNewUserIdsApproval(request()->get('module') . "_upgrade"),
                ]);
                break;
        }
    }

    public function sendNotification($pesan)
    {
        $chatId = '-4054507555'; // Ganti dengan chat ID penerima notifikasi

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $pesan,
        ]);
    }


    public function checkAction($action, $perms, $record = null)
    {
        $user = auth()->user();
        switch ($action) {
            case 'edit':
                $checkStatus = in_array($this->status, ['new', 'draft', 'rejected']);
                return $checkStatus && $user->checkPerms($perms . '.edit');
            case 'delete':
                $checkStatus = in_array($this->status, ['new', 'draft', 'rejected']);
                return $checkStatus && $user->checkPerms($perms . '.delete');
            case 'show':
            case 'history':
                return true;
            case 'approval':
                if ($this->status === 'waiting.approval.revisi') {
                    if ($this->checkApproval(request()->get('module') . '_upgrade')) {
                        return $user->checkPerms($perms . '.approve');
                    }
                }
                if ($this->checkApproval(request()->get('module'))) {
                    $checkStatus = in_array($this->status, ['waiting.approval']);
                    return $checkStatus && $user->checkPerms($perms . '.approve');
                }
                break;
            case 'revisi':
                if ($user->checkPerms($perms . '.edit')) {
                    // if (isset($this->pembatalan->status) && in_array($this->pembatalan->status, ['waiting.approval', 'waiting.approval.revisi', 'completed'])) {
                    //     return false;
                    // }
                    // if (isset($this->pembayaran->status) &&  in_array($this->pembayaran->status, ['waiting.approval', 'waiting.approval.revisi', 'completed'])) {
                    //     return false;
                    // }
                    return $this->status === 'completed';
                }
                break;
            case 'tracking':
                $checkStatus = in_array($this->status, ['waiting.approval', 'completed', 'waiting.approval.revisi']);
                return $checkStatus;
            case 'print':
                $checkStatus = in_array($this->status, ['waiting.approval', 'rejected', 'completed']);
                return $checkStatus;
        }

        return false;
    }
}
