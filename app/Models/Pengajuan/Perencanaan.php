<?php

namespace App\Models\Pengajuan;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Approval;
use App\Models\Globals\MenuFlow;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use App\Models\Model;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Traits\HasApprovals;
use App\Models\Traits\HasFiles;
use App\Support\Base;
use Telegram\Bot\Laravel\Facades\Telegram;
use Carbon\Carbon;

class Perencanaan extends Model
{
    use HasFiles, HasApprovals;

    protected $table = 'trans_usulan';

    protected $fillable = [
        'code',
        'date',
        'struct_id',
        'is_repair',
        'regarding',
        'sentence_start',
        'sentence_end',
        'note',
        'procurement_year',
        'status',
        'version',
        'upgrade_reject'
    ];

    protected $casts = [
        'date'          => 'date',
    ];

    /*******************************
     ** MUTATOR
     *******************************/
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value);
    }

    /*******************************
     ** ACCESSOR
     *******************************/

    /*******************************
     ** RELATION
     *******************************/

    public function struct()
    {
        return $this->belongsTo(OrgStruct::class, 'struct_id');
    }

    public function details()
    {
        return $this->hasMany(PerencanaanDetail::class, 'perencanaan_id');
    }

    // public function cc()
    // {
    //     return $this->belongsToMany(User::class, 'trans_pengajuan_pembelian_cc', 'perencanaan_id', 'user_id');
    // }

    public function to_user()
    {
        return $this->belongsTo(User::class, 'user_kepada');
    }

    /*******************************
     ** SCOPE
     *******************************/
    public function scopeGrid($query)
    {
        $user = auth()->user();
        return $query->when(
            !in_array($user->position->location->id, [13]),
           // !in_array($user->position->location->level,['departemen']),
            function ($q) use ($user) {
                $q->when(
                    $user->position->imKepalaDeparetemen(),
                    function ($qq) use ($user) {
                        $qq->whereIn('struct_id', $user->position->location->getIdsWithChild());
                    },
                    function ($qq) use ($user) {
                        $qq->where('struct_id', $user->position->location->id);
                    },
                )->orWhereHas('approvals', function ($q) use ($user) {
                    $q->when($user->id, function ($qq) use ($user) {
                        // If user_id is filled
                        $qq->where('user_id', $user->id);
                    }, function ($qq) use ($user) {
                        // If user_id is not filled
                        $qq->orWhereIn('role_id', $user->getRoleIds())
                           ->orWhere('position_id', $user->position->id);
                    });
                });
            }
        )
        ->latest();
        // return $query->latest();
    }

    public function scopeGridStatusCompleted($query)
    {
        return $query->where('status', 'completed')->latest();
    }

    public function scopeFilters($query)
    {
        return $query->latest();
        // $position = auth()->user()->position_id;
        // $departemen = Position::with('location')->where('id',$position)->first();
        
        // if($departemen->location->level == 'department'){

        //     $data = OrgStruct::where(function ($subquery) use ($departemen) {
        //         $subquery->where(function ($innerSubquery) use ($departemen){
        //             $innerSubquery->where('level','subdepartment')->whereIn('parent_id',[$departemen->location_id]);
        //                 //->orWhere('level', 'department')->where('id',$departemen->location_id);
        //         });
        //     });

        //     $results = $data->get();
        //     $ids = $results->pluck('id')->all();
        //     //dd($ids);
        //     $query->orWhere(function ($q) use ($departemen){
        //         $q->where('struct_id', '=', $departemen->location_id);
        //     });

        //     $query->orWhere(function ($q) use ($ids){
        //         $q->whereIn('struct_id', $ids)->where('status','!=','Draft');
        //     });

        //     // dd($ids);
        //     return $query;
        // }

        // elseif($departemen->location->level == 'subdepartment' )
        //     if(auth()->user()->hasRole('Sub Bagian Program Perencanaan')){
        //         $usulan = Perencanaan::all();
        //         $idx = $usulan->pluck('id')->all();

        //         $data = Approval::whereIn('target_id',$idx)->where(function ($query) {
        //             $query->where('order',1)->orWhere('order',2);
        //         })->where('status','approved');

        //         $results = $data->get();
        //         $ids = $results->pluck('target_id')->all();

        //         $query->orWhere(function ($q) use ($departemen){
        //             $q->where('struct_id', '=', $departemen->location_id);
        //         });
            
        //         // Filter data usulan lain dengan status waiting approval
        //         $query->orWhere(function ($q) use ($ids) {
        //             $q->whereIn('id',$ids);
        //         });
            
        //         return $query;
        //     }else{
        //         return $query->where('struct_id',$departemen->location_id);
        //     }
        // elseif($departemen->location->level == 'bod') //pengakses
        //     $usulan = Perencanaan::all();
        //     $idx = $usulan->pluck('id')->all();

        //     $data = Approval::whereIn('target_id',$idx)->where(function ($query) {
        //         $query->where('order',2)->orWhere('order',3);
        //     })->where('status','approved');

        //     $results = $data->get();
        //     $ids = $results->pluck('target_id')->all();
        //     // dd($ids);

        //     $query->orWhere(function ($q) use ($departemen){
        //         $q->where('struct_id', '=', $departemen->location_id);
        //     });
        
        //     // Filter data usulan lain dengan status waiting approval
        //     $query->orWhere(function ($q) use ($ids) {
        //         $q->whereIn('id',$ids);
        //     });
        
        //     return $query;
            
        // $results = $data->get();
        // $ids = $results->pluck('id')->all();
        // // dd($ids);
        // return $query->whereIn('struct_id',$ids);
        
    }

    /*******************************
     ** SAVING
     *******************************/
    public function handleStoreOrUpdate($request, $statusOnly = false)
    {
        $this->beginTransaction();
        try {
            $data = $request->all();
            $this->fill($data);
            $this->status = 'draft';
            $this->save();
            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
            $this->saveLogNotify();
           // $this->cc()->sync($request->cc ?? []);

            // dd($request->is_submit);
            $data = PerencanaanDetail::Where('perencanaan_id',$this->id)->count();
           // dd($data);
            if ($request->is_submit) {
                if($data > 0){
                    $this->handleSubmitSave($request);
                }else{
                    return $this->rollback(
                        [
                            'message' => 'Detail Usulan Tidak Boleh Kosong!'
                        ]
                    );
                }
            }
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleDetailStoreOrUpdate($request, PerencanaanDetail $detail)
    {
        $this->beginTransaction();
        try {

            $detail->fill($request->all());
            $value1 = str_replace(['.', ','],'',$request->HPS_unit_cost);
            $detail->HPS_unit_cost = (int)$value1;

            $value2 = str_replace(['.', ','],'',$request->HPS_total_agree);
            $detail->HPS_total_agree = (int)$value2;

            $value3 = str_replace(['.', ','],'',$request->existing_amount);
            $detail->existing_amount = (int)$value3;

            $value4 = str_replace(['.', ','],'',$request->requirement_standard);
            $detail->requirement_standard = (int)$value4;

            $value5 = str_replace(['.', ','],'',$request->qty_req);
            $detail->qty_req = (int)$value5;

            $value6 = str_replace(['.', ','],'',$request->qty_agree);
            $detail->qty_agree = (int)$value6;

            $this->details()->save($detail);
            $this->status = 'draft';
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
            // dd('tes');
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

    public function handleDetailDestroy(PerencanaanDetail $detail)
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

            $data = PerencanaanDetail::Where('perencanaan_id',$this->id)->count();
           // dd($data);
            if($data > 0){
                $this->update(['status' => 'waiting.approval']);
                $this->generateApproval($request->module);
                $this->saveLogNotify();
                $redirect = route(request()->get('routes') . '.index');
                return $this->commitSaved(compact('redirect'));
            }else{
                return $this->rollback(
                    [
                        'message' => 'Detail Usulan Tidak Boleh Kosong!'
                    ]
                );
            }
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleVerify($request)
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

            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function saveLogNotify()
    {
        $data = 'Pengajuan Pembelian No Surat : ' . $this->code;
        $routes = request()->get('routes');
        switch (request()->route()->getName()) {
            case $routes . '.store':
                $this->addLog('Membuat ' . $data);
                $pesan = 'Membuat ' . $data;
                $this->sendNotification($pesan);
                if (request()->is_submit) {
                    $this->addLog('Submit ' . $data);
                    $this->addNotify([
                        'message' => 'Waiting Verification ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => auth()->user()->imVerificationKepalaDepartement($this->struct),
                    ]);
                    $pesan = 'Waiting Verification ' . $data;
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

                    $pesan = 'Waiting Verification ' . $data;
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
