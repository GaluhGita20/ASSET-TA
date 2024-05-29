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
        'maintenance_date',
        'status',
    ];

    protected $casts = [
        'maintenance_date'  => 'date',
    ];

    /*******************************
     ** MUTATOR
     *******************************/
    public function mainTenanceDate($value)
    {
        $this->attributes['maintenance_date'] = Carbon::createFromFormat('d/m/Y', $value);
    }

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
        return $query->when(empty(array_intersect(['Direksi','Sarpras','BPKAD'], $user->roles->pluck('name')->toArray())), 
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
        return $query->filterBy(['code','status'])
        ->filterBy(['departemen_id'])->when(request()->maintenance_date_year, function ($q) {
            // $date = request()->maintenance_date_year;
            // $formatted_date = Carbon::createFromFormat('d/m/Y',$date)->format('Y');
            $q->whereYear('maintenance_date',request()->maintenance_date_year);
        })->when(request()->maintenance_date_month, function ($q) {
            // $date = request()->maintenance_date_month;
            // $formatted_date = Carbon::createFromFormat('d/m/Y',$date)->format('m');
            $q->whereMonth('maintenance_date',request()->maintenance_date_month);
        })->latest();
    }

    /*******************************
     ** SAVING
     *******************************/
    public function handleStore($request,$statusOnly = false){
        $this->beginTransaction();
        try {
            $now = Carbon::now();
            $time = Carbon::createFromFormat('d/m/Y', $request->maintenance_date);
            if($now->month == $time->month && $now->year == $time->year){
               // $data = $request->all();
                $this->maintenance_date = $time;
                $this->departemen_id = $request->departemen_id;
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
                $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
                $this->createDetail($request);
                $this->saveLogNotify();
                $redirect = route(request()->get('routes') . '.index');
                return $this->commitSaved(compact('redirect'));
            }else{
                return $this->rollback(
                    [
                        'message' => 'Jadwal Pemeliharaan Hanya Dapat Dibuat Pada Bulan Ini! (Bulan '.$now->month.' Tahun '.$now->year.')'
                    ]
                );
            }
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function createDetail($request){
        $dep = $request->departemen_id;
        $data = Aset::where('condition', 'baik')
            ->where(function ($query) use ($dep) {
                $query->whereHas('usulans', function ($q) use ($dep) {
                    $q->whereHas('perencanaan', function ($q) use ($dep) {
                        $q->where('struct_id', $dep);
                    });
                })
                ->orWhere('location_hibah_aset', $dep);
            })
            ->where('status', 'actives')
            ->where('acq_value', '>=', 1000000)
            ->whereIn('type', ['KIB B', 'KIB E'])
            ->pluck('id')
            ->toArray();

        // Update status aset menjadi 'maintenance'
        Aset::whereIn('id', $data)->update(['status' => 'maintenance']);

        // $data = Aset::where('condition','baik')->where('status', 'actives')->whereHas('usulans', function ($q) use ($dep) {
        //     $q->whereHas('perencanaan', function ($qq) use ($dep) {
        //         $qq->where('struct_id', $dep);
        //     })->whereHas('trans',function($qqq){
        //         $qqq->where('unit_cost','>=',1000000);
        //     });
        // })->orWhere('location_hibah_aset', $dep)->whereHas('usulans', function ($q){
        //     $q->whereHas('trans', function ($qq){
        //         $qq->where('unit_cost','>=', 1000000);
        //     });
        // })->whereIn('type', ['KIB B','KIB E'])->pluck('id')->toArray();


        // $data = Aset::where('condition','baik')->whereHas('usulans',function($q)use($dep){
        //     $q->whereHas('perencanaan', function($q) use ($dep){
        //         $q->where('struct_id',$dep);
        //     });
        // })->orWhere('location_hibah_aset',$dep)->where('status', 'actives')->where('acq_value','>=',1000000)->whereIn('type', ['KIB B','KIB E'])->pluck('id')->toArray();

        // Aset::whereIn('id',$data)->update(['status'=>'maintenance']);
        // $data = Aset::with('usulans')
        //     ->where('condition', 'baik')
        //     ->where('status', 'actives')
        //     // ->where('unit_cost','>=',100000)
        //     // ->whereNotIn('id',$peliharaan)
        //     ->where(function ($query) use ($dep) {
        //         $query->orWhere(function ($q) use ($dep) {
        //             $q->whereHas('usulans', function ($qq) use ($dep) {
        //                 $qq->whereHas('perencanaan', function ($qqq) use ($dep) {
        //                     $qqq->where('struct_id', $dep);
        //                 });
        //             });
        //         })
        //         ->orWhere('location_hibah_aset', $dep);
        //     })->whereIn('type', ['KIB B','KIB E'])->pluck('id')->toArray();

        //dd($data);

        foreach ($data as $aset_id) {
            $detail = new PemeliharaanDetail;
            $detail->pemeliharaan_id = $this->id;
            $detail->kib_id = $aset_id;
            $detail->save();
        }
    }

    // public function handleStoreOrUpdate($request){
    //     $this->beginTransaction();
    //     try {
    //         $data = $request->all();
    //         $this->fill($data);
    //         $this->save();
    //         $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
    //         $redirect = route(request()->get('routes') . '.index');
    //         return $this->commitSaved(compact('redirect'));
    //     } catch (\Exception $e) {
    //         return $this->rollbackSaved($e);
    //     }
    // }

    public function handleStoreOrUpdate($request, $statusOnly = false)
    {
        $this->beginTransaction();
        try {
            
            if($request->is_submit == 1){
                $time = $this->maintenance_date;
                $check = PemeliharaanDetail::Where('pemeliharaan_id',$this->id)->whereHas('pemeliharaan',function ($q) use ($time) {
                    $q->whereMonth('maintenance_date', $time->month);
                })->count();

                $flag = PemeliharaanDetail::Where('pemeliharaan_id',$this->id)->where('maintenance_action','!=',null)->whereHas('pemeliharaan',function ($q) use ($time) {
                    $q->whereMonth('maintenance_date', $time->month);
                })->count();

                if($check == $flag){
                    $this->handleSubmitSave($request);
                    $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
                   // Pemeliharaan::where('id',$this->id)->update(['status'=>'waiting.verify']);
                    return $this->commitSaved();
                }else{
                    return $this->rollback(
                        [
                            'message' => 'Silahkan Lengkapi Data Pemeliharaan Sebelum Melakukan Submit!'
                        ]
                    );
                }
            }

            if($request->is_submit == 0 && $request->maintenance_date == null){
                $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
                $redirect = route(request()->get('routes') . '.index');
                return $this->commitSaved(compact('redirect'));
            }

            $time = Carbon::createFromFormat('d/m/Y', $request->maintenance_date);
            $last_time = $this->maintenance_date;
            if($last_time->month == $time->month && $last_time->year == $time->year){
                Pemeliharaan::where('id',$this->id)->update(['maintenance_date'=>$time]);
                $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
                $this->saveLogNotify();
            }else{
                return $this->rollback(
                    [
                        'message' => 'Pembaruan Jadwal Pemeliharaan Hanya Dapat Dilakukan Pada Bulan Yang Sama Dengan Jadwal Sebelumnya! (Bulan '.$last_time->month.' Tahun '.$last_time->year.')'
                    ]
                );
            }
            // $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');

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
           // $time = $this->maintenance_date;
            $this->details()->save($detail);
            $this->save();
            Aset::where('id',$request->kib_id)->update(['status'=>'maintenance']);
            // $awal = PemeliharaanDetail::where('pemeliharaan_id',$this->id)->whereHas('pemeliharaan',function ($q) use ($time) {
            //     $q->whereMonth('maintenance_date', $time->month);
            // })->count('id');
            // $flag = PemeliharaanDetail::where('pemeliharaan_id',$this->id)->where('maintenance_action','<>',null)->whereHas('pemeliharaan',function ($q) use ($time) {
            //     $q->whereMonth('maintenance_date', $time->month);
            // })->count('id');
            // if($flag == $awal){
            //     Pemeliharaan::where('id',$this->id)->update(['status'=>'completed']);
            //     $this->saveLogNotify();
            // }
            return $this->commitSaved();
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleDestroy()
    {
        $this->beginTransaction();
        try {
            $aset = PemeliharaanDetail::where('pemeliharaan_id',$this->id)->pluck('kib_id')->toArray();
            Aset::whereIn('id',$aset)->update(['status' => 'actives']);

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
            Aset::where('id',$detail->kib_id)->update(['status'=>'actives']);
            $detail->delete();
            return $this->commitDeleted([
                'redirect' => route(request()->routes . '.detail', $this->id)
            ]);
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
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
                        $aset = PemeliharaanDetail::where('pemeliharaan_id',$this->id)->pluck('kib_id')->toArray();
                        Aset::whereIn('id',$aset)->update(['status' => 'actives']);
                        // Aset::where('id',$this->kib_id)->update(['status'=>'notactive']);
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
        $location = 8;
        $kepala_department = User::whereHas('position', function ($q) use ($location) {
            $q->where([['location_id', $location], ['level', 'kepala']]);
        })->pluck('id')->toArray();

        $data = 'Jadwal Pemeliharaan Aset : ' . $this->code;
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
                        'user_ids' => $kepala_department,
                    ]);
                    $pesan = $user.' Menunggu Verifikasi Hasil Pemeliharaan';
                    $this->sendNotification($pesan);
                }
                
                break;
            case $routes . '.update':
                $this->addLog('Memperbarui ' . $data.' '.'dan Hasil Pemeliharaan');
                if (request()->is_submit) {

                    $this->addLog('Submit ' . $data. ' '.'dan Hasil Pemeliharaan');
                    //dd($data);
                    $this->addNotify([
                        'message' => 'Waiting Verification ' . $data,
                        'url' => route($routes . '.approval', $this->id),
                        'user_ids' => $kepala_department,
                    ]);
                    
                    $pesan = $user.' Menunggu Verifikasi Hasil Pemeliharaan';
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
        $chatId = '-4170853844'; // Ganti dengan chat ID penerima notifikasi

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
