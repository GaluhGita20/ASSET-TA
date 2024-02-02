<?php

namespace App\Models\Transaksi;

use App\Models\Model;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Master\Aset\Aset;
use App\Models\Traits\HasApprovals;
use App\Models\Traits\HasFiles;
use App\Support\Base;
use App\Models\Globals\MenuFlow;
use App\Models\Auth\User;
use App\Models\Master\Dana\Dana;
use App\Models\Master\Vendor\Vendor;
use App\Models\Master\Pengadaan\Pengadaan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use App\Models\Auth\Role;
use App\Models\Globals\Approval;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use Telegram\Bot\Laravel\Facades\Telegram;

class PembelianTransaksi extends Model
{
    use HasFiles, HasApprovals;
    protected $table = 'trans_aset';

    protected $fillable = [
        'trans_name',
        'vendor_id',
        'receipt_date',
        'location_receipt',
        'asset_test_results',
        'status',
        'asset_test_results',
        'location_receipt',
    ];

    protected $casts = [
        'receipt_date'          => 'date',
    ];

 

   
    /*******************************
     ** MUTATOR
     *******************************/
   


    /*******************************
     ** RELATION
     *******************************/

    public function pengujianPengadaan()
    {
        return $this->belongsToMany(User::class, 'trans_pivot_pengujian','trans_id','user_id');
    }

    public function vendors()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
   
    /*******************************
     ** SCOPE
     *******************************/

     public function scopeGrid($query)
     {
        $user = auth()->user();
       // dd($user->roles()->pluck('name'));
        return $query->when(empty(array_intersect(['PPK','Keuangan'], $user->roles->pluck('name')->toArray())),
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
     }

    public function scopeFilters($query)
    {
        // return $query;
        return $query->filterBy(['vendor_id','receipt_date'])->when(
            $jenis_vendor = request()->vendor_id,
            function ($q) use ($jenis_vendor){
                $q->whereHas('vendors', function ($qq) use ($jenis_vendor){
                        $qq->where('id',$jenis_vendor);
            });
        })->latest();
    }


    /*******************************
     ** SAVING
     *******************************/
    public function handleStoreOrUpdate($request)
    {
        $this->beginTransaction();
        try { 

            $data = $request->all();
            $this->fill($data);
            $this->save();

            $this->pengujianPengadaan()->sync($request->user_id ?? []);
        
            if ($request->is_submit == 1) {
                $this->handleSubmitSave($request);
            }
            
            $redirect = route('transaksi.hibah-aset' . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
          //  Log::error('Kesalahan: ' . $e->getMessage());
            return $this->rollbackSaved($e->getMessage());
        }
    }

    public function handleSubmitSave($request)
    {
        $this->beginTransaction();
        try {
            // dd('tes');
            $this->update(['status' => 'waiting.approval']);

            $this->generateApproval($request->module);
            $this->saveLogNotify();
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
            $this->delete();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }
    

    public function handleApprove($request)
    {
        $this->beginTransaction();
        try {
            //dd($request);
            
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
                        $this->saveLogNotify();
                       // $this->_generateReport('completed');
                    }
                }

                $user = auth()->user();
                $flag =  $this->whereHas('approvals',function($q){
                    $q->where('target_id',$this->id)->where('role_id',2)->where('status','approved');
                })->count();
                
                if($flag == 1){
                    $data = $this->perencanaanPengadaan('detail_usulan_id');
                    $detailUsulan = $this->perencanaanPengadaan()->where('pembelian_id', $this->id)->get()->pluck('pivot.detail_usulan_id')->toArray();
                    PerencanaanDetail::whereIn('id',$detailUsulan)->update(['status' => 'waiting register']);
                }
    
                $redirect = route(request()->get('routes') . '.index');
                return $this->commitSaved(compact('redirect'));

        } catch (\Exception $e) {
            $customMessage = 'This is a custom error message';
            return $this->rollback($customMessage);
          //  return $this->rollbackSaved($e);
            
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


    public function saveLogNotify()
    {
        $user = auth()->user()->name;
        $data = 'Transaksi : ' . $this->trans_name;
        $routes = request()->get('routes');
        switch (request()->route()->getName()) {
            case $routes . '.store':
                $this->addLog('Membuat ' . $data);
                $pesan = 'Membuat ' . $data;
                $this->sendNotification($pesan);
                if (request()->is_submit) {
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Approval ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);

                    $pesan = $user.' Mengajukan Approval '.$data;
                    $this->sendNotification($pesan);
                }
                break;
            case $routes . '.update':
                $this->addLog('Mengubah ' . $data);
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

                    $pesan = 'Waiting Approval Revisi ' . $data;
                    $this->sendNotification($pesan);

                } else {
                    $this->addLog('Menyetujui ' . $data);

                    $this->addNotify([
                        'message' => 'Waiting Approval ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                    ]);
                    
                    $pesan = $user.' Menyetujui ' . $data;
                    $this->sendNotification($pesan);
                }
                break;
                // ada lagi? aman kan?
                //aman gal thanks suhu
                // okay ntar kalau ada apa2 lagi ingpoin aja, 24 jam dpn leptop kok aku. Aku putusin koneksi ya byeeee
            case $routes . '.reject':
                if (in_array($this->status, ['rejected'])) {
                    $this->addLog('Menolak ' . $data . ' dengan alasan: ' . request()->get('note'));

                    $this->addNotify([
                        'message' => 'Menolak ' . $data . ' dengan alasan: ' . request()->get('note'),
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => [$this->created_by],
                    ]);
                    $pesan = $user.' Menolak ' . $data;
                    $this->sendNotification($pesan);
                } else {
                    $this->addLog('Menolak Revisi ' . $data . ' dengan alasan: ' . request()->get('note'));

                    $this->addNotify([
                        'message' => 'Menolak Revisi ' . $data . ' dengan alasan: ' . request()->get('note'),
                        'url' => route($routes . '.show', $this->id),
                        'user_ids' => [$this->created_by],
                    ]);
                    $pesan = $user.' Menolak ' . $data;
                    $this->sendNotification($pesan);
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
                $checkStatus = in_array($this->status, ['new', 'draft']);
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
                    // dd(request()->get('module'));
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
