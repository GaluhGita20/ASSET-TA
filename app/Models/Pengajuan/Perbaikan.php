<?php

namespace App\Models\Pengajuan;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Approval;
use App\Models\Globals\MenuFlow;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use App\Models\Model;
use App\Models\Globals\Activity;
use App\Models\Perbaikan\TransPerbaikanDisposisi;
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
        'check_up_result'
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
        // empty(array_intersect(['Keuangan','PPK','Direksi','Sarpras','BPKAD'], $user->roles->pluck('name')->toArray())
        // !in_array($user->position->location->id, [8,17]
        return $query->when(empty(array_intersect(['Keuangan','Direksi','Sarpras','BPKAD'], $user->roles->pluck('name')->toArray())), 
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
        ->filterBy(['departemen_id','repair_results','status'])
        ->when(
            $tahun_usulan = request()->submission_date,
            function ($q) use ($tahun_usulan){
                $q->whereYear('submission_date',$tahun_usulan);
            })
        ->latest();
        
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

            Aset::where('id',$request->kib_id)->update(['status'=>'in repair']);

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
            //dd($request->all());
            $time = Carbon::createFromFormat('d/m/Y', $request->repair_date);
            $this->update(['repair_date' => $time]);
            // dd('tes');
            $this->approveApproval($request->module);

            $this->update(['status' => 'approved']);
        
            $user = auth()->user()->name;
            $this->addLog('Memverifikasi Pengajuan Perbaikan Aset :'. $this->code. ' dengan Tanggal Pemanggilan Perbaikan Aset :'. $this->submission_date);

            $this->addNotify([
                'message' => 'Melakukan Verifikasi Pengajuan Perbaikan Aset ',$this->code,
                'url' => route('perbaikan.perbaikan-aset.show',$this->id),
                'user_ids' => [$this->created_by],
            ]);
            

            $pesan = ($user.' Melakukan Verifikasi Permintaan Perbaikan Aset '.$this->code);
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
            if($request->repair_results != null && $this->is_disposisi == 'yes'){
                $flag_awal = TransPerbaikanDisposisi::where('perbaikan_id',$this->id)->count();
                if($flag_awal > 0 ){
                    // dd('tes');
                    $flagco = TransPerbaikanDisposisi::where('perbaikan_id', $this->id)
                    ->where('sper_status','completed')
                    ->count();

                    $flagnc = TransPerbaikanDisposisi::where('perbaikan_id', $this->id)
                    ->whereIn('sper_status', ['new', 'draft', 'waiting.approval'])
                    ->count();

                    $flagre = TransPerbaikanDisposisi::where('perbaikan_id', $this->id)
                    ->where('sper_status', 'rejected')
                    ->count();

                    $flag_completed = TransPerbaikanDisposisi::where('perbaikan_id', $this->id)
                        ->where('status', ['completed'])
                        ->count();
                    
                    $flag_total = $flagco + $flagnc;
                    if($flag_completed != $flag_total){

                        return $this->rollback(
                            [
                                'message' => 'Masih Terdapat Transaksi Sperpat Yang Belum Diselesaikan, Silahkan Lengkapi Data Transaksi Sperpat!'
                            ]
                        );
                        
                    }
                }
            }

            $this->fill($data);
            $this->save();
            $this->petugas()->sync($request->user_id ?? []);

            $user = auth()->user()->name;

            if($request->repair_results != null){
                if($request->repair_results == 'SELESAI'){
                    Aset::where('id',$request->kib_id)->update(['condition'=>'baik']);
                    Aset::where('id',$request->kib_id)->update(['status'=>'actives']);
                }elseif($request->repair_results == 'ALAT TIDAK BISA DIGUNAKAN'){
                    Aset::where('id',$request->kib_id)->update(['condition'=>'rusak berat']);
                    Aset::where('id',$request->kib_id)->update(['status'=>'actives']);
                }
            }

            if($this->check_up_results == null && $request->check_up_results != null){
                $this->addLog('Update Hasil Pemeriksaan Aset'.$this->code);
                $this->addNotify([
                    'message' => 'Melakukan Update Hasil Pemeriksaan Aset ',$this->code,
                    'url' => route('perbaikan.perbaikan-aset.show',$this->id),
                    'user_ids' => [$this->created_by],
                ]);

                $pesan = ($user.' Melakukan Update Hasil Pemeriksaan Aset ' . $this->code);
                $this->sendNotification($pesan);
            }

            if($request->repair_results != null){
                // dd('tes');
                


                $this->addLog('Update Hasil Perbaikan Aset'.$this->code);
                $this->addNotify([
                    'message' => 'Melakukan Update Hasil Perbaikan Aset ',$this->code,
                    'url' => route('perbaikan.perbaikan-aset.show',$this->id),
                    'user_ids' => [$this->created_by],
                ]);

                $pesan = ($user.' Melakukan Update Hasil Perbaikan Aset ' . $this->code);
                $this->sendNotification($pesan);

                $data = Activity::where('module', 'pj-perbaikan-aset')
                    ->where('target_id', $this->id)
                    ->first();

                if ($data) {
                    $data->update(['module' => 'perbaikan-aset']);
                }
            }


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
            Aset::where('id',$this->kib_id)->update(['status'=>'actives']);
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
            Aset::where('id',$request->kib_id)->update(['status'=>'in repair']);
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
        $data =  $this->code;
        $routes = request()->get('routes');
        switch (request()->route()->getName()) {
            case $routes . '.store':
                $this->addLog('Membuat Pengajuan Perbaikan Aset : '. $data);
                $pesan = $user.' Membuat Pengajuan Perbaikan Aset :' . $data;
                $this->sendNotification($pesan);
                if (request()->is_submit) {
                    $this->addLog('Submit Pengajuan Perbaikan Aset :' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Verification Pengajuan Perbaikan Aset :' . $data,
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->struct),
                    ]);
                    $pesan = $user.' Menunggu Verifikasi Pengajuan Perbaikan Aset :' . $data;
                    $this->sendNotification($pesan);
                }
                break;
            case $routes . '.update':
                $this->addLog('Mengubah Pengajuan Perbaikan Aset :' . $data);
                if($this->status == 'waiting.verify'){

                        $this->addNotify([
                            'message' => 'Memverifikasi Pengajuan Perbaikan Aset :' . $data,
                            'url' => route($routes . '.show', $this->id),
                            'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->departemen_id),
                        ]);
                        $pesan = $user.' Memverifikasi Pengajuan Perbaikan Aset : ' . $data;
                        $this->sendNotification($pesan);
                    }
                    if($this->status == 'verify'){
                    
                        $this->addNotify([
                            'message' => 'Melakukan Update Hasil Perbaikan Aset ' . $data,
                            'url' => route($routes . '.show', $this->id),
                            'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->departemen_id),
                        ]);
                        $pesan = $user.' Melakukan Update Hasil Perbaikan Aset ' . $data;
                        $this->sendNotification($pesan);
                    }

                
                break;
            case $routes . '.destroy':
                $this->addLog('Menghapus Pengajuan Perbaikan Aset :' . $data);
                break;
            case $routes . '.verify':
                $this->addNotify([
                    'message' => 'Melakukan Verifikasi Pengajuan Perbaikan Aset :' . $data,
                    'url' => route($routes . '.show', $this->id),
                    'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                ]);
                $pesan = $user.' Melakukan Verifikasi Pengajuan Perbaikan :' . $data;
                $this->sendNotification($pesan);

                break;
            case $routes . '.approve':
                if (in_array($this->status, ['draft', 'waiting.approval.revisi'])) {
                    $this->addLog('Menyetujui Revisi Pengajuan Perbaikan Aset :' . $data);

                    $this->addNotify([
                        'message' => 'Waiting Approval Revisi Pengajuan Perbaikan Aset :' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);

                    $pesan = $user. ' Waiting Approval Revisi Pengajuan Perbaikan Aset :' . $data;
                    $this->sendNotification($pesan);

                } else {
                    $this->addLog('Menyetujui Pengajuan Perbaikan Aset :' . $data);

                    $this->addNotify([
                        'message' => 'Waiting Approval Pengajuan Perbaikan Aset :' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);
                    $pesan = $user. ' Menyetujui Pengajuan Perbaikan Aset :' . $data;
                    $this->sendNotification($pesan);
                }
                break;
            case $routes . '.reject':
                if (in_array($this->status, ['rejected'])) {
                    $this->addLog('Menolak Pengajuan Perbaikan Aset : ' . $data . ' dengan alasan: ' . request()->get('note'));

                    $this->addNotify([
                        'message' => 'Menolak Pengajuan Perbaikan Aset :' . $data . ' dengan alasan: ' . request()->get('note'),
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => [$this->created_by],
                    ]);

                    $pesan = $user .' Menolak Pengajuan Pengajuan Perbaikan Aset :' . $data;
                    $this->sendNotification($pesan);
                } else {
                    $this->addLog('Menolak Revisi Pengajuan Perbaikan Aset :' . $data . ' dengan alasan: ' . request()->get('note'));

                    $this->addNotify([
                        'message' => 'Menolak Revisi Pengajuan Perbaikan Aset :' . $data . ' dengan alasan: ' . request()->get('note'),
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => [$this->created_by],
                    ]);
                }
                break;
            case $routes . '.revisi':
                $this->addLog('Revisi Pengajuan Perbaikan Aset :' . $data);
                $this->addNotify([
                    'message' => 'Waiting Approval Revisi Pengajuan Perbaikan Aset :' . $data,
                    'url' => route($routes . '.approval', $this->id),
                    'user_ids' => $this->getNewUserIdsApproval(request()->get('module') . "_upgrade"),
                ]);
                break;
        }
    }

    public function sendNotification($pesan)
    {
        $chatId = '-4136008848'; // Ganti dengan chat ID penerima notifikasi

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
