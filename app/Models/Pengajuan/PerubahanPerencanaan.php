<?php

namespace App\Models\Pengajuan;

use App\Models\Model;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Master\Aset\AsetRs;
use App\Models\Master\Dana\Dana;
use App\Models\Auth\Role;
use App\Models\Globals\Approval;
use App\Models\Globals\MenuFlow;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use App\Models\Traits\ResponseTrait;
use App\Models\Inventaris\Aset;
use App\Models\Traits\HasApprovals;
use App\Models\Traits\HasFiles;
use App\Support\Base;
use App\Models\Transaksi\PembelianTransaksi;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;

class PerubahanPerencanaan extends Model
{

    use HasFiles, HasApprovals;
    protected $table = 'trans_perubahan_usulan';

    protected $fillable = [
        'usulan_id',
        'note',
        'status',
    ];


    /*******************************
     ** MUTATOR
     *******************************/
    public function scopeGrid($query)
    {
        $user = auth()->user();
        //13,55,19
        return $query->when(!in_array($user->position->location->id, [55,19]) &&  !collect(auth()->user()->roles)->contains('name', 'PPK'), 
        function ($q) use ($user) { 
            return $q->where('status','<>','draft')->when($user->position->imKepalaDeparetemen(), 
                function ($qq) use ($user) {
                    return $qq->whereHas('detailUsulan', function ($qqq) use ($user){
                        $qqq->whereHas('perencanaan',function ($qqqq) use ($user){
                            $qqqq->whereIn('struct_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
                        });
                    });
                },

                function ($qq) use ($user) {
                    return $qq->whereHas('detailUsulan', function ($qqq) use ($user){
                        $qqq->whereHas('perencanaan',function ($qqqq) use ($user){
                            $qqqq->where('struct_id', $user->position->location->id); //ambil anak dan kepala departemen
                        });
                    });
                    // return $qq->where('struct_id', $user->position->location->id); 
                }
            );
        })->when(auth()->user()->roles->pluck('id')->contains(3), function ($query) {
            $query->orWhereHas('approvals', function ($q) {
            $q->where('order', 1)->where('status','approved');
            });
        })->when(auth()->user()->roles->pluck('id')->contains(2), function ($query) {
            $query->orWhereHas('approvals', function ($q) {
            $q->where('order', 2)->where('status','approved');
            });
        });
    }


    public function scopeGridPelayanan($query)
    {
        $user = auth()->user();
        //13,55,19
        return $query->when(!in_array($user->position->location->id, [55,19]) &&  !collect(auth()->user()->roles)->contains('name', 'PPK'), 
        function ($q) use ($user) { 
            return $q->where('status','<>','draft')->when($user->position->imKepalaDeparetemen(), 
                function ($qq) use ($user) {
                    return $qq->whereHas('detailUsulan', function ($qqq) use ($user){
                        $qqq->whereHas('perencanaan',function ($qqqq) use ($user){
                            $qqqq->whereIn('struct_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
                        });
                    });
                },

                function ($qq) use ($user) {
                    return $qq->whereHas('detailUsulan', function ($qqq) use ($user){
                        $qqq->whereHas('perencanaan',function ($qqqq) use ($user){
                            $qqqq->where('struct_id', $user->position->location->id); //ambil anak dan kepala departemen
                        });
                    });
                    // return $qq->where('struct_id', $user->position->location->id); 
                }
            );
        })->when(auth()->user()->roles->pluck('id')->contains(3), function ($query) {
            $query->orWhereHas('approvals', function ($q) {
            $q->where('order', 2)->where('status','approved');
            });
        })->when(auth()->user()->roles->pluck('id')->contains(2), function ($query) {
            $query->orWhereHas('approvals', function ($q) {
            $q->where('order', 3)->where('status','approved');
            });
        })->when(auth()->user()->position->location->name == 'Bidang Penunjang Medik dan Non Medik', function ($query) {
            $query->orWhereHas('approvals', function ($q) {
            $q->where('order', 1)->where('status','approved');
            });
        });
        
    }

    /*******************************
     ** RELATION
     *******************************/

    public function detailUsulan()
    {
        return $this->belongsTo(PerencanaanDetail::class, 'usulan_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


/*******************************
 ** SCOPE
*******************************/

    // public function scopeGrid($query)
    // {
    //     return $query;
    // }

    public function scopeFilters($query)
    {
        return $query->filterBy(['usulan_id'])
            ->when(
                $struct = request()->struct_id,
                function ($q) use ($struct){
                    $q->whereHas('detailUsulan', function ($qq) use ( $struct){
                        $qq->whereHas('perencanaan', function($qqq) use ($struct){
                            $qqq->whereHas('struct', function($qqqq) use ($struct){
                                $qqqq->where('id', $struct);
                            });
                        });
                    });
                })
            ->latest();
    }


    public function handleStore($request,$statusOnly = false){
        $this->beginTransaction();
        try {
            if($request->note == null){
                return $this->rollback(
                    [
                        'message' => 'Keterangan Perubahan Usulan  Wajib Diisi!'
                    ]
                );
            }

            $data = $request->all();
            $this->fill($data);
            $time = now()->format('Y-m-d');
            $this->update_date =  $time;
            $this->save();

            PerencanaanDetail::where('id',$request->usulan_id)->update(['status'=>'on update']);

            if($request->is_submit == 1 ){
                $this->handleSubmitSave($request);
            }else{
                $this->saveLogNotify();
            }

            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleStoreOrUpdateHarga($request){
        $this->beginTransaction();
        try {
        
            if($request->pagu_unit == null || $request->pagu_unit == 0){
                return $this->rollback(
                    [
                        'message' => 'Harga Unit Aset Wajib Diisi'
                    ]
                );
            }

            $value1 = str_replace(['.', ','],'',$request->pagu_unit);
            $value1 = (int)$value1;
            $qtys = PerencanaanDetail::where('id', $this->usulan_id)->value('qty_agree');
            $total_cost = $qtys * $value1;

            // PerencanaanDetail::where('id',$this->usulan_id)->update(['desc_spesification'=>$request->spesifikasi]);
            PerencanaanDetail::where('id',$this->usulan_id)->update(['HPS_unit_cost'=>$value1]);
            PerencanaanDetail::where('id',$this->usulan_id)->update(['HPS_total_cost'=>$total_cost]);
            
            $this->saveLogNotify();


            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }


    public function handleStoreOrUpdate($request, $statusOnly = false)
    {
        $this->beginTransaction();
        try {
            
            if($request->perencanaan_id == null){
                return $this->rollback(
                    [
                        'message' => 'Nomor Surat Wajib Diisi!'
                    ]
                );
            }

            if($request->usulan_id == null){
                return $this->rollback(
                    [
                        'message' => 'Nama Aset Wajib Diisi!'
                    ]
                );
            }

            if($request->note == null){
                return $this->rollback(
                    [
                        'message' => 'Keterangan Perubahan Usulan Wajib Diisi!'
                    ]
                );
            }

            if($request->usulan_id != $this->usulan_id && $request->usulan_id != null){
                // dd('usulan_lama',$this->usulan_id, 'dan usulan baru',$request->usulan_id);
                PerencanaanDetail::where('id',$this->usulan_id)->update(['status'=>'waiting purchase']);
            }
        
            $data = $request->all();
            $this->fill($data);
            $this->save();

            if($request->usulan_id != null){
                PerencanaanDetail::where('id',$request->usulan_id)->update(['status'=>'on update']);
            }
            

            if($request->is_submit == 1 ){
                $this->handleSubmit($request);
                // $this->handleSubmitSave($request);
            }else{
                $this->saveLogNotify();
            }

            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }


    public function handleStoreOrUpdateSpesifikasi($request, $statusOnly = false){
        $this->beginTransaction();
        try {
            
            if($request->spesifikasi == null){
                return $this->rollback(
                    [
                        'message' => 'Spesifikasi Aset Wajib Diisi!'
                    ]
                );
            }

            if($request->pagu_unit == null || $request->pagu_unit <= 0){
                return $this->rollback(
                    [
                        'message' => 'Harga Unit Harus Diisi'
                    ]
                );
            }

           // dd($request->all());
            $value1 = str_replace(['.', ','],'',$request->pagu_unit);
            $value1 = (int)$value1;
            $qtys = PerencanaanDetail::where('id', $request->usulan_id)->value('qty_agree');
            $total_cost = $qtys * $value1;

            PerencanaanDetail::where('id',$this->usulan_id)->update(['desc_spesification'=>$request->spesifikasi]);
            PerencanaanDetail::where('id',$this->usulan_id)->update(['HPS_unit_cost'=>$value1]);
            PerencanaanDetail::where('id',$this->usulan_id)->update(['HPS_total_cost'=>$total_cost]);

            $this->handleSubmitSave($request);

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
        //    dd($this->usulan_id);
            PerencanaanDetail::where('id',$this->usulan_id)->update(['status'=>'waiting purchase']);
            $this->delete();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }

    public function handleSubmit($request)
    {
        $this->beginTransaction();
        try {
            $this->update(['status' => 'waiting.update']);
            // $this->generateApproval($request->module);
            $this->saveLogNotify();
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));

        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }


    public function handleSubmitSave($request)
    {
        $this->beginTransaction();
        try {
            $this->update(['status' => 'waiting.approval']);
            $this->generateApproval($request->module);
            $this->saveLogNotify();
            $module = request()->get('module');
            $user = auth()->user()->name;
            $data = 'Pengajuan Perubahan Perencanaan Aset dengan No Surat : ' . $this->detailUsulan->perencanaan->code;
            // dd($module, $this->status);

            if($module == 'perubahan-perencanaan' && $this->status == 'waiting.approval'){
                $pesan = $user.' Menunggu Approval ' . $data;
                $this->sendNotification($pesan);
            }else{
                $pesan = $user.' Menunggu Approval ' . $data;
                $this->sendNotification($pesan);
            }

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
                        $this->update(['status' => 'completed']);
                        PerencanaanDetail::where('id',$this->usulan_id)->update(['status'=>'waiting purchase']);
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
        $data = 'Pengajuan Perubahan Perencanaan Aset dengan No Surat : ' . $this->detailUsulan->perencanaan->code;

        $routes = request()->get('routes');
        //dd(request()->is_submit);
      //  dd(request()->route()->getName());
        switch (request()->route()->getName()) {
            case $routes . '.store':
                // dd('au');
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
                }elseif(request()->is_submit == 2){
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Approval ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);
                    // dd('ah');
                    $pesan = $user.' Menunggu Approval ' . $data;
                    $this->sendNotification($pesan);
                }
                break;
            case $routes . '.update':
                // dd('as');
                $this->addLog('Mengubah ' . $data);
                if (request()->is_submit == 1) {
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Update Spesifikasi ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);
                    // dd('hsj');
                    $pesan = $user.' Menunggu Update Spesifikasi ' . $data;
                    $this->sendNotification($pesan);
                }elseif(request()->is_submit == 2){
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Approval ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);
                    // dd('ah');
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

        $module = request()->get('module');
        // if($module == 'perubahan-usulan-umum'){
        $approval1_u = $this->whereHas('approvals', function ($q)  use ($module){
            $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('role_id',5)->where('order',1);
        })->count();

        $approval2_u = $this->whereHas('approvals', function ($q) use ($module){
            $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('role_id',5)->where('order',2);
        })->count();

        $approval3_u = $this->whereHas('approvals', function ($q) use ($module){
            $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('role_id',3)->where('order',3);
        })->count();
        // }else{
        //perencanaan direksi
        $approval1 = $this->whereHas('approvals', function ($q) use ($module) {
            $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('role_id',5)->where('order',1);
        })->count();

        $approval2 = $this->whereHas('approvals', function ($q) use ($module) {
            $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('role_id',3)->where('order',2);
        })->count();
        // }

        $struct1 = PerencanaanDetail::where('id',$this->usulan_id)->value('perencanaan_id');
        $struct = Perencanaan::where('id',$struct1)->value('struct_id');

        $parent = OrgStruct::where('id', $struct)->value('parent_id');
        $chat_grup = OrgStruct::where('id', $struct)->value('telegram_id');
        $chat_perencanaan = OrgStruct::where('name', 'Sub Bagian Program Perencanaan dan Pelaporan')->value('telegram_id');
        $chat_direksi = OrgStruct::where('name', 'Direksi RSUD')->value('telegram_id');
        $chat_departemen = OrgStruct::where('id', $parent)->value('telegram_id');
        $penunjang = OrgStruct::where('name', 'Bidang Penunjang Medik dan Non Medik')->value('telegram_id');
        $chatId = '-4054507555'; //grup notif perencanaan
        //$chat_id = null;
        $tes = '1196496344'; // id chat prema
        

        $send_chat = [];
        if ($this->status == 'draft') {
            $send_chat = array_filter([$chat_grup]);
        }elseif ($this->status == 'waiting.approval' && $approval1 > 0 && $module == 'perubahan-perencanaan') { //verify tahap 1
            $send_chat = array_filter([$chat_grup, $penunjang]);
            $pesan = $pesan.' '.' dan Kepada Departemen Penunjang Mohon Untuk Melakukan Approval Dokumen Usulan';
        }elseif ($this->status == 'waiting.approval' && $approval1_u > 0 && $module == 'perubahan-usulan-umum') { //verify tahap 1 umum
            $send_chat = array_filter([$chat_grup, $chat_departemen]);
            $pesan = $pesan.' '.' dan Kepada Departemen Unit Mohon Untuk Melakukan Approval Dokumen Usulan';
        } elseif ($this->status == 'waiting.approval' && $approval1_u == 0 && $approval2_u > 0 && $module == 'perubahan-usulan-umum') { //verify tahap 2 umum
            $send_chat = array_filter([$chat_grup, $penunjang]);
            $pesan = $pesan.' '.' dan Kepada Departemen Penunjang Mohon Untuk Melakukan Approval Dokumen Usulan';
        } elseif ($this->status == 'waiting.approval' && $approval1 == 0 && $approval2 > 0 && $module == 'perubahan-perencanaan') { //verify tahap 2
            $send_chat = array_filter([$chat_grup, $chat_perencanaan]);
            $pesan = $pesan.' '.' dan Kepada Unit Perencanaan Mohon Untuk Melakukan Approval Dokumen Usulan';
        } elseif ($this->status == 'waiting.approval' && $approval1_u == 0 && $approval2_u == 0 && $approval3_u > 0 && $module == 'perubahan-usulan-umum') { //verify tahap 2 umum
            $send_chat = array_filter([$chat_grup, $chat_perencanaan]);
            $pesan = $pesan.' '.' dan Kepada Unit Perencanaan Mohon Untuk Melakukan Approval Dokumen Usulan';
        } elseif ($this->status == 'waiting.approval' && $approval1_u == 0 && $approval2_u == 0 && $approval3_u == 0 && $module == 'perubahan-usulan-umum') { //verify tahap 2 umum
            $send_chat = array_filter([$chat_grup, $chat_direksi]);
            $pesan = $pesan.' '.' dan Kepada Direktur Mohon Untuk Melakukan Approval Dokumen Usulan';
        } elseif ($this->status == 'waiting.approval' && $approval1 == 0 && $approval2 == 0 && $module == 'perubahan-perencanaan') { //verify tahap 2
            $send_chat = array_filter([$chat_grup, $chat_direksi]);
            $pesan = $pesan.' '.' dan Kepada Direktur Mohon Untuk Melakukan Approval Dokumen Usulan';
        }elseif($this->status == 'rejected' && $module == 'perubahan-usulan-umum' || $this->status == 'rejected' && $module == 'perubahan-perencanaan'){ //rejected ipsrs
            $send_chat = array_filter([$chat_grup]);
        } else {
            $send_chat = array_filter([$chat_grup]);
        }

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

    /*******************************
     ** SAVING
     *******************************/

    //non pengadaan
   

}
