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
use App\Models\Pengajuan\PerencanaanDetail;
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

    public function sper()
    {
        return $this->hasMany(TransPerbaikanDisposisi::class, 'perbaikan_id');
        // return $this->belongsTo(Aset::class, 'kib_id');
    }

    /*******************************
     ** SCOPE
     *******************************/

    public function scopeGrid($query)
    {
        $user = auth()->user();
        // empty(array_intersect(['Keuangan','PPK','Direksi','Sarpras','BPKAD'], $user->roles->pluck('name')->toArray())
        // !in_array($user->position->location->id, [8,17]
        return $query->when(empty(array_intersect(['Keuangan','Direksi','BPKAD'], $user->roles->pluck('name')->toArray())), 
        function ($q) use ($user) { 
            return $q->when($user->position->imKepalaDeparetemen(), 
                function ($qq) use ($user) {
                    return $qq->whereIn('departemen_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
                },
                function ($qq) use ($user) {
                    return $qq->where('departemen_id', $user->position->location->id); 
                }
            )->when(auth()->user()->roles->pluck('id')->contains(6), function ($query) {
                $query->orWhereHas('approvals', function ($q) {
                $q->where('order', 1)->where('status', 'approved');
                });
            });
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

            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');

            Aset::where('id',$request->kib_id)->update(['condition'=>'rusak sedang']);
            Aset::where('id',$request->kib_id)->update(['status'=>'in repair']);

            if($request->is_submit == 1 ){
                $module ='perbaikan-aset';
                $this->update(['status'=>'waiting.verify']);
                $this->generateApproval($module);
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

            $d= $this->whereHas('approvals', function ($q) {
                $q->where('target_id',$this->id)->where('status','!=','approved')->where('role_id',5);
            })->count();

            dd($request->all());

            if($request->submission_date == null && $d == 0){
                return $this->rollback(
                    [
                        'message' => 'Tanggal Pemanggilan Wajib Diisi !'
                    ]
                );
            }elseif($request->submission_date != null && $d == 0){
                $time = Carbon::createFromFormat('d/m/Y', $request->repair_date);
                $this->update(['repair_date' => $time]);
                $this->update(['status' => 'approved']);
            }else{
                $this->update(['status' => 'waiting.verify']);
            }

            $this->handleApprove($request);
            $user = auth()->user()->name;
            if($request->submission_date != null){
                $this->addLog('Memverifikasi Pengajuan Perbaikan Aset :'. $this->code. ' dengan Tanggal Pemanggilan Perbaikan Aset :'. $this->submission_date);
            }else{
                $this->addLog($user.' Memverifikasi Pengajuan Perbaikan Aset :'. $this->code);
            }
            
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

    public function handleApprove($request)
    {
        $this->beginTransaction();
        try {
        
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

                    // dd($request->all());
                    if ($this->firstNewApproval($request->module)) {
                        $this->update(['status' => 'waiting.verify']);
                    } else {
                        if($request->repair_date == null){
                            return $this->rollback(
                                [
                                    'message' => 'Tanggal Pemanggilan Wajib Diisi !'
                                ]
                            );
                        }elseif($request->repair_date != null){
                            $time = Carbon::createFromFormat('d/m/Y', $request->repair_date);
                            $this->update(['repair_date' => $time]);
                            $this->update(['status' => 'approved']);
                        }else{
                            $this->update(['status' => 'waiting.verify']);
                        }
                    }
                }

                $user = auth()->user()->name;
                if($request->submission_date != null){
                    $this->addLog('Memverifikasi Pengajuan Perbaikan Aset :'. $this->code. ' dengan Tanggal Pemanggilan Perbaikan Aset :'. $this->submission_date);
                }else{
                    $this->addLog($user.' Memverifikasi Pengajuan Perbaikan Aset :'. $this->code);
                }
                
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
            $customMessage = 'This is a custom error message';
            return $this->rollback($customMessage);
          //  return $this->rollbackSaved($e);
            
        }
    }

    public function handleStoreOrUpdate($request){
        $this->beginTransaction();
        try {
            $data = $request->all();

            if ($request->repair_results != null && $this->is_disposisi == 'yes') {
                $flag_awal = TransPerbaikanDisposisi::where('perbaikan_id', $this->id)->count();
            
                if ($flag_awal > 0) {
                    $flagco = TransPerbaikanDisposisi::where('perbaikan_id', $this->id)
                        ->where('sper_status', 'completed')
                        ->count();
            
                    $flagnc = TransPerbaikanDisposisi::where('perbaikan_id', $this->id)
                        ->whereIn('sper_status', ['new', 'draft', 'waiting.approval'])
                        ->count();
        
                    $flag_completed = TransPerbaikanDisposisi::where('perbaikan_id', $this->id)
                        ->where('status', 'completed')
                        ->count();
            
                    $flag_total = $flagco + $flagnc;
                    if ($flag_completed != $flag_total) {
                        return $this->rollback([
                            'message' => 'Masih Terdapat Transaksi Sperpat Yang Belum Diselesaikan, Silahkan Lengkapi Data Transaksi Sperpat!'
                        ]);
                    }
                }else{
                    return $this->rollback([
                        'message' => 'Pengajuan Sperpat Belum Dibuat dan Perbaikan Ini Memerlukan Pengajuan Sparepat Aset'
                    ]);
                }
            }
            
            if($request->is_submit == 1 && $this->status == 'draft' || $request->is_submit == 1 && $this->status == 'rejected' ){
                $this->handleSubmitSave($request);
            }else{
                if($this->status=='rejected'){
                    $this->update(['status'=>'draft']);
                }
            }
            
            $this->fill($data);
            $this->save();

        
            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');

            if($request->user_id !=null){
                $this->petugas()->sync($request->user_id ?? []);
            }

            $user = auth()->user()->name;

            if($this->status == 'draft'  && $this->check_up_results == null || $this->status == 'rejected'  && $this->check_up_results == null){
                $this->addLog('Update Usulan Perbaikan Aset '.$this->code);
                $this->addNotify([
                    'message' => 'Melakukan Update Usulan Perbaikan Aset ',$this->code,
                    'url' => route('perbaikan.perbaikan-aset.show',$this->id),
                    'user_ids' => [$this->created_by],
                ]);

                $pesan = ($user.' Melakukan Update Usulan Perbaikan Aset ' . $this->code);
                $this->sendNotification($pesan);
            }

            if($this->status == 'waiting.verify' && $this->check_up_results == null){
                $this->addLog('Menunggu Verifikasi Usulan Perbaikan Aset '.$this->code);
                $this->addNotify([
                    'message' => 'Menunggu Verifikasi Usulan Perbaikan Aset ',$this->code,
                    'url' => route('perbaikan.perbaikan-aset.show',$this->id),
                    'user_ids' => [$this->created_by],
                ]);

                $pesan = ($user.' Menunggu Verifikasi Usulan Perbaikan Aset ' . $this->code);
                $this->sendNotification($pesan);
            }

            if($request->repair_results != null){
                if($request->repair_results == 'SELESAI'){
                    Aset::where('id',$request->kib_id)->update(['condition'=>'baik']);
                    Aset::where('id',$request->kib_id)->update(['status'=>'actives']);
                }elseif($request->repair_results == 'ALAT TIDAK BISA DIGUNAKAN'){
                    Aset::where('id',$request->kib_id)->update(['condition'=>'rusak berat']);
                    Aset::where('id',$request->kib_id)->update(['status'=>'actives']);
                }

                //$perbaikan = Perbaikan::where('repair_results','SELESAI')->where('action_repair','<>',null)->value('kib_id');
                if($request->repair_results == 'SELESAI'){
                    $aset = Aset::where('id',$this->kib_id)->where('type','KIB F')->value('usulan_id');
                    // PerencanaanDetail::where('id',$aset)->update(['status'=>'waiting register']);
                    Aset::where('usulan_id',$aset)->where('type','KIB F')->update(['status' => 'notactive']);
                }
            }

            if($request->repair_results != null){
                $this->addLog('Update Hasil Perbaikan Aset '.$this->code);
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

            if($aset){
                // update data kib f menjadi kib c
                $this->handleStoreOrUpdateKibC($request);
                $redirect = route(request()->get('routes') . '.index');
                return $this->commitSaved(compact('redirect'));

            }else{
                $redirect = route(request()->get('routes') . '.index');
                return $this->commitSaved(compact('redirect'));
            }

        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }



    public function handleStoreOrUpdateKibC($request){
        $flagt = Aset::where('usulan_id',$request->usulan_id)->where('type','KIB F')->value('book_value');
        if($flagt){
    
            $data = $request->all();
    
            $value6 = str_replace(['.', ','],'',$request->wide);
            $wide = (int)$value6;
    
            if($request->type == 'KIB C'){
                $value7 = str_replace(['.', ','],'',$request->wide_bld);
                $wide_bld = (int)$value7;
            }
    
            $sertif_date = Carbon::createFromFormat('d/m/Y', $request->sertificate_date);
    
            $value8 = str_replace(['.', ','],'',$request->residual_value);
            $residu = (int)$value8;
    
            $value9 = str_replace(['.', ','],'',$request->unit_cost);
            $cost = (int)$value9;

            $asetData = new Aset;
            $asetData->fill($data);
            $asetData->type ='KIB C';
            $asetData->land_status = strtolower($request->land_status);
            // $this->land_use = strtolower($request->land_use);
            $asetData->residual_value= $residu;
            $asetData->sertificate_date = $sertif_date;
            $asetData->wide= $wide;
    
            if($request->type == 'KIB C'){
                $asetData->wide_bld= $wide_bld;
            }
    
            $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
            $asetData->no_register = $no_inventaris + 1;
            $asetData->accumulated_depreciation = 0;
            $asetData->book_value = $cost + $flagt;
            $asetData->acq_value = $cost + $flagt;
            $asetData->status = 'actives';
            $asetData->save();

            $data = Activity::where('module', 'pj-perbaikan-aset')
                    ->where('target_id', $this->id)
                    ->first();

                if ($data) {
                    $data->update(['module' => 'inventaris']);
                }

            Aset::where('usulan_id',$request->usulan_id)->where('type','KIB F')->update(['status' => 'notactive']);
            
        }
    }


    public function handleCheckUp($request){
        $this->beginTransaction();
        try {
            $data = $request->all();
            
            if($request->check_up_result == null){
                return $this->rollback(
                    [
                        'message' => 'Hasil Pemeriksaan Awal Aset Harus Diisi!'
                    ]
                );
            }
                
            if($request->is_disposisi == null){
                return $this->rollback(
                    [
                        'message' => 'Status Pengajuan Disposisi Sperpat Aset Harus Diisi'
                    ]
                );
            }


            $this->update(['is_disposisi' => $request->is_disposisi]);
            $this->update(['check_up_result' => $request->check_up_result]);
            
            if($this->status == 'approved' && $request->check_up_results != null){
                $this->addLog('Update Hasil Pemeriksaan Aset'.$this->code);
                $this->addNotify([
                'message' => 'Melakukan Update Hasil Pemeriksaan Aset ',$this->code,
                'url' => route('perbaikan.perbaikan-aset.show',$this->id),
                'user_ids' => [$this->created_by],
                ]);

                $pesan = ($user.' Melakukan Update Hasil Pemeriksaan Aset ' . $this->code);
                $this->sendNotification($pesan);
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
            $this->update(['status'=>'waiting.verify']);
            $this->generateApproval($module);
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
            
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }


    public function saveLogNotify()
    {
        $user = auth()->user()->name;
        // dd('hjhjh');
        $data =  $this->code;
        $routes = request()->get('routes');
        //dd(request()->route()->getName());
        switch (request()->route()->getName()) {
            case $routes . '.store':
                $this->addLog('Membuat Pengajuan Perbaikan Aset : '. $data);
                $pesan = $user.' Membuat Pengajuan Perbaikan Aset :' . $data;
                if (request()->is_submit) {
                    $this->addLog('Submit Pengajuan Perbaikan Aset :' . $data);

                    $this->addNotify([
                        'message' => 'Memverifikasi Pengajuan Perbaikan Aset :' . $data,
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);

                    $pesan = $user.' Membuat Pengajuan Perbaikan Aset dan Menunggu Verifikasi Pengajuan Perbaikan Aset :' . $data;
                    $this->sendNotification($pesan);
                }else{
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

                    $pesan = $user .' Menolak Pengajuan Pengajuan Perbaikan Aset :' . $data .' dengan alasan: ' . request()->get('note');
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

    public function sendNotification($pesan)
    {

        $approval1 = $this->whereHas('approvals', function ($q) {
            $q->where('target_id',$this->id)->where('status','!=','approved')->where('role_id',5);
        })->count();

        $parent = OrgStruct::where('id', $this->departemen_id)->value('parent_id');
        $chat_grup = OrgStruct::where('id', $this->departemen_id)->value('telegram_id');
        $chat_ipsrs = OrgStruct::where('name', 'IPSRS')->value('telegram_id');
        $chat_departemen = OrgStruct::where('id', $parent)->value('telegram_id');
        $chatId = '-4136008848'; //grup notif perbaikan

        $send_chat = [];
        if ($this->status == 'draft') {
            $send_chat = array_filter([$chatId, $chat_grup]);
        } elseif ($this->status == 'waiting.verify' && $approval1 > 0 ) { //verify tahap 1
            $send_chat = array_filter([$chatId, $chat_grup, $chat_departemen]);
            $pesan = $pesan.' '.' dan Kepada Departemen Unit Mohon Untuk Melakukan Approval';
        }elseif($this->status == 'rejected' && $approval1 > 0){ //rejected oleh departemen
            $send_chat = array_filter([$chatId, $chat_grup]); //ditolak departemen
        } elseif ($this->status == 'waiting.verify' && $approval1 == 0) { //verify tahap 2
            $send_chat = array_filter([$chatId, $chat_grup, $chat_ipsrs]);
            $pesan = $pesan.' '.' dan Kepada Unit IPSRS Mohon Untuk Segera Melakukan Approval dan Melakukan Pemeriksaan Pada Aset';
        }elseif($this->status == 'rejected' && $approval1 == 0){ //rejected ipsrs
            $send_chat = array_filter([$chatId, $chat_grup]);
        } else if($this->status == 'approved' && $this->repair_results != 'BELUM'){
            $send_chat = array_filter([$chatId, $chat_grup,$chat_ipsrs]);
            $pesan = $pesan.' '.'dengan hasil perbaikan'.$this->repair_results;
        }else{
            $send_chat = array_filter([$chatId, $chat_grup,$chat_ipsrs]);
        }
        
        // Kirim pesan ke setiap chat ID
        foreach ($send_chat as $chat_id) {
            Telegram::sendMessage([
                'chat_id' => $chat_id,
                'text' => $pesan,
            ]);
        }
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


    public function getBookValueUtility($aset)
    {
        $min = 100000;
        $max = 10000000;
        //$min = Aset::where('type',$aset->type)->min('book_value'); // nilai minimum yang bisa diatur sesuai kebutuhan
        //$max = Aset::where('type',$aset->type)->max('book_value'); // nilai maksimum yang bisa diatur sesuai kebutuhan
        return ($aset->book_value - $min) / ($max - $min);
    }

    public function getConditionUtility($aset)
    {
        $conditions = ['rusak berat' => 1, 'rusak sedang' => 0.8, 'baik' => 0];
        return $conditions[$aset->condition] ?? 0;
    }

    public function getAgeUtility($aset)
    {
        $min = 1; // usia minimum yang bisa diatur sesuai kebutuhan
        $max = 7; // usia maksimum yang bisa diatur sesuai kebutuhan
        $age = date_diff(date_create($aset->book_date), date_create(now()))->y;
        return 1 - (1 - $min) / ($max - $min);
    }

    public function getMautScore($aset)
    {
        $weightBookValue = 0.5;
        $weightCondition = 0.2;
        $weightAge = 0.3;

        $bookValueUtility = $this->getBookValueUtility($aset);
        $conditionUtility = $this->getConditionUtility($aset);
        $ageUtility = $this->getAgeUtility($aset);

        return ($weightBookValue * $bookValueUtility) +
            ($weightCondition * $conditionUtility) +
            ($weightAge * $ageUtility);
    }

    ///================================
    


















     //kondisi aset
     private $damageWeights = [
        'rusak sedang' => 2,
        'rusak berat' => 4
    ];
    
    //nilai aset
    private $valueWeights = [
        ['min' => 44000, 'max' => 2022999, 'weight' => 8],
        ['min' => 2023000, 'max' => 4001999, 'weight' => 7],
        ['min' => 4002000, 'max' => 5980999, 'weight' => 6],
        ['min' => 5981000, 'max' => 7959999, 'weight' => 5],
        ['min' => 7960000, 'max' => 9938999, 'weight' => 4],
        ['min' => 9939000, 'max' => 11917999, 'weight' => 3],
        ['min' => 11918000, 'max' => 13896999, 'weight' => 2],
        ['min' => 13897000, 'max' => 15876000, 'weight' => 1]
    ];
    
    //umur aset
    private $economicLifeWeights = [
        ['max' => 2, 'weight' => 5],
        ['min' => 3, 'weight' => 2] // Assume anything greater than 2 years has the weight 2
    ];
    
    public function calculateUtilityScore($aset) {
        $damageWeight = $this->getDamageWeight($aset->condition);
        $valueWeight = $this->getValueWeight($aset->book_value);
        $economicLifeWeight = $this->getEconomicLifeWeight(date_diff(date_create($aset->book_date), date_create(now()))->y);
        
        $min_book = Aset::where('type', $aset->type)->min('book_value');
        $max_book = Aset::where('type', $aset->type)->max('book_value');

        $min = $this->getValueWeight($min_book);
        $max = $this->getValueWeight($max_book);

        $damageWeight = ($damageWeight - 2) / (5 - 2);
        if ($max != $min) {
            $valueWeight = ($valueWeight - $min) / ($max - $min);
        } else {
            $valueWeight = 0; // Contoh, bisa disesuaikan dengan logika aplikasi Anda
        }
        $economicLifeWeight = ($economicLifeWeight - 2) / (5 - 2);

        $aset['utility_score'] = ($damageWeight * 0.2) + ($valueWeight * 0.5) + ($economicLifeWeight * 0.3);
        return $aset;
    }

    public function getDamageWeight($condition) {
        return $this->damageWeights[$condition] ?? 0;
    }

    public function getValueWeight($value) {
        foreach ($this->valueWeights as $range) {
            if ($value >= $range['min'] && $value <= $range['max']) {
                return $range['weight'];
            }
        }
        return 0;
    }

    public function getEconomicLifeWeight($economicLife) {
        foreach ($this->economicLifeWeights as $range) {
            if (!isset($range['min']) && $economicLife <= $range['max']) {
                return $range['weight'];
            }
            if (!isset($range['max']) && $economicLife >= $range['min']) {
                return $range['weight'];
            }
        }
        return 0;
    }
    
    // // Contoh penggunaan
    // $assets = [
    //     ['condition' => 'Rusak Berat', 'value' => 5000000, 'economic_life' => 1],
    //     ['condition' => 'Rusak Ringan', 'value' => 3000000, 'economic_life' => 4],
    //     ['condition' => 'Rusak Berat', 'value' => 8000000, 'economic_life' => 3]
    // ];
    
    // $calculator = new MautCalculator();
    // $assetsWithUtilityScores = $calculator->calculateUtilityScore($assets);
    
    // foreach ($assetsWithUtilityScores as $asset) {
    //     echo "Aset dengan kondisi {$asset['condition']} memiliki skor utility {$asset['utility_score']}\n";
    // }
}
