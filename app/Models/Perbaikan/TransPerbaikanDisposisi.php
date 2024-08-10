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
        'submission_date',
        'procurement_year',
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
        'spm_code',
        'total_cost_vendor',
        'ppk_id'
    ];

    protected $casts = [
        'spk_start_date' => 'date',
        'spk_end_date' => 'date',
        'receipt_date' => 'date',
        'submission_date' => 'date',
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
        return $query->when(
            empty(array_intersect(['Sarpras', 'BPKAD'], $user->roles->pluck('name')->toArray())),
            function ($q) use ($user) {
                // $q->whereHas('approvals', function ($q) use ($user) {
                //     $q->when($user->id, function ($qq) use ($user) {
                //         $qq->whereIn('role_id', $user->getRoleIds())->where('status', 'new');
                //     }, function ($qq) use ($user) {
                //         $qq->orWhereIn('role_id', $user->getRoleIds())
                //         ->orWhere('position_id', $user->position->id);
                //     });
                // });
        
                $q->when($user->position->imKepalaDeparetemen(), function ($qq) use ($user) {
                    $qq->whereHas('codes', function ($qqq) use ($user){
                        return $qqq->whereIn('departemen_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
                    });
                }, function ($qq) use ($user) {
                    $qq->whereHas('codes', function ($qqq) use ($user){
                        // return $qqq->whereIn('departemen_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
                        return $qqq->where('departemen_id', $user->position->location->id);
                    });
                });
            })
            ->when(auth()->user()->position->location->name == 'Sub Bagian Program Perencanaan dan Pelaporan', function ($query) {
                $query->orWhereHas('approvals', function ($q) {
                    $q->where('order', 2)->orWhere('order',1)->where('status', 'approved');
                });
            })
            ->when(auth()->user()->position->location->name == 'Bidang Penunjang Medik dan Non Medik', function ($query) {
                $query->orWhereHas('approvals', function ($q) {
                    $q->where('order', 1)->where('status', 'approved');
                });
            })
            ->when(auth()->user()->position->location->name == 'Sub Bagian Keuangan', function ($query) {
                $query->orWhereHas('approvals', function ($q) {
                    $q->where('module','trans-sperpat')->where('order', 1)->where('status', '<>','approved');
                });
            })->when(auth()->user()->position->location->name == 'Direksi RSUD', function ($query) {
                $query->orWhereHas('approvals', function ($q) {
                    $q->where('module','trans-sperpat')->where('order', 1)->where('status','approved');
                });
            });
    }


    public function scopeGridSperpat($query)
    {
        $user = auth()->user();
        return $query->when(
            empty(array_intersect(['Sarpras', 'BPKAD'], $user->roles->pluck('name')->toArray())),
            function ($q) use ($user) {
                $q->when($user->position->imKepalaDeparetemen(), function ($qq) use ($user) {
                    $qq->whereHas('codes', function ($qqq) use ($user){
                        return $qqq->whereIn('departemen_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
                    });
                }, function ($qq) use ($user) {
                    $qq->whereHas('codes', function ($qqq) use ($user){
                        // return $qqq->whereIn('departemen_id', $user->position->location->getIdsWithChild()); //ambil anak dan kepala departemen
                        return $qqq->where('departemen_id', $user->position->location->id);
                    });
                });
            }
        )
        ->when(auth()->user()->position->location->name == 'Sub Bagian Program Perencanaan dan Pelaporan', function ($query) {
            $query->orWhereHas('approvals', function ($q) {
                $q->where('order',1)->where('module','usulan_pembelian-sperpat')->where('status', 'approved');
            });
        })
        ->when(auth()->user()->position->location->name == 'Sub Bagian Program Perencanaan dan Pelaporan', function ($query) {
            $query->orWhereHas('approvals', function ($q) {
                $q->where('order',2)->where('module','usulan_pembelian-sperpat-umum')->where('status', 'approved');
            });
        })
        ->when(auth()->user()->roles->pluck('id')->contains(2), function ($query) {
            $query->orWhereHas('approvals', function ($q) {
                $q->where('role_id', 3)->where('status', 'approved');
            });
        })
        ->when(auth()->user()->position->location->name == 'Bidang Penunjang Medik dan Non Medik', function ($query) {
            $query->orWhereHas('approvals', function ($q) {
                $q->where('order', 1)->where('module','usulan_pembelian-sperpat-umum')->where('status', 'approved');
            });
        });
    }

    public function scopeGridStatusCompleted($query)
    {
        return $query->where('status', 'completed')->latest();
    }

    public function scopeFilters($query)
    {
        return $query
        ->filterBy(['vendor_id','repair_type','status','sper_status'])
        ->when($codes = request()->code,
            function ($q) use ($codes){
                $q->whereHas('codes', function ($qq) use ($codes){
                        $qq->where('code',$codes);
            });
        })
        ->when(
            $tahun_usulan = request()->procurement_year,
            function ($q) use ($tahun_usulan){
                $q->where('procurement_year',$tahun_usulan);
        })->latest();
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
                        'message' => 'Periode Usulan Sperpat Sudah Lewat!'
                    ]
                );
            }

            $data = $request->all();
            $flag = TransPerbaikanDisposisi::where('perbaikan_id',$request->perbaikan_id)->where('repair_type',$request->repair_type)->where('vendor_id',$request->vendor_id)->count();
            if($flag > 0){
                return $this->rollback(
                    [
                        'message' => 'Usulan Pembelian Sperpat Pada Vendor Ini Sudah Tersedia'
                    ]
                );
            }

            // dd($request->all());
            $dep = Perbaikan::where('id',$request->perbaikan_id)->value('departemen_id');
            $parent = OrgStruct::where('id',$dep)->value('parent_id');

            if($parent == 3 || $dep == 3){
                $module = 'usulan_pembelian-sperpat';
            }else{
                $module = 'usulan_pembelian-sperpat-umum';
            }

            $this->fill($data);
            $time = now()->format('Y-m-d');
            $this->submission_date =  $time;
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
            if($this->repair_type == 'vendor' || $this->repair_type == 'sperpat dan vendor' ){
                if($request->total_cost_vendor == null){
                    return $this->rollback(
                        [
                            'message' => 'Biaya Jasa Vendor Wajib Diisi!'
                        ]
                    );
                }

                $total = str_replace(['.', ','], '', $request->total_cost);
                $this->total_cost= (int)$total;

                $total_2 = str_replace(['.', ','], '', $request->total_cost_vendor);
                $this->total_cost_vendor= (int)$total_2;
            }
            



            // if($this->status == 'completed'){
            //     // dd('tes');
            //     $tax = str_replace(['.', ','], '', $request->tax_cost);
            //     $shiping = str_replace(['.', ','], '', $request->shiping_cost);
            //     $total = str_replace(['.', ','], '', $request->total_cost);
            //     $this->tax_cost = (int)$tax;
            //     $this->shiping_cost = (int)$shiping;
            //     $this->total_cost = (int)$total + (int)$shiping + (int)$tax;
            //     $this->spk_start_date = $spk_start;
            //     $this->spk_end_date = $spk_start;
            //     $this->spk_range_time = $selisih;
            //     $this->receipt_date = $receipt;

            //     if($this->repair_type == 'vendor'){
            //         $total = str_replace(['.', ','], '', $request->total_cost);
            //         $this->total_cost= (int)$total;
            //     }
           // }


            if($request->module == 'trans-sperpat' && $this->status == 'completed'){
                
                $spk_start = Carbon::createFromFormat('d/m/Y', $request->spk_start_date);
                $spk_end = Carbon::createFromFormat('d/m/Y', $request->spk_end_date);
                $receipt = Carbon::createFromFormat('d/m/Y', $request->receipt_date);
                $selisih = $spk_start->diffInDays($spk_end);
                
                $tax = str_replace(['.', ','], '', $request->tax_cost);
                $shiping = str_replace(['.', ','], '', $request->shiping_cost);
                
                $this->tax_cost = (int)$tax;
                $this->shiping_cost = (int)$shiping;
                
                
                if($request->repair_type == 'sperpat dan vendor' ){
                    $total_v = str_replace(['.', ','], '', $request->total_cost_vendor);
                    $total_s = str_replace(['.', ','], '', $request->ts_cost);
                    $this->total_cost = (int)$total_v + (int)$total_s + (int)$shiping + (int)$tax;
                    
    
                }elseif($this->repair_type == 'vendor') {
                    $total_v = str_replace(['.', ','], '', $request->total_cost_vendor);
                    $this->total_cost= (int)$total_v + (int)$shiping + (int)$tax;
                    # code...
                }else{
                    $total = str_replace(['.', ','], '', $request->ts_cost);
                    $this->total_cost= (int)$total + (int)$shiping + (int)$tax;
                }

                $this->spk_start_date = $spk_start;
                $this->spk_end_date = $spk_start;
                $this->spk_range_time = $selisih;
                $this->receipt_date = $receipt;

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


    //kondisi aset
    private $damageWeights = [
        'Rusak Berat' => 4,
        'Rusak Ringan' => 2
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
    
    public function calculateUtilityScore($assets) {
        $damageWeight = $this->getDamageWeight($asset['condition']);
        $valueWeight = $this->getValueWeight($asset['value']);
        $economicLifeWeight = $this->getEconomicLifeWeight($asset['economic_life']);
        $asset['utility_score'] = ($damageWeight * 0.4) + ($valueWeight * 0.4) + ($economicLifeWeight * 0.2);
        return $assets;
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
    

    public function handleStoreOrUpdateHarga($request, $statusOnly = false)
    {
        $this->beginTransaction();
        try {

            $data = $request->all();
            // dd($data);
            $this->fill($data);

            if($this->repair_type == 'vendor' || $this->repair_type == 'sperpat dan vendor' ){
                if($request->total_cost_vendor == null || $request->total_cost_vendor <= 0 ){
                    return $this->rollback(
                        [
                            'message' => 'Biaya Jasa Vendor Wajib Diisi!'
                        ]
                    );
                }

                $total_2 = str_replace(['.', ','], '', $request->total_cost_vendor);
                $this->total_cost_vendor= (int)$total_2;
            }
            

            $this->saveFilesByTemp($request->uploads, $request->module, 'uploads');

            $this->save();
            $this->saveLogNotify();
            
            
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
            // $ts = str_replace(['.', ','], '', $request->ts_cost);
            
            //dd($request->all());
            if($request->repair_type == 'sperpat dan vendor' ){
                $total_v = str_replace(['.', ','], '', $request->ts_cost_vendor);
                $total_s = str_replace(['.', ','], '', $request->ts_cost);
                $total = (int)$total_v + (int)$total_s + (int)$shiping + (int)$tax;
                $this->total_cost_vendor = (int)$total_v;

            }elseif($this->repair_type == 'vendor') {
                $total_v = str_replace(['.', ','], '', $request->ts_cost_vendor);
                $total= (int)$total_v + (int)$shiping + (int)$tax;
                $this->total_cost_vendor = (int)$total_v;
                # code...
            }else{
                $ts = str_replace(['.', ','], '', $request->ts_cost);
                $total= (int)$ts + (int)$shiping + (int)$tax;
                $this->total_cost_vendor = 0;
                # code...
            }

            // dd(auth()->user()->roles->pluck('id')->contains(8), auth()->user()->id);
            if(auth()->user()->roles->pluck('id')->contains(8)){
                $this->ppk_id = auth()->user()->id;
            }else{
                $this->ppk_id = null;
            }

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
            $this->spm_code = $request->spm_code;

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


    public function handleDetailStoreOrUpdateHarga($request, UsulanSperpat $detail)
    {
        $this->beginTransaction();
        try {

            if($request->unit_cost == null || $request->unit_cost <= 0){
                return $this->rollback(
                    [
                        'message' => 'Harga Unit Harus Diisi!'
                    ]
                );
            }

            $unit_cost = str_replace(['.', ','], '', $request->unit_cost);
            $unit_cost = (int)$unit_cost; // Konversi ke integer atau bisa juga float tergantung kebutuhan

            $detail->unit_cost = $unit_cost;
            
            $qty = $detail->qty; 
            $tot_harga = $qty * $unit_cost;

            $detail->save(); // Simpan detail sebelum mengupdate relasi

            $this->details()->where('id',$detail->id)->update(['total_cost' => $tot_harga]); // Pastikan ini adalah metode yang benar untuk update relasi
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
            $dep =  Perbaikan::where('id',$this->perbaikan_id)->value('departemen_id');
            $parent = OrgStruct::where('id',$dep)->value('parent_id');

            if($parent == 3 || $dep == 3){
                $module = 'usulan_pembelian-sperpat';
            }else{
                $module = 'usulan_pembelian-sperpat-umum';
            }

            $this->rejectApproval($module, $request->note);
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
            $dep = Perbaikan::where('id',$this->perbaikan_id)->value('departemen_id');
            $parent = OrgStruct::where('id',$dep)->value('parent_id');

            if($parent == 3 || $dep == 3){
                $module = 'usulan_pembelian-sperpat';
            }else{
                $module = 'usulan_pembelian-sperpat-umum';
            }

            $this->generateApproval($module);
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
                    $dep =  Perbaikan::where('id',$this->perbaikan_id)->value('departemen_id');
                    $parent = OrgStruct::where('id',$dep)->value('parent_id');

                    if($parent == 3 || $dep == 3){
                        $module = 'usulan_pembelian-sperpat';
                    }else{
                        $module = 'usulan_pembelian-sperpat-umum';
                    }

                    $this->approveApproval($module);

                    $p1 = $this->whereHas('approvals', function ($q) {
                        $q->where('target_id',$this->id)->where('module','usulan_pembelian-sperpat')->where('role_id',3)->where('status','!=','approved')->where('order',2);
                    })->count();

                    $p2 = $this->whereHas('approvals', function ($q){
                        $q->where('target_id',$this->id)->where('module','usulan_pembelian-sperpat-umum')->where('role_id',5)->where('status','!=','approved')->where('order',2);
                    })->count();

                    $p3 = $this->whereHas('approvals', function ($q){
                        $q->where('target_id',$this->id)->where('module','usulan_pembelian-sperpat-umum')->where('role_id',3)->where('status','!=','approved')->where('order',3);
                    })->count();

                    $p4 = $this->whereHas('approvals', function ($q){
                        $q->where('target_id',$this->id)->where('module','usulan_pembelian-sperpat-umum')->where('role_id',2)->where('status','!=','approved')->where('order',4);
                    })->count();

                    $p5 = $this->whereHas('approvals', function ($q){
                        $q->where('target_id',$this->id)->where('module','usulan_pembelian-sperpat')->where('role_id',2)->where('status','!=','approved')->where('order',3);
                    })->count();

                    if($p4 != 0 && $p3 == 0 && $p2 == 0 && $module == 'usulan_pembelian-sperpat-umum' && $request->source_fund_id == null){
                        return $this->rollback(
                            [
                                'message' => 'Sumber Pendanaan Harus Diisi!'
                            ]
                        );
                    }elseif($p5 != 0 && $p1 == 0 && $module == 'usulan_pembelian-sperpat' && $request->source_fund_id == null){
                        //dd($p2);
                        return $this->rollback(
                            [
                                'message' => 'Sumber Pendanaan Harus Diisi!'
                            ]
                        );
                    }else{
                        if($request->source_fund_id != null){
                            $this->update(['source_fund_id' => $request->source_fund_id]);
                        }
                        if ($this->firstNewApproval($module)) {
                            $this->update(['sper_status' => 'waiting.approval']);
                            $this->saveLogNotify();
                        } else {
                            $this->update(['sper_status' => 'completed']);
                            $this->saveLogNotify();
                        }
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
        if($this->status == 'draft'){
            $data = 'Pengajuan Usulan Sperpat Aset dengan No Surat : ' . $this->codes->code;
        }else{
            $data = 'Pengajuan Transaksi Sperpat Aset dengan No Surat : ' . $this->codes->code;
        }
        $routes = request()->get('routes');
      //  dd(request()->route()->getName());
        switch (request()->route()->getName()) {
            case $routes . '.store':
                $this->addLog('Membuat ' . $data);
                $pesan = $user.' Membuat ' . $data;
                $this->sendNotification($pesan);
                if($this->status == 'draft'){
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
                }else{
                    if (request()->is_submit) {
                        $this->addLog('Submit ' . $data);
                        
                        $this->addNotify([
                            'message' => 'Waiting Verify ' . $data,
                            'url' => route($routes . '.approval', $this->id),
                            'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                        ]);
    
                        $pesan = $user.' Menunggu Verifikasi ' . $data;
                        $this->sendNotification($pesan);
                    }
                    break;
                }
            case $routes . '.update':

                $this->addLog('Memperbarui ' . $data);
                if($this->status == 'draft'){
                    
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
                }else{
                    if (request()->is_submit == 1) {
                        $this->addLog('Submit ' . $data);
                        $this->addNotify([
                            'message' => 'Waiting Verify ' . $data,
                            'url' => route($routes . '.approval', $this->id),
                            'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                        ]);
    
                        $pesan = $user.' Menunggu Verifikasi ' . $data;
                        $this->sendNotification($pesan);
                    }
                    break;
                }
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
                if($this->status == 'draft'){
                    if (in_array($this->status, ['draft', 'waiting.approval.revisi'])) {
                        $this->addLog(' Menyetujui  ' . $data);
    
                        $this->addNotify([
                            'message' => ' Menyetujui ' . $data,
                            'url' => route($routes . '.approval', $this->id),
                            'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                        ]);
    
                        $pesan = $user. ' Menyetujui ' . $data;
                        $this->sendNotification($pesan);
    
                    } else {
                        $this->addLog(' Menyetujui ' . $data);
    
                        $this->addNotify([
                            'message' => ' Menyetujui ' . $data,
                            'url' => route($routes . '.approval', $this->id),
                            'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                        ]);
                        $pesan = $user. ' Menyetujui ' . $data;
                        $this->sendNotification($pesan);
                    }
                    break;
                }else{
                    if (in_array($this->status, ['draft', 'waiting.approval.revisi'])) {
                        $this->addLog(' Memverifikasi  ' . $data);
    
                        $this->addNotify([
                            'message' => ' Memverifikasi ' . $data,
                            'url' => route($routes . '.approval', $this->id),
                            'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                        ]);
    
                        $pesan = $user. ' Memverifikasi ' . $data;
                        $this->sendNotification($pesan);
    
                    } else {
                        $this->addLog(' Memverifikasi ' . $data);
    
                        $this->addNotify([
                            'message' => ' Memverifikasi ' . $data,
                            'url' => route($routes . '.approval', $this->id),
                            'user_ids' => $this->getNewUserIdsApproval(request()->get('module')),
                        ]);
                        $pesan = $user. ' Memverifikasi ' . $data;
                        $this->sendNotification($pesan);
                    }
                    break;
                }

                
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

        //usulan pembelian sperpat
        $dep =  Perbaikan::where('id',$this->perbaikan_id)->value('departemen_id');
        $parent = OrgStruct::where('id',$dep)->value('parent_id');

        if($parent == 3 || $dep == 3){
            $module = 'usulan_pembelian-sperpat';
        }else{
            $module = 'usulan_pembelian-sperpat-umum';
        }

        // pembelian sperpat umum
        $approval1_u = $this->whereHas('approvals', function ($q) use ($module){
            $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('role_id',5)->where('order',1);
        })->count();

        $approval2_u = $this->whereHas('approvals', function ($q) use ($module){
            $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('role_id',5)->where('order',2);
        })->count();

        $approval3_u = $this->whereHas('approvals', function ($q) use ($module){
            $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('role_id',3)->where('order',3);
        })->count();

        //transaksi pembelian sperpat
        $approval1 = $this->whereHas('approvals', function ($q) use ($module) {
            $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('role_id',5)->where('order',1);
        })->count();

        $approval2 = $this->whereHas('approvals', function ($q) use ($module) {
            $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('role_id',3)->where('order',2);
        })->count();

        $kepala_dep = OrgStruct::where('id', $parent)->value('telegram_id');
        $chat_perencanaan = OrgStruct::where('name', 'Sub Bagian Program Perencanaan dan Pelaporan')->value('telegram_id');
        $penunjang = OrgStruct::where('name', 'Bidang Penunjang Medik dan Non Medik')->value('telegram_id');
        $chat_direksi = OrgStruct::where('name', 'Direksi RSUD')->value('telegram_id');
        $chat_keuangan = OrgStruct::where('name', 'Sub Bagian Keuangan')->value('telegram_id');
        $chat_ipsrs = OrgStruct::where('name', 'IPSRS')->value('telegram_id');
        $chatId = '-4136008848'; //grup notif perbaikan
        $chatPPK = '-4287618762';

        $send_chat = [];
        if($this->sper_status != 'completed'){
            if ($this->sper_status == 'draft') {
                $send_chat = array_filter([$chat_ipsrs]);
            }elseif ($this->sper_status == 'waiting.approval' && $approval1 > 0 && $module == 'usulan_pembelian-sperpat') { //verify tahap 1
                $send_chat = array_filter([$chat_ipsrs, $penunjang]);
                $pesan = $pesan.' '.' dan Kepada Departemen Penunjang Mohon Untuk Melakukan Approval Selanjutnya';
            }elseif ($this->sper_status == 'waiting.approval' && $approval1_u > 0 && $module == 'usulan_pembelian-sperpat-umum') { //verify tahap 1 umum
                $send_chat = array_filter([$chat_ipsrs, $kepala_dep]);
                $pesan = $pesan.' '.' dan Kepada Departemen Unit Mohon Untuk Melakukan Approval';
            } elseif ($this->sper_status == 'waiting.approval' && $approval1_u == 0 && $approval2_u > 0 && $module == 'usulan_pembelian-sperpat-umum') { //verify tahap 2 umum
                $send_chat = array_filter([$chat_ipsrs, $penunjang]);
                $pesan = $pesan.' '.' dan Kepada Departemen Penunjang Mohon Untuk Melakukan Approval Selanjutnya';
            } elseif ($this->sper_status == 'waiting.approval' && $approval1 == 0 && $approval2 > 0 && $module == 'usulan_pembelian-sperpat') { //verify tahap 2
                $send_chat = array_filter([$chat_ipsrs, $chat_perencanaan]);
                $pesan = $pesan.' '.' dan Kepada Unit Perencanaan Mohon Untuk Melakukan Approval Selanjutnya';
            } elseif ($this->sper_status == 'waiting.approval' && $approval1_u == 0 && $approval2_u == 0 && $approval3_u > 0 && $module == 'usulan_pembelian-sperpat-umum') { //verify tahap 2 umum
                $send_chat = array_filter([$chat_ipsrs, $chat_perencanaan]);
                $pesan = $pesan.' '.' dan Kepada Unit Perencanaan Mohon Untuk Melakukan Approval Selanjutnya';
            } elseif ($this->sper_status == 'waiting.approval' && $approval1_u == 0 && $approval2_u == 0 && $approval3_u == 0 && $module == 'usulan_pembelian-sperpat-umum') { //verify tahap 2 umum
                $send_chat = array_filter([$chat_ipsrs, $chat_direksi]);
                $pesan = $pesan.' '.' dan Kepada Direktur Mohon Untuk Melakukan Approval Selanjutnya';
            } elseif ($this->sper_status == 'waiting.approval' && $approval1 == 0 && $approval2 == 0 && $module == 'usulan_pembelian-sperpat') { //verify tahap 2
                $send_chat = array_filter([$chat_ipsrs, $chat_direksi]);
                $pesan = $pesan.' '.' dan Kepada Direktur Mohon Untuk Melakukan Approval Selanjutnya';
            }elseif($this->sper_status == 'rejected' && $module == 'usulan_pembelian-sperpat-umum' || $this->sper_status == 'rejected' && $module == 'usulan_pembelian-sperpat'){ //rejected ipsrs
                $send_chat = array_filter([$chat_ipsrs]);
            } else {
                $send_chat = array_filter([$chat_ipsrs]);
            }
        }else{
            $module = 'trans-sperpat';
            $approval1 = $this->whereHas('approvals', function ($q) use ($module) {
                $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('order',1);
            })->count();
    
            $approval2 = $this->whereHas('approvals', function ($q) use ($module) {
                $q->where('target_id',$this->id)->where('module',$module)->where('status','!=','approved')->where('order',2);
            })->count();

            // dd($this->status,$approval1, $approval2,$module);
            if ($this->status == 'draft') {
                $send_chat = array_filter([$chatPPK, $chat_ipsrs]);
            } elseif ($this->status == 'waiting.approval' && $approval1 > 0 &&  $module == 'trans-sperpat' ) { //verify tahap 1
                $send_chat = array_filter([$chatPPK, $chat_ipsrs, $chat_keuangan]);
                $pesan = $pesan.' '.' dan Kepada Bagian Keuangan Mohon Untuk Melakukan Verifikasi';
            } elseif ($this->status == 'waiting.approval' && $approval2 > 0 &&  $module == 'trans-sperpat' ) { //verify tahap 2
                $send_chat = array_filter([$chatPPK, $chat_ipsrs, $chat_direksi]);
                $pesan = $pesan.' '.' dan Kepada Direktur Mohon Untuk Melakukan Verifikasi Selanjutnya';
            }elseif($this->status == 'rejected' &&  $module == 'trans-sperpat' ){ //rejected ipsrs
                $send_chat = array_filter([$chatPPK, $chat_ipsrs]);
            } else {
                $send_chat = array_filter([$chatPPK,$chat_ipsrs]);
            }
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
