<?php

namespace App\Models\Perbaikan;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Approval;
use App\Models\Globals\MenuFlow;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use App\Models\Master\Vendor\Vendor;
use App\Models\Master\Dana\Dana;
use App\Models\Model;
use App\Models\Pengajuan\Perbaikan;
use App\Models\Perbaikan\TransPerbaikanDisposisi;
//use App\Models\Perbaikan\PerbaikanDisposisiDetail;
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

class UsulanSperpat extends Model
{
    use HasFiles, HasApprovals;

    protected $table = 'trans_detail_sperpat';

    protected $fillable = [
        'trans_perbaikan_id',
        'sperpat_name',
        'desc_sper',
        'qty',
        'unit_cost',
        'total_cost',
        //'source_cost',
    ];

    protected $casts = [

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

    public function perbaikans()
    {
        return $this->belongsTo(TransPerbaikanDisposisi::class, 'trans_perbaikan_id');
    }

    public function danad()
    {
        return $this->belongsTo(Dana::class,'source_cost');
    }

    /*******************************
     ** SCOPE
     *******************************/

    public function scopeGrid($query)
    {
        $user = auth()->user();
        // return $query->when(empty(array_intersect(['Sarpras','Sub Bagian Program Perencanaan','BPKAD'], $user->roles->pluck('name')->toArray()))
        // )->when(auth()->user()->roles->pluck('id')->contains(3), function ($query) {
        //     $query->orWhereHas('approvals', function ($q) {
        //         $q->where('module','usulan_pembelian-sperpat')->where('order', 1)->whereIn('status', ['new','rejected']);
        //     });
        // })
        // ->when(auth()->user()->roles->pluck('id')->contains(2), function ($query) {
        //     $query->whereHas('approvals', function ($subQuery) {
        //         $subQuery->where('module','usulan_pembelian-sperpat')->where('order', 1)->where('status', 'approved');
        //     })
        //     ->whereHas('approvals', function ($subQuery) {
        //         $subQuery->where('module','usulan_pembelian-sperpat')->where('order', 2)->where('status', 'new');
        //     });
        // });

        return $query->when(empty(array_intersect(['Sarpras','BPKAD'], $user->roles->pluck('name')->toArray())),
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
        // return $query->when(empty(array_intersect(['Sub Bagian Program Perencanaan','Sarpras'], $user->roles->pluck('name')->toArray())),
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
        // return $query->when(
        //     $stat = request()->status,
        //     function ($q) use ($stat){
        //         $q->whereHas('perbaikans', function ($qq) use ($stat){
        //                 $qq->where('sper_status',$stat);
        //     });
        // })
            return $query
        ->where('sperpat_name','LIKE','%'.request()->sperpat_name.'%')->latest();
        
    }

    /*******************************
     ** SAVING
     *******************************/
    public function handleStore($request,$statusOnly = false){
        $this->beginTransaction();
        try {

            $data = $request->all();
            // dd($data);

            $this->fill($data);
            $qty = str_replace(['.', ','],'',$request->qty);
            
            $unit_cost = str_replace(['.', ','],'',$request->unit_cost);
            // $total_cost = str_replace(['.', ','], '', $request->total_cost);

            $this->qty = (int)$qty;
            $this->unit_cost = (int)$unit_cost;
            $this->total_cost = (int)$unit_cost * (int)$qty;
          

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
            
            if($request->procurement_year < now()->format('Y')){
                return $this->rollback(
                    [
                        'message' => 'Periode Usulan Sperpat Sudah Lewat!'
                    ]
                );
            }

            $data = $request->all();
            $this->fill($data);

            $qty = str_replace(['.', ','], '', $request->qty);
            $unit_cost = str_replace(['.', ','], '', $request->unit_cost);
            // $total_cost = str_replace(['.', ','], '', $request->total_cost);

            $this->qty = $qty;
            $this->unit_cost = $unit_cost;
            $this->total_cost = $unit_cost * $qty;

            $this->save();

            $this->saveLogNotify();

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
            $this->delete();
            $this->saveLogNotify();

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
                        $this->update(['status' => 'completed']);
                        $coa = Aset::where('id',$this->kib_id)->value('coa_id');
                        $merek = Aset::where('coa_id', $coa)->pluck('merek_type_item')->first(); 
                        // Aset::where('coa_id',$coa)->where('merek_type_item',$merek)->where('condition','rusak berat')->where('status','notactive')->limit($this->qty)->update(['status'=>'notactive']);
                        Aset::where('id',$this->kib_id)->update(['condition'=>'rusak berat']);
                        Aset::where('id',$this->kib_id)->update(['status'=>'clean']);
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
        $data = 'Pengajuan Usulan Sperpat dengan No Surat : ' . $this->code;
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
                        'message' => 'Menyetujui ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);

                    $pesan = $user. ' Menyetujui ' . $data;
                    $this->sendNotification($pesan);

                } else {
                    $this->addLog('Menyetujui ' . $data);

                    $this->addNotify([
                        'message' => 'Menyetujui ' . $data,
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
