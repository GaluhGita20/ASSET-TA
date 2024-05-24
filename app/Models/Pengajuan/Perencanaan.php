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
        // 'is_repair',
        'regarding',
        'sentence_start',
        'sentence_end',
        'note',
        'procurement_year',
        'status',
        // 'version',
        // 'upgrade_reject'
    ];

    protected $casts = [
        'date'          => 'date',
    ];

    /*******************************
     ** MUTATOR
     *******************************/
    // public function setDateAttribute($value)
    // {
    //     $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value);
    // }

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
        return $query->when(!in_array($user->position->location->id, [13,55,19]), 
        function ($q) use ($user) { 
            return $q->when($user->position->imKepalaDeparetemen(), 
                function ($qq) use ($user) {
                    return $qq->whereIn('struct_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
                },
                function ($qq) use ($user) {
                    return $qq->where('struct_id', $user->position->location->id); 
                }
            );
        })->when(auth()->user()->roles->pluck('id')->contains(2), function ($query) {
            $query->orWhereHas('approvals', function ($q) {
            $q->where('order', 2)->where('status', 'approved');
            });
        });
        
        // ->when(auth()->user()->roles->pluck('id')->contains(3), function ($query) {
        //     $query->orWhereHas('approvals', function ($q) {
        //     $q->where('order', 1)->where('status', 'approved');
        //     });
        // });
        // return $query->latest();
    }
    // public function scopeGrid($query)
    // {
    //     $user = auth()->user();
    //     return $query->when(!in_array($user->position->location->id, [13]), 
    //         function ($q) use ($user) { 
    //             $q->when($user->position->imKepalaDeparetemen(), 
    //                 function ($qq) use ($user) {
    //                     $qq->whereIn('struct_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
    //                 },
    //                 function ($qq) use ($user) {
    //                     $qq->where('struct_id', $user->position->location->id); 
    //                 },
    //             )

    //             ->orWhereHas('approvals', function ($q) use ($user) {
    //                 $q->when($user->id, function ($qq) use ($user) {
    //                      $qq->WhereIn('user_id', $user)->where('status','new');
    //                 },function ($qq) use ($user) {
    //                     $qq->orWhereIn('role_id', $user->getRoleIds())
    //                     ->orWhere('position_id', $user->position->id);
    //                 });
    //             });
    //             // ->orWhereHas('approvals', function ($q) use ($user) {
    //             //     $q->when($user->id, function ($qq) use ($user) {
    //             //          $qq->WhereIn('role_id', $user->getRoleIds())->where('status','new');
    //             //     },function ($qq) use ($user) {
    //             //         $qq->orWhereIn('role_id', $user->getRoleIds())
    //             //         ->orWhere('position_id', $user->position->id);
    //             //     });
    //             // });
    //         }
    //     )
    //     ->latest();
    //     // return $query->latest();
    // }

    public function scopeGridStatusCompleted($query)
    {
        return $query->where('status', 'completed')->latest();
    }

    public function scopeFilters($query)
    {
        return $query->filterBy(['code','procurement_year','status'])
        ->filterBy(['struct_id'])->latest();
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
    public function handleStore($request,$statusOnly = false){
        $this->beginTransaction();
        try {
            
            if($request->procurement_year < now()->format('Y')){
                return $this->rollback(
                    [
                        'message' => 'Periode Usulan Sudah Lewat!'
                    ]
                );
            }
            
            $idMax = Perencanaan::where('struct_id',$request->struct_id)->count('id');
            $dep = OrgStruct::where('id',$request->struct_id)->first('name');
            $format_angka = str_pad(($idMax+1) < 10 ? '0' . ($idMax+1) : ($idMax+1), 3, '0', STR_PAD_LEFT);
            
            $this->code = $format_angka.'/'.$dep->name.'/'.now()->format('Y');
            
            $this->status = 'draft';
            $time = now()->format('Y-m-d');
            $this->date =  $time;
            
            $data = $request->all();
            $this->fill($data);
            $this->save();
            // $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
            // dd($this->struct);
            // dd($data);
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
            // dd($request->all());
            if($request->procurement_year < now()->format('Y')){
                return $this->rollback(
                    [
                        'message' => 'Tahun Usulan Sudah Lewat!'
                    ]
                );
            }

            if($request->note != null){
              //  dd($request->all());
                $this->update(['note' => $request->note]);
            }

            
            if($request->is_submit == 0 && $request->regarding != null){
                $data = $request->all();
                $this->fill($data);
                // $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
                $this->save();
                // $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
                $this->saveLogNotify();
            }

            $data = PerencanaanDetail::Where('perencanaan_id',$this->id)->count();
            if ($request->is_submit == 1) {
                
                if($data == 0){
                    return $this->rollback(
                        [
                            'message' => 'Detail Usulan Tidak Boleh Kosong!'
                        ]
                    );
                }

                if($request->note != null){
                    $this->update(['note',$request->note]);
                }

                if($request->sentence_start !=null){
                    $this->update(['sentence_start'=> $request->sentence_start]);
                    $this->update(['sentence_end' => $request->sentence_end]);
                }
                
                $this->handleSubmitSave($request);
                
            }
            if($request->sentence_start !=null){
                //dd($request->sentence_start);
                $this->update(['sentence_start' => $request->sentence_start]);
                $this->update(['sentence_end' => $request->sentence_end]);
            }
            
            

            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleDetailApproval($record){
        $data = PerencanaanDetail::where('perencanaan_id',$record->id)->get();
        $validasi = PerencanaanDetail::where('perencanaan_id',$record->id)->count('id');
        
        //perencanaan
        $approval1 = $record->whereHas('approvals', function ($q) use ($record) {
            $q->where('target_id',$record->id)->where('status','!=','approved')->where('role_id',5);
        })->count(); //departemen belum approved maka nilai nya 1

        $approval2 = $record->whereHas('approvals', function ($q) use ($record) {
            $q->where('target_id',$record->id)->where('status','!=','approved')->where('role_id',3);
        })->count(); //departemen suddah approved dan perencanaan belum approved

        $approval3 = $record->whereHas('approvals', function ($q) use ($record) {
            $q->where('target_id',$record->id)->where('status','!=','approved')->where('role_id',2);
        })->count();

        //$approval1 
        $flag = 0;
        $list_error[] =[];
       // dd($data);
        foreach($data as $datas){
            if($datas->status == 'draf' && ($approval1 > 0)){ //data masih baru
                $flag = $flag + 0;
            }elseif($datas->status == 'waiting purchase' && ($approval1 > 0)){ //data baru di reject
                $flag = $flag + 0;
            }elseif($datas->status =='waiting purchase' && ($approval2 > 0) && ($approval1 == 0) ){
                $flag = $flag + 0;
            }elseif($datas->status =='waiting purchase' && ($approval3 > 0) && ($approval2 == 0) && ($approval1 == 0) ){
                $flag = $flag + 0;
            }else{
                $flag = $flag + 1;
                $list_error[] = ['text' => $datas->asetd->name];
            }
        }
        if($flag <= 0){
            return $data = [
                    'status' => 'ok',
                    'message' => 'null',
            ];
        }else{
            // return $this->rollback(
            //     [
            //         'message' => 'Silahkan lakukan approval pada .'
            //     ]
            // );
            $datak = implode(', ', array_column($list_error, 'text'));
            return $data = [
                'status' => 'gagal',
                'message' => $datak,
            ];
        }
    }

    public function handleDetailStoreOrUpdate($request, PerencanaanDetail $detail)
    {
        $this->beginTransaction();
        try {
            $flag = PerencanaanDetail::where('perencanaan_id', $this->id)->where('ref_aset_id',$request->ref_aset_id)->where('id','<>',$detail->id)->count();
            if($flag > 0){
                return $this->rollback(
                    [
                        'message' => 'Refrensi Aset Sudah Digunakan Untuk Perencanaan Ini!'
                    ]
                );
            }
            
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

            
            if($request->is_submit == 1){
                if($value6 > 0 && $value6 != $value5 && $request->source_fund_id == null){
                    return $this->rollback(
                        [
                            'message' => 'Silahkan isi Sumber Pembiayaan !'
                        ]
                    );
                }
    
                if($value6 == $value5 && $request->source_fund_id == null){
                    return $this->rollback(
                        [
                            'message' => 'Silahkan isi Sumber Pembiayaan !'
                        ]
                    );
                }
                if($value6 < $value5 && $request->reject_notes == null){
                    return $this->rollback(
                        [
                            'message' => 'Silahkan Berikan Alasan Penolakan Untuk '.$value5-$value6.' Usulan Ditolak!'
                        ]
                    );
                }else{
                    $detail->status = 'waiting purchase';
                    if($value6 == $value5){
                        $detail->reject_notes = null;
                    }else{
                        $detail->reject_notes = $request->reject_notes;
                    }

                    if($value6 == 0){
                        $detail->source_fund_id = null;
                    }

                    $this->details()->save($detail);
                    $this->save();
                    
                    $this->saveLogNotify();
                    return $this->commitSaved();
                }
            }else{
                $detail->reject_notes = null;
                $detail->source_fund_id = null;
                $this->details()->save($detail);
                $this->save();
                $this->saveLogNotify();
    
                return $this->commitSaved();
            }


        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleReject($request)
    {
        $this->beginTransaction();
        try {
            // dd('tes');
            $data = PerencanaanDetail::where('perencanaan_id', $this->id)->update(['status' => 'draf']);
            // dd($data);
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
            if($data > 0){
                $this->update(['status' => 'waiting.approval']);
                if($request->note != null){
                    $this->update(['note',$request->note]);
                }
                //dd($request->module);
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
                        $this->saveLogNotify();
                       // $this->_generateReport('completed');
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
        $data = 'Pengajuan Pembelian Aset dengan No Surat : ' . $this->code;
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
    //-4151848000


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
