<?php

namespace App\Models\Perbaikan;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Approval;
use App\Models\Globals\MenuFlow;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use App\Models\Master\Vendor\Vendor;
use App\Models\Model;
use App\Models\Master\Dana\Dana;
use App\Models\Pengajuan\Perbaikan;
use App\Models\Perbaikan\PerbaikanDisposisiDetail;
use App\Models\Master\Aset\AsetRs;
use App\Models\Traits\ResponseTrait;
use App\Models\Inventaris\Aset;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Traits\HasApprovals;
use App\Models\Traits\HasFiles;
use App\Support\Base;
use Telegram\Bot\Laravel\Facades\Telegram;
use Carbon\Carbon;

class TransPerbaikanDisposisi extends Model
{
    use HasFiles, HasApprovals;

    protected $table = 'trans_sperpat';

    protected $fillable = [
        'perbaikan_id',
        'repair_type',
        'vendor_id',
        'spk_start_date',
        'spk_end_date',
        'spk_range_time',
        'no_spk',
        'shiping_cost',
        'tax_cost',
        'total_cost',
        'sp2d_code',
        'source_fund_id',
        'sp2d_date',
        'sper_status',
        'status',
        'receipt_date',
        'faktur_code',
    ];

    protected $casts = [
        'spk_start_date' => 'date',
        'spk_end_date' => 'date',
        'receipt_date' => 'date',
        // 'sp2d_date' => 'date',
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

    public function codes()
    {
        return $this->belongsTo(Perbaikan::class, 'perbaikan_id');
    }

    public function vendors()
    {
        return $this->belongsTo(Vendor::class,'vendor_id');
    }

    public function details()
    {
        return $this->hasMany(UsulanSperpat::class, 'trans_perbaikan_id');
    }

    public function danad()
    {
        return $this->belongsTo(Dana::class, 'source_fund_id');
    }


    // public function danad()
    // {
    //     return $this->hasMany(UsulanSperpat::class, 'trans_perbaikan_id');
    // }

    /*******************************
     ** SCOPE
     *******************************/

    public function scopeGrid($query)
    {
        $user = auth()->user();
        // return $query->when(empty(array_intersect(['Sarpras','BPKAD'], $user->roles->pluck('name')->toArray()))
        // )->when(auth()->user()->roles->pluck('id')->contains(4), function ($query) {
        //     $query->orWhereHas('approvals', function ($q) {
        //         $q->where('order', 1)->whereIn('status', ['new','rejected']);
        //     });
        // })
        // ->when(auth()->user()->roles->pluck('id')->contains(2), function ($query) {
        //     $query->whereHas('approvals', function ($subQuery) {
        //         $subQuery->where('module','trans-sperpat')->where('order', 1)->where('status', 'approved');
        //     })
        //     ->whereHas('approvals', function ($subQuery) {
        //         $subQuery->where('module','trans-sperpat')->where('order', 2)->where('status', 'new');
        //     });
        // });
        return $query->when(empty(array_intersect(['Sarpras','Keuangan','BPKAD'], $user->roles->pluck('name')->toArray())),
            function ($q) use ($user) { 
                $q->WhereHas('approvals', function ($q) use ($user) {
                    $q->when($user->id, function ($qq) use ($user) {
                        $qq->WhereIn('role_id', $user->getRoleIds())->where('status','new');
                    },function ($qq) use ($user) {
                        $qq->orWhereIn('role_id', $user->getRoleIds())
                        ->orWhere('position_id', $user->position->id);
                    });
                });
            }
        )
        ->latest();


        // return $query->when(empty(array_intersect(['Direksi','Keuangan','Sarpras'], $user->roles->pluck('name')->toArray())),
        //     function ($q) use ($user) { 
        //         $q->WhereHas('approvals', function ($q) use ($user) {
        //             $q->when($user->id, function ($qq) use ($user) {
        //                 $qq->WhereIn('role_id', $user->getRoleIds())->where('status','new');
        //             },function ($qq) use ($user) {
        //                 $qq->orWhereIn('role_id', $user->getRoleIds())
        //                 ->orWhere('position_id', $user->position->id);
        //             });
        //         });
        //     }
        // )
        // ->latest();
        // return $query->when(!in_array($user->position->location->id, [8,17]), 
        // function ($q) use ($user) { 
        //     return $q->when($user->position->imKepalaDeparetemen(), 
        //         function ($qq) use ($user) {
        //             return $qq->whereIn('departemen_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
        //         },
        //         function ($qq) use ($user) {
        //             return $qq->where('departemen_id', $user->position->location->id); 
        //         }
        //     );
        // })->latest();
    }

    public function scopeGridStatusCompleted($query)
    {
        return $query->where('status', 'completed')->latest();
    }

    public function scopeFilters($query)
    {
        return $query
        ->filterBy(['vendor_id','repair_type','status','sper_status'])->when(
            $codes = request()->code,
            function ($q) use ($codes){
                $q->whereHas('codes', function ($qq) use ($codes){
                        $qq->where('code',$codes);
            });
        })->latest();
        
    }

    /*******************************
     ** SAVING
     *******************************/
    public function handleStore($request,$statusOnly = false){
        $this->beginTransaction();
        try {

            $data = $request->all();
            $flag = TransPerbaikanDisposisi::where('perbaikan_id',$request->perbaikan_id)->where('repair_type',$request->repair_type)->where('vendor_id',$request->vendor_id)->count();
            if($flag > 0){
                return $this->rollback(
                    [
                        'message' => 'Usulan Pembelian Sperpat Pada Vendor Ini Sudah Tersedia'
                    ]
                );
            }
            $this->fill($data);
            $this->save();

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
            
            $data = $request->all();
            // dd($data);
            $this->fill($data);

            // dd($request->all());
            if($this->repair_type == 'vendor'){
                if($request->total_cost == null){
                    return $this->rollback(
                        [
                            'message' => 'Biaya Total Sewa Vendor Wajib Diisi!'
                        ]
                    );
                }

                $total = str_replace(['.', ','], '', $request->total_cost);
                $this->total_cost= (int)$total;
            }



            if($this->status == 'completed'){
                // dd('tes');
                $tax = str_replace(['.', ','], '', $request->tax_cost);
                $shiping = str_replace(['.', ','], '', $request->shiping_cost);
                $total = str_replace(['.', ','], '', $request->total_cost);
                
                $spk_start = Carbon::createFromFormat('d/m/Y', $request->spk_start_date);
                $spk_end = Carbon::createFromFormat('d/m/Y', $request->spk_end_date);
                $receipt = Carbon::createFromFormat('d/m/Y', $request->receipt_date);
                $selisih = $spk_start->diffInDays($spk_end);
                
                $this->tax_cost = (int)$tax;
                $this->shiping_cost = (int)$shiping;
                $this->total_cost = (int)$total + (int)$shiping + (int)$tax;
                $this->spk_start_date = $spk_start;
                $this->spk_end_date = $spk_start;
                $this->spk_range_time = $selisih;
                $this->receipt_date = $receipt;

                if($this->repair_type == 'vendor'){
                    $total = str_replace(['.', ','], '', $request->total_cost);
                    $this->total_cost= (int)$total;
                }
            }

            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');

            $this->save();

            if($request->is_submit == 1){
                $data = UsulanSperpat::Where('trans_perbaikan_id',$this->id)->count();
                $type_repair = TransPerbaikanDisposisi::where('id', $this->id)->pluck('repair_type')->first();

            // dd($data,$type_repair);
                if ($data == 0 && $type_repair == 'sperpat') {
                    // Your code here
                    return $this->rollback(
                        [
                            'message' => 'Detail Usulan Sperpat Tidak Boleh Kosong!'
                        ]
                    );
                }else{
                    $this->handleSubmitSave($request);
                }
            }else{
                $this->saveLogNotify();
            }      
            
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleTransStoreOrUpdate($request, $statusOnly = false)
    {
        $this->beginTransaction();
        try {
            
            //dd($request->all());
            $tax = str_replace(['.', ','], '', $request->tax_cost);
            $shiping = str_replace(['.', ','], '', $request->shiping_cost);
            $ts = str_replace(['.', ','], '', $request->ts_cost);

            // if($this->repair_type == 'sperpat'){
            $total = (int)$tax + (int)$shiping + (int)$ts;
            // }else{
            //     $ta = str_replace(['.', ','], '', $request->total_cost);
            //     $total = (int)$ta;
            // }
            
            $spk_start = Carbon::createFromFormat('d/m/Y', $request->spk_start_date);
            $spk_end = Carbon::createFromFormat('d/m/Y', $request->spk_end_date);
            $receipt = Carbon::createFromFormat('d/m/Y', $request->receipt_date);
            $selisih = $spk_start->diffInDays($spk_end);
            
            $this->perbaikan_id = $request->perbaikans_id;
            $this->vendor_id = $request->vendors_id;
            $this->repair_type = $request->repair_type;
            $this->tax_cost = (int)$tax;
            $this->shiping_cost = (int)$shiping;
            $this->total_cost = $total;
            $this->spk_start_date = $spk_start;
            $this->spk_end_date = $spk_end;
            $this->no_spk = $request->no_spk;
            $this->faktur_code = $request->faktur_code;
            $this->sper_status = 'completed';
            $this->spk_range_time = $selisih;
            $this->receipt_date = $receipt;

            // if($this->repair_type == 'vendor'){
            //     $total = str_replace(['.', ','], '', $request->total_cost);
            //     $this->total_cost= (int)$total;
            // }
            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');

            
            $this->save();

            if($request->is_submit == 1){
                
                $this->handleSubmitSaveTrans($request);
                
            }else{
                $this->saveLogNotify();
            }

            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleSubmitSaveTrans($request)
    {
        $this->beginTransaction();
        try {
            $this->update(['status' => 'waiting.approval']);
            $this->generateApproval($request->module);
            $this->saveLogNotify();
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleRejectTrans($request)
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

    public function handleApproveTrans($request)
    {
        $this->beginTransaction();
        try {
            // dd($request);
                if ($this->trans_status === 'waiting.approval.revisi') {
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
                        if($request->sp2d_code == null){
                            return $this->rollback(
                                [
                                    'message' => 'Kode SP2D Wajib Diisi!'
                                ]
                            );
                        }

                        if($request->sp2d_date == null){
                            return $this->rollback(
                                [
                                    'message' => 'Tanggal SP2D Wajib Diisi!'
                                ]
                            );
                        }
                            // dd($this);
                        $this->update(['status' => 'waiting.approval']);
                        $this->update(['sp2d_code'=> $request->sp2d_code]);
                        $this->update(['sp2d_date' => $request->sp2d_date]);
                        $this->update(['status' => 'waiting.approval']);
                        $this->saveLogNotify();
                    } else {
                        $this->update(['status' => 'completed']);
                        $this->saveLogNotify();
                    }
                }
    
                $redirect = route(request()->get('routes') . '.index');
                return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            $customMessage = 'This is a custom error message';
            return $this->rollback($customMessage); 
        }
    }


    public function handleDetailStoreOrUpdate($request, UsulanSperpat $detail)
    {
        $this->beginTransaction();
        try {

            $detail->fill($request->all());
            $qty = str_replace(['.', ','],'',$request->qty);
            $unit_cost = str_replace(['.', ','],'',$request->unit_cost);

            $detail->unit_cost = $unit_cost;
            $detail->qty = $qty;
            $detail->total_cost = $qty * $unit_cost;
            $this->details()->save($detail);
            $this->save();
            $this->saveLogNotify();

            return $this->commitSaved();
            
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleReject($request)
    {
        $this->beginTransaction();
        try {
            $this->rejectApproval($request->module, $request->note);
            $this->update(['sper_status' => 'rejected']);
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
            $coa = Aset::where('id',$this->kib_id)->value('coa_id');
            $merek = Aset::where('coa_id', $coa)->pluck('merek_type_item')->first(); 
            Aset::where('coa_id',$coa)->where('merek_type_item',$merek)->where('condition','rusak berat')->where('status','notactive')->limit($this->qty)->update(['status'=>'notactive']);
            Aset::where('id',$this->kib_id)->update(['condition'=>'rusak berat']);
            Aset::where('id',$this->kib_id)->update(['status'=>'notactive']);
            $this->delete();
            $this->saveLogNotify();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }

    public function handleDetailDestroy(UsulanSperpat $detail)
    {
        $this->beginTransaction();
        
        try {
            $this->saveLogNotify();
            $detail->delete();
            return $this->commitDeleted([
                'redirect' => route(request()->routes . '.detail', $this->id)
            ]);
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }

        public function handleSubmitSave($request)
    {
        $this->beginTransaction();
        try {
            $this->update(['sper_status' => 'waiting.approval']);
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
                        $this->update(['sper_status' => 'waiting.approval.revisi']);
                        $this->saveLogNotify();
                    } else {
                        $this->update(
                            [
                                'sper_status' => 'draft',
                                'version' => $this->version + 1,
                            ]
                        );
                        $this->saveLogNotify();
                    }
                } else {
                    $this->approveApproval($request->module);
                    if ($this->firstNewApproval($request->module)) {
                        if($request->source_fund_id == null){
                            return $this->rollback(
                                [
                                    'message' => 'Sumber Pendanaan Wajib Diisi !'
                                ]
                            );
                        }else{
                            $this->update(['source_fund_id' => $request->source_fund_id]);
                        }

                        $this->update(['sper_status' => 'waiting.approval']);
                        $this->saveLogNotify();
                    } else {
                        $this->update(['sper_status' => 'completed']);
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
        $data = 'Pengajuan Transaksi Sperpat Aset dengan No Surat : ' . $this->codes->code;
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
                $this->addLog('Memperbarui ' . $data);
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
                // return true;
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

}
