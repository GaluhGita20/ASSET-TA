<?php

namespace App\Models\Pengajuan;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Approval;
use App\Models\Globals\MenuFlow;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use App\Models\Model;
use App\Models\Master\Pemutihan\Pemutihan;
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

class Pemutihans extends Model
{
    use HasFiles, HasApprovals;

    protected $table = 'trans_pemutihan';

    protected $fillable = [
        'code',
        'qty',
        'kib_id',
        'submmission_date',
        'clean_type',
        'status',
        'pic',
        'target',
        'valued',
        'location'
    ];

    protected $casts = [
        'submmission_date'   => 'date',
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
        return $this->belongsTo(Aset::class,'kib_id');
    }

    public function pemutihanType()
    {
        return $this->belongsTo(Pemutihan::class, 'clean_type');
    }

    public function picd()
    {
        return $this->belongsTo(User::class, 'pic');
    }



    /*******************************
     ** SCOPE
     *******************************/

    public function scopeGrid($query)
    {
        $user = auth()->user();
        return $query->when(empty(array_intersect(['Sarpras','Keuangan','Direksi','BPKAD'], $user->roles->pluck('name')->toArray())), 
            function ($q) use ($user) { 
            return $q->when($user->position->imKepalaDeparetemen(), 
                function ($qq) use ($user) {
                    return $qq->whereIn('departemen_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
                },
                function ($qq) use ($user) {
                    return $qq->where('departemen_id', $user->position->location->id); 
                }
            );
        })->when(auth()->user()->roles->pluck('id')->contains(7), function ($query) {
            $query->orWhereHas('approvals', function ($q) {
                $q
                    ->where('order', 1)
                    ->where('status', 'approved');
            });
        });
        
        // return $query->when(!in_array($user->position->location->id, [19]), 
        //     function ($q) use ($user) { 
        //     return $q->when($user->position->imKepalaDeparetemen(), 
        //         function ($qq) use ($user) {
        //             return $qq->whereIn('departemen_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
        //         },
        //         function ($qq) use ($user) {
        //             return $qq->where('departemen_id', $user->position->location->id); 
        //         }
        //     );
        // })->when(auth()->user()->roles->pluck('id')->contains(7), function ($query) {
        //     $query->orWhereHas('approvals', function ($q) {
        //         $q
        //             ->where('order', 1)
        //             ->where('status', 'approved');
        //     });
        // );

    }

    public function scopeGridStatusCompleted($query)
    {
        return $query->where('status', 'completed')->latest();
    }

    public function scopeFilters($query)
    {
        return $query->filterBy(['code','status'])
        ->filterBy(['struct_id'])->when(
            $names = request()->aset_name,
            function ($q) use ($names){
                $q->whereHas('asets', function ($qq) use ($names){
                    $qq->whereHas('asetData',function ($qqq) use ($names){
                        $qqq->where('name','LIKE','%'.request()->sperpat_name.'%');
                    });
            });
        })->when(request()->submmission_date, function ($q) {
            $date = request()->submmission_date;
            $formatted_date = Carbon::createFromFormat('d/m/Y',$date)->format('Y-m-d');
            //dd($formatted_date);
            $q->where('submmission_date',$formatted_date);
        })->latest();
    }

    /*******************************
     ** SAVING
     *******************************/
    public function handleStore($request,$statusOnly = false){
        $this->beginTransaction();
        try {
            
            $coa = Aset::where('id',$request->kib_id)->value('coa_id');
            $flag1 = Aset::where('coa_id',$coa)->where('status','notactive')->first();
            // dd($request->qty);
            if(($flag1->no_factory_item != null && $request->qty > 1) || ($flag1->no_frame != null && $request->qty > 1)){
                if($flag1->no_frame != null && $request->qty > 1 ){
                    return $this->rollback(
                        [
                            'message' => 'Aset dengan nomor rangka '.$flag1->no_factory_item.' Hanya ada 1'
                        ]
                    );
                }elseif($flag1->no_factory_item != null && $request->qty > 1 ){
                    return $this->rollback(
                        [
                            'message' => 'Aset dengan nomor rangka '.$flag1->no_factory_item.' Hanya ada 1'
                        ]
                    );
                }
            }

            
            $idMax = Pemutihans::count(); // Menghitung jumlah baris dalam tabel Pemutihans
            $merek = Aset::where('coa_id', $coa)->pluck('merek_type_item')->first(); // Menggunakan first() untuk mengambil nilai pertama jika ada
            $name = AsetRs::where('id', $coa)->pluck('name')->first(); // Menggunakan first() untuk mengambil nilai pertama jika ada

            $flag = Aset::where('coa_id', $coa)
                        ->where('merek_type_item', $merek)
                        ->where('condition', 'rusak berat')
                        ->where('status', 'notactive')
                        ->count();

            // dd($request->all());
            if($request->qty > $flag  ){
                return $this->rollback(
                    [
                        'message' => 'Jumlah yang ada hanya '.$flag.''
                    ]
                );
            }

            $time = Carbon::createFromFormat('d/m/Y', $request->submmission_date); // Membuat objek Carbon dengan format tanggal yang benar

            $format_angka = str_pad(($idMax + 1) < 10 ? '0' . ($idMax + 1) : ($idMax + 1), 3, '0', STR_PAD_LEFT); // Menggunakan operasi penjumlahan dengan nilai integer untuk mendapatkan nomor urut yang benar

            $this->code = $format_angka."/Pemutihan Aset/".$name."/".$merek."/".$time->day."/".$time->month."/".$time->year; // Menghapus indeks [0] pada $name karena pluck() sudah mengembalikan nilai tunggal

            $value4 = str_replace(['.', ','], '', $request->valued);
            // dd('tes');
            $this->kib_id = $request->kib_id;
            $this->qty = $request->qty;
            $this->target = $request->target;
            $this->location = $request->location;
            // $this->valued = $request->valued;
            $this->pic = $request->pic;
            $this->status = 'draft';
            $this->clean_type = $request->clean_type;

            $this->valued = (int)$value4;
            $this->submmission_date = $time;
            $this->save();

            $coa = Aset::where('id',$request->kib_id)->value('coa_id');
            Aset::where('coa_id',$coa)->where('merek_type_item',$merek)->where('condition','rusak berat')->where('status','notactive')->limit($request->qty)->update(['status'=>'in cleaned']);
            
            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');

            
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

    public function handleStoreOrUpdate($request, $statusOnly = false)
    {
        $this->beginTransaction();
        try {
            $coa = Aset::where('id',$request->kib_id)->value('coa_id');
            $flag1 = Aset::where('coa_id',$coa)->where('status','notactive')->first();
            // dd($request->qty);
            
            if($request->is_submit != 1 ){
                if(($flag1->no_factory_item != null && $request->qty > 1) || ($flag1->no_frame != null && $request->qty > 1)){
                    if($flag1->no_frame != null && $request->qty > 1 ){
                        return $this->rollback(
                            [
                                'message' => 'Aset dengan nomor rangka '.$flag1->no_factory_item.' Hanya ada 1'
                            ]
                        );
                    }elseif($flag1->no_factory_item != null && $request->qty > 1 ){
                        return $this->rollback(
                            [
                                'message' => 'Aset dengan nomor rangka '.$flag1->no_factory_item.' Hanya ada 1'
                            ]
                        );
                    }
                }
            }

            // dd($request->all());


            if($request->is_submit == 1 ){
                $idMax = Pemutihans::count(); // Menghitung jumlah baris dalam tabel Pemutihans
                $merek = Aset::where('coa_id', $coa)->pluck('merek_type_item')->first(); // Menggunakan first() untuk mengambil nilai pertama jika ada
                $name = AsetRs::where('id', $coa)->pluck('name')->first(); // Menggunakan first() untuk mengambil nilai pertama jika ada
                
                if($request->qty != $this->qty){{
                    $flag = Aset::where('coa_id', $coa)
                                ->where('merek_type_item', $merek)
                                ->where('condition', 'rusak berat')
                                ->where('status', 'notactive')
                                ->count();
        
                    // dd($request->all());
                    if($request->qty > $flag  ){
                        return $this->rollback(
                            [
                                'message' => 'Jumlah yang ada hanya '.$flag.''
                            ]
                        );
                    }
                }}

                $data = $this->code;
                $tanggal_baru = $request->submmission_date;  // Tanggal baru yang ingin Anda update

                // Pisahkan string menjadi bagian-bagian yang terpisah berdasarkan tanda '/'
                $bagian = explode('/', $data);

                // Ganti tanggal pada indeks ke-4 (indeks dimulai dari 0)
                $bagian[4] = $tanggal_baru;

                // Gabungkan kembali bagian-bagian menjadi string utuh
                $code = implode('/', $bagian);

                $time = Carbon::createFromFormat('d/m/Y', $request->submmission_date); // Membuat objek Carbon dengan format tanggal yang benar

                // $format_angka = str_pad(($idMax + 1) < 10 ? '0' . ($idMax + 1) : ($idMax + 1), 3, '0', STR_PAD_LEFT); // Menggunakan operasi penjumlahan dengan nilai integer untuk mendapatkan nomor urut yang benar

                $this->code = $code;

                $value4 = str_replace(['.', ','], '', $request->valued);
                // dd('tes');
                $last_qty = $this->qty;

                $this->kib_id = $request->kib_id;
                $this->qty = $request->qty;
                $this->target = $request->target;
                $this->location = $request->location;
                // $this->valued = $request->valued;
                $this->pic = $request->pic;
                $this->status = 'draft';
                $this->clean_type = $request->clean_type;

                $this->valued = (int)$value4;
                $this->submmission_date = $time;
                $this->save();
                //dd($request->all());

            //  $coa = Aset::where('id',$request->kib_id)->value('coa_id');
                if($last_qty != $request->qty){
                    Aset::where('coa_id',$coa)->where('merek_type_item',$merek)->where('condition','rusak berat')->where('status','notactive')->limit($last_qty)->update(['status'=>'notactive']);
                    Aset::where('coa_id',$coa)->where('merek_type_item',$merek)->where('condition','rusak berat')->where('status','notactive')->limit($request->qty)->update(['status'=>'in cleaned']);
                }
            }
            
            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');

            if($request->is_submit == 1 ){
                $this->handleSubmitSave($request);
            }

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
                        $coa = Aset::where('id',$this->kib_id)->value('coa_id');
                        $merek = Aset::where('coa_id', $coa)->where('id',$this->kib_id)->where('status','in cleaned')->pluck('merek_type_item')->first();
                        $no_frame = Aset::where('coa_id', $coa)->where('id',$this->kib_id)->where('status','in cleaned')->pluck('no_frame')->first(); 
                        $no_factory_item = Aset::where('coa_id', $coa)->where('id',$this->kib_id)->where('status','in cleaned')->pluck('no_factory_item')->first();
                        $no_machine_item = Aset::where('coa_id', $coa)->where('id',$this->kib_id)->where('status','in cleaned')->pluck('no_machine_item')->first();   
                        
                        Aset::where('coa_id', $coa)
                        ->where('merek_type_item', $merek)
                        ->where('condition', 'rusak berat')
                        ->where('status', 'in cleaned')
                        ->where('no_frame',$no_frame)
                        ->where('no_factory_item', $no_factory_item)
                        ->where('no_machine_item',$no_machine_item)
                        ->limit($this->qty)
                        ->update(['condition'=>'rusak berat','status'=>'clean']);

                        // dd('tes');
                        $this->update(['status' => 'completed']);
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
        $data = 'Pengajuan Pemutihan Aset dengan No Surat : ' . $this->code;
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
        $chatId = '-4144015581'; // Ganti dengan chat ID penerima notifikasi

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
