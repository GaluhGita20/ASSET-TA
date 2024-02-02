<?php

namespace App\Models\Pengajuan;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Approval;
use App\Models\Globals\MenuFlow;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use App\Models\Model;
use App\Models\Traits\ResponseTrait;
use App\Models\Master\Aset\AsetRs;
use App\Models\Inventaris\Aset;
use App\Models\Traits\HasApprovals;
use App\Models\Traits\HasFiles;
use App\Support\Base;
use Telegram\Bot\Laravel\Facades\Telegram;
use Carbon\Carbon;

class Perbaikan extends Model
{
    use HasFiles, HasApprovals;

    protected $table = 'trans_perbaikan';

    protected $fillable = [
        'code',
        'kib_id',
        'departemen_id',
        'status',
        'repair_results',
        'repair_date',
        'submission_date',
        'problem',
        'action_repair',
        'is_disposisi',
    ];

    protected $casts = [
        'submission_date'  => 'date',
        'repair_date'  => 'date'
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

    public function petugas()
    {
        return $this->belongsToMany(User::class, 'trans_pivot_perbaikan', 'perbaikan_id', 'user_id');
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
        })->latest();
    }

    public function scopeGridStatusCompleted($query)
    {
        return $query->where('status', 'completed')->latest();
    }

    public function scopeFilters($query)
    {
        return $query->filterBy(['code'])
        ->filterBy(['departemen_id'])->latest();
        
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
            $idMax = Perbaikan::where('departemen_id',$request->departemen_id)->count('id');
            $dep = OrgStruct::where('id',$request->departemen_id)->first('name');
            $format_angka = str_pad(($idMax+1) < 10 ? '0' . ($idMax+1) : ($idMax+1), 3, '0', STR_PAD_LEFT);

            $uid= Aset::where('id',$request->kib_id)->pluck('usulan_id');
            $aset = PerencanaanDetail::where('id',$uid)->pluck('ref_aset_id');
            $name= AsetRs::where('id',$aset[0])->pluck('name');

            $this->code = $format_angka."/ Perbaikan Aset /".$name[0]."/".$dep->name."/".now()->format('d/m/Y');
            $this->submission_date = Carbon::now();
         
            $this->save();

            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');

            if($request->is_submit == 0 ){
                $this->handleSubmitSave($request);
            }
            $this->saveLogNotify();
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleVerify($request){
        $this->beginTransaction();
        try {

            $this->update(['repair_date' => $request->repair_date]);
            $this->approveApproval($request->module);
           
            $this->update(['status' => 'approved']);
        
            $user = auth()->user()->name;
            $this->addLog('Memverifikasi '. $this->code);
            $this->addNotify([
                'message' => 'Melakukan Verifikasi Permintaan Perbaikan Aset ',$this->code,
                'url' => route('pengajuan.perbaikan-aset.show',$this->id),
                'user_ids' => [$this->created_by],
            ]);
           
            $pesan = ($user.' Melakukan Verifikasi Permintaan Perbaikan Aset ' . $this->code);
            $this->sendNotification($pesan);

            $redirect = route(request()->get('routes') . '.index');

            return $this->commitSaved(compact('redirect'));

        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleStoreOrUpdate($request){
        $this->beginTransaction();
        try {
          
            $data = $request->all();
            $this->fill($data);
            $this->save();
            $this->petugas()->sync($request->user_id ?? []);

            $user = auth()->user()->name;
            if($request->repair_results == 'SELESAI'){
                Aset::where('id',$request->kib_id)->update(['condition'=>'baik']);
                
            }elseif($request->repair_results == 'ALAT TIDAK BISA DIGUNAKAN'){
                Aset::where('id',$request->kib_id)->update(['condition'=>'rusak berat']);
               
            }
            $user = auth()->user()->name;
            $this->addLog('Update Hasil Perbaikan '. $this->code);
            $this->addNotify([
                'message' => 'Melakukan Update Hasil Perbaikan Aset ',$this->code,
                'url' => route('pengajuan.perbaikan-aset.show',$this->id),
                'user_ids' => [$this->created_by],
            ]);
           
            $pesan = ($user.' Melakukan Update Hasil Perbaikan Aset ' . $this->code);
            $this->sendNotification($pesan);
            
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleDestroy()
    {
        $this->beginTransaction();
        try {
            Aset::where('id',$this->kib_id)->update(['condition'=>'baik']);
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
            $module ='perbaikan-aset';
            // dd('tes');
            Aset::where('id',$request->kib_id)->update(['condition'=>'rusak sedang']);
            $this->generateApproval($module);
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
        
           
          
        } catch (\Exception $e) {
            return $this->rollback($e);
        }
        

    }

    public function saveLogNotify()
    {
        $user = auth()->user()->name;
        // dd('hjhjh');
        $data = 'Pengajuan Perbaikan Aset : ' . $this->code;
        $routes = request()->get('routes');
        switch (request()->route()->getName()) {
            case $routes . '.store':
                $this->addLog('Membuat ' . $data);
                $pesan = $user.' Melakukan ' . $data;
                $this->sendNotification($pesan);
                if (request()->is_submit) {
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Verification ' . $data,
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->struct),
                    ]);
                    $pesan = $user.' Menunggu Verifikasi ' . $data;
                    $this->sendNotification($pesan);
                }
                break;
            case $routes . '.update':
                $this->addLog('Mengubah ' . $data);
                if($this->status == 'waiting.verify'){
                        dd('yh');

                        $this->addNotify([
                            'message' => 'Memverifikasi ' . $data,
                            'url' => route($routes . '.show', $this->id),
                            'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->departemen_id),
                        ]);
                 
                        $pesan = $user.' Memverifikasi ' . $data;
                        $this->sendNotification($pesan);
                    }
                    if($this->status == 'verify'){
                        dd($tes);
                        $this->addNotify([
                            'message' => 'Melakukan Update Hasil Perbaikan Aset ' . $data,
                            'url' => route($routes . '.show', $this->id),
                            'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->departemen_id),
                        ]);
                        dd('tes');
                        $pesan = $user.' Melakukan Update Hasil Perbaikan Aset ' . $data;
                        $this->sendNotification($pesan);
                    }

                
                break;
            case $routes . '.destroy':
                $this->addLog('Menghapus ' . $data);
                break;
            case $routes . '.verify':
                $this->addNotify([
                    'message' => 'Melakukan Verifikasi ' . $data,
                    'url' => route($routes . '.show', $this->id),
                    'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                ]);
                $pesan = $user.' Melakukan Verifikasi Permintaan Perbaikan ' . $data;
                $this->sendNotification($pesan);

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
            // case 'verification':
            //     if ($this->status === 'waiting.verification') {
            //         return $user->isVerificationKepalaDepartement($this->struct);
            //     }
            //     return false;
            //     break;
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
