<?php

namespace App\Models\Transaksi;

use App\Models\Model;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Master\Aset\AsetRs;
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
// use App\Models\Traits\HasFiles;
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
        'source_acq',
        'jenis_pengadaan_id',
        'no_spk',
        'spk_start_date',
        'spk_end_date',
        'spk_range_time',
        'budget_limit',
        'qty',
        'unit_cost',
        'shiping_cost',
        'tax_cost',
        'total_cost',
        'receipt_date',
        'faktur_code',
        'location_receipt',
        'status',
        'sp2d_date',
        'sp2d_code',
        'asset_test_results',
        'location_receipt',
    ];

    protected $casts = [
        'spk_start_date'        => 'date',
        'spk_end_date'          => 'date',
        'receipt_date'          => 'date',
        'sp2d_date'             => 'date',
    ];

    /*******************************
     ** MUTATOR
     *******************************/
    public function setSpkStartDateAttribute($value)
    {
        $this->attributes['spk_start_date'] = Carbon::createFromFormat('d/m/Y', $value);
    }

    public function setSpkEndDateAttribute($value)
    {
        $this->attributes['spk_end_date'] = Carbon::createFromFormat('d/m/Y', $value);
    }
    public function setReceiptDateAttribute($value)
    {
        $this->attributes['receipt_date'] = Carbon::createFromFormat('d/m/Y', $value);
    }

    public function setSP2DDateAttribute($value)
    {
        $this->attributes['sp2d_date'] = Carbon::createFromFormat('d/m/Y', $value);
    }

    public function setTotalCostAttribute($value = '')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['total_cost'] = (int) $value;
    }

    public function setUnitCostAttribute($value = '')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['unit_cost'] = (int) $value;
    }

    public function setQtyAttribute($value = '')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['qty'] = (int) $value;
    }

    public function setBudgetLimitUnitCost($value ='')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['budget_limit'] = (int) $value;
    }

    public function setTaxCostAttribute($value='')
    {
        
        $value = str_replace(['.',','],'',$value);
        $this->attributes['tax_cost'] = (int)$value;
    }

    public function setShipingCostAttribute($value='')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['shiping_cost'] = (int) $value;
    }


    /*******************************
     ** RELATION
     *******************************/
    
    public function perencanaanPengadaan()
    {
        return $this->belongsToMany(PerencanaanDetail::class, 'trans_pivot_perencanaan_pengadaan','pembelian_id' ,'detail_usulan_id');
    }

    public function pengujianPengadaan()
    {
        return $this->belongsToMany(User::class, 'trans_pivot_pengujian','trans_id','user_id');
    }

    public function vendors()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function pengadaans()
    {
        return $this->belongsTo(Pengadaan::class, 'jenis_pengadaan_id');
    }

    public function usulans()
    {
        return $this->hasMany(PerencanaanDetail::class,'trans_id');
    }

    // public function details()
    // {
    //     return $this->hasMany(PerencanaanDetail::class, 'trans_id');
    // }

     // public function cc()
    // {
    //     return $this->belongsToMany(User::class, 'trans_pengajuan_pembelian_cc', 'perencanaan_id', 'user_id');
    // }

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
        return $query->filterBy(['no_spk','status'])
            ->when(request()->vendor_id, function ($q) {
                $q->whereHas('vendors', function ($qq) {
                    $qq->where('id', request()->vendor_id);
                });
            })
            ->when(request()->spk_start_date, function ($q) {
                $date = request()->spk_start_date;
                $formatted_date = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                $q->where('spk_start_date',$formatted_date);
            })
            ->when(request()->spk_end_date, function ($q) {
                $date = request()->spk_end_date;
                $formatted_date = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                $q->where('spk_end_date',$formatted_date);
            })
            ->when(request()->receipt_date, function ($q) {
                $date = request()->receipt_date;
                $formatted_date = Carbon::createFromFormat('d/m/Y',$date)->format('Y-m-d');
                //dd($formatted_date);
                $q->where('receipt_date',$formatted_date);
            })
            ->where('trans_name', 'LIKE', '%' . request()->trans_name . '%')
            ->latest();

    }

    /*******************************
     ** SAVING
     *******************************/
    // public function handleStoreOrUpdate($request)
    // {
    //     $this->beginTransaction();
    //     try { 
    //         if($request->unit_cost == 0){
    //             return $this->rollback(
    //                 [
    //                     'message' => 'Harga Unit Masih 0 !'
    //                 ]
    //             );
    //         }
    //         if($request->total_cost == 0){
    //             return $this->rollback(
    //                 [
    //                     'message' => 'Biaya Total Masih 0 !'
    //                 ]
    //             );
    //         }
    //         $data = $request->all();

    //         $this->fill($data);

    //         $value1 = str_replace(['.', ','],'',$request->unit_cost);
    //         $this->unit_cost = (int)$value1;
    //         $value2 = str_replace(['.', ','],'',$request->tax_cost);
    //         $this->tax_cost = (int)$value2;

    //         $value3 = str_replace(['.', ','],'',$request->shiping_cost);
    //         $this->shiping_cost = (int)$value3;

    //         $value4 = str_replace(['.', ','],'',$request->budget_limit);
    //         $this->budget_limit = (int)$value4;

    //         $value5 = str_replace(['.', ','],'',$request->total_cost);
    //         $this->total_cost = (int)$value5;

    //         $value6 = str_replace(['.', ','],'',$request->qty);
    //         $this->qty = (int)$value6;
    //         $start_time = Carbon::createFromFormat('d/m/Y', $request->spk_start_date);
    //         $end_time = Carbon::createFromFormat('d/m/Y', $request->spk_end_date);
    //         $selisih = $start_time->diffInDays($end_time);
    //         $this->spk_range_time = $selisih;
    //         $this->save();
    //         $dataArray = json_decode($request->usulan_id, true);

    //         $this->perencanaanPengadaan()->sync($dataArray ?? []);

    //         //$this->jenisUsaha()->sync($request->user_id);
    //         $this->pengujianPengadaan()->sync($request->user_id ?? []);

    //         if ($dataArray) {
    //             PerencanaanDetail::whereIn('id', $dataArray)->update(['status' => 'waiting receipt']);
    //         }

    //         if ($request->is_submit == 1) {
    //             $this->handleSubmitSave($request);
    //         }
    //         $redirect = route('transaksi.pengadaan-aset' . '.index');
    //         return $this->commitSaved(compact('redirect'));
    //     } catch (\Exception $e) {
    //       //  Log::error('Kesalahan: ' . $e->getMessage());
    //         return $this->rollbackSaved($e->getMessage());
    //     }
    // }

    public function handleStoreHibah($request){
        $this->beginTransaction();
        try {

            if ($request->is_submit == 1) {
                if($request->asset_test_results == null){
                    return $this->rollback(
                        [
                            'message' => 'Silahkan Lengkapi Hasil Pengujian!'
                        ]
                    );
                }

                if($request->user_id == null){
                    return $this->rollback(
                        [
                            'message' => 'Silahkan Lengkapi Data Penguji!'
                        ]
                    );
                }
                $data = $request->all();
                $this->fill($data);
                $this->save();
                $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
                $this->pengujianPengadaan()->sync($request->user_id ?? []);
                $this->saveLogNotify();
                $this->handleSubmitSave($request);
            }else{
                $data = $request->all();
                $this->fill($data);
                $this->save();
                $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
                if($request->user_id != null){
                    $this->pengujianPengadaan()->sync($request->user_id ?? []);
                }
                $this->saveLogNotify();
            }
            

            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleDetailStoreOrUpdateHibah($request, PerencanaanDetail $detail)
    {
        $this->beginTransaction();
        try {
           // dd($detail->id);
            $flag = PerencanaanDetail::where('trans_id', $this->id)->where('ref_aset_id',$request->ref_aset_id)->where('id','!=',$detail->id)->count();
            
            // dd($flag);
            if($flag > 0){
                return $this->rollback(
                    [
                        'message' => 'Aset Sudah Dicatat Untuk Penerimaan Ini!'
                    ]
                );
            }
            
            $detail->fill($request->all());

            $value1 = str_replace(['.', ','],'',$request->HPS_unit_cost);
            $detail->HPS_unit_cost = (int)$value1;


            $value6 = str_replace(['.', ','],'',$request->qty_agree);
            $detail->qty_agree = (int)$value6;

            
            if($request->is_submit == 1){

                $detail->status = 'waiting receipt';

                $this->usulans()->save($detail);
                $this->save();
                $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
                $this->pengujianPengadaan()->sync($request->user_id ?? []);
                
                return $this->commitSaved();
                
            }else{
                // dd($request->all());
                if($request->user_id != null){
                    $this->pengujianPengadaan()->sync($request->user_id ?? []);
                }

                $this->usulans()->save($detail);
                $this->save();
                $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
                return $this->commitSaved();
            }

        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
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


    public function handleStoreOrUpdate($request)
    {
        $this->beginTransaction();
        try { 
           //dd($request->all());
            if($request->unit_cost == 0){
                return $this->rollback(
                    [
                        'message' => 'Harga Unit Masih 0 !'
                    ]
                );
            }

            if($request->total_cost == 0){
                return $this->rollback(
                    [
                        'message' => 'Biaya Total Masih 0 !'
                    ]
                );
            }
            $data = $request->all();

            $this->fill($data);

            // dd('tes');
            if($request->sp2d_code != null){
                $this->sp2d_code = $request->sp2d_code;
            }
            if($request->sp2d_date != null){
                $this->sp2d_date = $request->sp2d_date;
            }

            $value1 = str_replace(['.', ','],'',$request->unit_cost);
            $this->unit_cost = (int)$value1;

            $value2 = str_replace(['.', ','],'',$request->tax_cost);
            $this->tax_cost = (int)$value2;

            $value3 = str_replace(['.', ','],'',$request->shiping_cost);
            $this->shiping_cost = (int)$value3;

            $value4 = str_replace(['.', ','],'',$request->budget_limit);
            $this->budget_limit = (int)$value4;

            $value5 = str_replace(['.', ','],'',$request->total_cost);
            $this->total_cost = (int)$value5;

            $value6 = str_replace(['.', ','],'',$request->qty);
            $this->qty = (int)$value6;
            
            $start_time = Carbon::createFromFormat('d/m/Y', $request->spk_start_date);
            $end_time = Carbon::createFromFormat('d/m/Y', $request->spk_end_date);
            $selisih = $start_time->diffInDays($end_time);
            $this->spk_range_time = $selisih;
            
            $this->save();
            $dataArray = json_decode($request->usulan_id, true);
            PerencanaanDetail::whereIn('id',$dataArray)->update(['trans_id' => $this->id]);
            $this->pengujianPengadaan()->sync($request->user_id ?? []);
            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');
            
            if ($dataArray) {
                PerencanaanDetail::whereIn('id', $dataArray)->update(['status' => 'waiting receipt']);
            }

            if ($request->is_submit == 1) {
                $this->handleSubmitSave($request);
            }else{
                $module='transaksi_waiting-purchase';
                $this->addLog('Memperbarui ' . $request->trans_name);
                $this->logs()->whereModule($module)->latest()->update(['module'=>'transaksi_pengadaan-aset']);
            }
            
            
            $redirect = route('transaksi.pengadaan-aset' . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
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


    // public function getPerencanaanPengadaan($record)
    // {
    //     $usulan = DB::table('trans_pivot_perencanaan_pengadaan')
    //     ->where('pembelian_id', $record)
    //     ->pluck('detail_usulan_id');

    //     $usulan_id = collect($usulan)->flatten()->toArray();
    //     $pagu = PerencanaanDetail::whereIn('id',$usulan_id)->sum('HPS_total_agree');
    //     $jumlah_beli = PerencanaanDetail::whereIn('id',$usulan_id)->sum('qty_agree');

    //     $data = [
    //         'usulan_id' => $usulan_id,
    //         'pagu' => $pagu,
    //         'jumlah_beli' => $jumlah_beli
    //     ];

    //     $this->budget_limit = $pagu;
    //     $this->qty= $jumlah_beli;
    //     $this->total_cost = $this->qty * $this->unit_cost + $this->tax_cost + $this->shiping_cost;
    //     $this->save();
    
    //     return $data;
    // }

    public function getPerencanaanPengadaan($record)
    {
        // dd($record);
        $usulan = PerencanaanDetail::where('trans_id', $record)
        ->pluck('id');

        $usulan_id = collect($usulan)->flatten()->toArray();

        $pagu = PerencanaanDetail::where('trans_id',$record)->sum('HPS_total_agree');
        $jumlah_beli = PerencanaanDetail::where('trans_id',$record)->sum('qty_agree');

        $data = [
            'usulan_id' => $usulan_id,
            'pagu' => $pagu,
            'jumlah_beli' => $jumlah_beli
        ];

        $this->budget_limit = $pagu;
        $this->qty= $jumlah_beli;
        $this->total_cost = $this->qty * $this->unit_cost + $this->tax_cost + $this->shiping_cost;
        $this->save();
        
        return $data;
    }

    // public function handleDestroy()
    // {
    //     $this->beginTransaction();
    //     try {
    //         $pivotIds = $this->perencanaanPengadaan()->pluck('detail_usulan_id');
    //         PerencanaanDetail::whereIn('id', $pivotIds)->update(['status' => 'waiting purchase']);

    //         $this->pengujianPengadaan()->wherePivot('trans_id', '=', $this->id)->detach();
    //         $this->perencanaanPengadaan()->wherePivot('pembelian_id', '=', $this->id)->detach();
    //         $this->saveLogNotify();
    //         $this->delete();

    //         return $this->commitDeleted();
    //     } catch (\Exception $e) {
    //         return $this->rollbackDeleted($e);
    //     }
    // }

    public function handleDestroy()
    {
        $this->beginTransaction();
        try {

            // $pivotIds = $this->perencanaanPengadaan()->pluck('detail_usulan_id');
            PerencanaanDetail::where('trans_id', $this->id)->update(['status' => 'waiting purchase','trans_id' => NULL]);

            // $this->pengujianPengadaan()->wherePivot('trans_id', '=', $this->id)->detach();
            // $this->perencanaanPengadaan()->wherePivot('pembelian_id', '=', $this->id)->detach();
            $this->saveLogNotify();
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
                        $this->saveLogNotify();
                    } else {
                        // dd($request->all());
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
                    // $data = $this->perencanaanPengadaan('detail_usulan_id');
                    // $detailUsulan = $this->perencanaanPengadaan()->where('pembelian_id', $this->id)->get()->pluck('pivot.detail_usulan_id')->toArray();
                    PerencanaanDetail::where('trans_id',$this->id)->update(['status' => 'waiting register']);
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
        $data = $this->trans_name;
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
                $this->addLog('Memperbarui ' . $data);
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
        $chatId = '-4161016242'; // Ganti dengan chat ID penerima notifikasi

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
