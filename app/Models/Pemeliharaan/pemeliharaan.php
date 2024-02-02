<?php

namespace App\Models\Pemeliharaan;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Approval;
use App\Models\Globals\MenuFlow;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use App\Models\Model;
use App\Models\Traits\ResponseTrait;
use App\Models\Inventaris\Aset;
use App\Models\Pemeliharaan\PemeliharaanDetail;
use App\Models\Traits\HasApprovals;
use App\Models\Traits\HasFiles;
use App\Support\Base;
use Telegram\Bot\Laravel\Facades\Telegram;
use Carbon\Carbon;

class Pemeliharaan extends Model
{
    use HasFiles, HasApprovals;

    protected $table = 'trans_pemeliharaan';

    protected $fillable = [
        'code',
        'departemen_id',
        'dates',
    ];

    protected $casts = [
        'dates'  => 'date',
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


    public function details()
    {
        return $this->hasMany(PemeliharaanDetail::class, 'pemeliharaan_id');
    }

    // public function petugas()
    // {
    //     return $this->belongsToMany(User::class, 'trans_pivot_perbaikan', 'perbaikan_id', 'user_id');
    // }

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
           
            $data = $request->all();
            $this->fill($data);
            $time = Carbon::createFromFormat('d/m/Y', $request->dates);


            $idMax = Pemeliharaan::where('departemen_id',$request->departemen_id)->count('id');
            $dep = OrgStruct::where('id',$request->departemen_id)->first('name');
            $format_angka = str_pad(($idMax+1) < 10 ? '0' . ($idMax+1) : ($idMax+1), 3, '0', STR_PAD_LEFT);
            $this->code = $format_angka."/ Pemeliharaan Aset /".$dep->name."/".$time->month."/".$time->year;
            

            $last = str_pad(($idMax) < 10 ? '0' . ($idMax) : ($idMax), 3, '0', STR_PAD_LEFT);
            $last_code = $last."/ Pemeliharaan Aset /".$dep->name."/".$time->month."/".$time->year;
            $flag = Pemeliharaan::where('code',$last_code)->count();
        
            if($flag != 0){
                return $this->rollback(
                    [
                        'message' => 'Pemeliharaan Pada Unit '.$dep->name.' Bulan '.$time->month.'/'.$time->year.' Sudah Dilakukan!'
                    ]
                );
            }

            $this->save();

            $this->createDetail($request);
            $this->saveLogNotify();
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function createDetail($request){
        $dep = $request->departemen_id;
        $data = Aset::where('condition','baik')->where('status', 'active')->whereHas('usulans', function ($q) use ($dep) {
            $q->whereHas('perencanaan', function ($qq) use ($dep) {
                $qq->where('struct_id', $dep);
            });
        })->orWhere('location_hibah_aset', $dep)->whereHas('usulans', function ($q){
            $q->whereHas('trans', function ($qq){
                $qq->where('unit_cost','>=', 100000);
            });
        })->whereIn('type', ['KIB B','KIB E'])->pluck('id')->toArray();;
      
        // dd($data);
        foreach ($data as $aset_id) {
            $detail = new PemeliharaanDetail;
            $detail->pemeliharaan_id = $this->id;
            $detail->kib_id = $aset_id;
            $detail->save();
        }
        
    }

    public function handleStoreOrUpdate($request){
        $this->beginTransaction();
        try {
          
            $data = $request->all();
            $this->fill($data);
            $this->save();
            
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }


    public function handleDetailStoreOrUpdate($request, PemeliharaanDetail $detail)
    {
        $this->beginTransaction();
        try {
            $detail->fill($request->all());
            $this->details()->save($detail);
            $this->save();

            $awal = PemeliharaanDetail::where('pemeliharaan_id',$this->id)->count('id');
            $flag = PemeliharaanDetail::where('pemeliharaan_id',$this->id)->where('maintenance_action','<>',null)->count('id');
           
            if($flag == $awal){
                Pemeliharaan::where('id',$this->id)->update(['status'=>'completed']);
                $this->saveLogNotify();
            }

            return $this->commitSaved();

        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleDestroy()
    {
        $this->beginTransaction();
        try {
            PemeliharaanDetail::where('pemeliharaan_id',$this->id)->delete();
            $this->delete();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }

    public function handleDetailDestroy(PemeliharaanDetail $detail)
    {
        $this->beginTransaction();
        
        try {
            // $this->saveLogNotify();
            $detail->delete();
            return $this->commitDeleted([
                'redirect' => route(request()->routes . '.detail', $this->id)
            ]);
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }



    public function saveLogNotify()
    {
        $user = auth()->user()->name;
        $data = 'Membuat Jadwal Pemeliharaan Aset : ' . $this->code;
        $routes = request()->get('routes');
        switch (request()->route()->getName()) {
            case $routes . '.store':
                $this->addLog('Membuat ' . $data);
                $pesan = $user.' Membuat ' . $data;
                $this->sendNotification($pesan);
                if (request()->is_submit) {
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Verification ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->struct),
                    ]);
                    $pesan = $user.' Menunggu Verifikasi ' . $data;
                    $this->sendNotification($pesan);
                }
                break;
            case $routes . '.update':
                $this->addLog('Mengubah ' . $data);
                if (request()->is_submit) {
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Verification ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->struct),
                    ]);

                    $pesan = $user.' Menunggu Verifikasi ' . $data;
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
