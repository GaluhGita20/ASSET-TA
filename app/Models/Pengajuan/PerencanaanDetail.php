<?php

namespace App\Models\Pengajuan;

use App\Models\Model;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Master\Aset\AsetRs;
use App\Models\Master\Dana\Dana;
use App\Models\Transaksi\PembelianTransaksi;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;


class PerencanaanDetail extends Model
{

    protected $table = 'trans_usulan_details';

    protected $fillable = [
        'perencanaan_id',
        'trans_id',
        'ref_aset_id',
        'desc_spesification',
        'existing_amount',
        'requirement_standard',
        'qty_req',
        'is_purchase',
        'status',
        'HPS_unit_cost',
        'HPS_total_cost',
        'HPS_total_agree',
        'sumber_biaya_id',
        'reject_notes',
    ];


    /*******************************
     ** MUTATOR
     *******************************/
    public function setExistingAmountAttribute($value = '')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['existing_amount'] = (int) $value;
    }

    public function setRequirementStandardAttribute($value = '')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['requirement_standard'] = (int) $value;
    }

    public function setQtyReqAttribute($value = '')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['qty_req'] = (int) $value;
    }


    public function setHpsUnitCost($value ='')
    {
        $value = str_replace(['.',','],'',$value);
        $this->attributes['HPS_unit_cost'] = (int)$value;
    }

    public function setHpsTotalCostAttribute($value='')
    {
        $value = str_replace(['.',','],'',$value);
        $this->attributes['HPS_total_cost'] = (int)$value;
    }

    public function setHpsTotalAgree($value='')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['Hps_total_agree'] = (int) $value;
    }

    /*******************************
     ** RELATION
     *******************************/

     public function asetd()
     {
         return $this->belongsTo(AsetRs::class, 'ref_aset_id');
     }
 
     public function danad()
     {
         return $this->belongsTo(Dana::class, 'sumber_biaya_id');
     }
     public function users()
     {
         return $this->belongsTo(User::class, 'created_by');
     }
 
     public function perencanaanPembelian()
     {
         return $this->belongsToMany(PembelianTransaksi::class, 'trans_pivot_perencanaan_pengadaan','detail_usulan_id' ,'pembelian_id');
     }
 
     public function perencanaanPembelianDetail()
     {
         return $this->belongsToMany(PembelianTransaksi::class, 'trans_pivot_perencanaan_pengadaan', 'detail_usulan_id', 'pembelian_id')
        ->withPivot('pembelian_id', 'detail_usulan_id');
     }
 
     public function perencanaan()
     {
         return $this->belongsTo(Perencanaan::class, 'perencanaan_id');
     }

     public function trans()
     {
         return $this->belongsTo(PembelianTransaksi::class, 'trans_id');
     }

    /*******************************
     ** SCOPE
     *******************************/

     public function scopeGrid($query)
     {
         return $query->where('status','draf');
     }


    public function scopeFilters($query)
    {
        // return   $query->filterBy(['aset'])
        //     ->when(
        //         $year = request()->procurement_year,
        //         function ($q) use ($year){
        //             $q->whereHas('perencanaan', function($qq) use ($year){
        //                 $qq->where('procurement_year',$year);
        //             });
        //     })->latest();

        return $query->when(
            $jenis_jenis_aset = request()->jenis_aset,
            function ($q) use ($jenis_jenis_aset){
                $q->whereHas('asetd', function ($qq) use ($jenis_jenis_aset){
                $qq->where('name','LIKE', '%' . $jenis_jenis_aset . '%');
            });
        })->latest();
            
    }

    public function scopeLaporan($query){
        $jenis_jenis_aset = request()->jenis_aset;
        $tahun_pengadaan = request()->procurement_year;
        $departemen = request()->struct_id;

       return $query->when($jenis_jenis_aset, function ($q) use ($jenis_jenis_aset) {
            $q->whereHas('asetd', function ($qq) use ($jenis_jenis_aset) {
                $qq->where('name', 'LIKE', '%' . $jenis_jenis_aset . '%');
            });
        })
        ->when($tahun_pengadaan && $departemen, function ($q) use ($tahun_pengadaan, $departemen) {
            $q->whereHas('perencanaan', function ($qq) use ($tahun_pengadaan, $departemen) {
                $qq->where('procurement_year', $tahun_pengadaan)->where('struct_id', $departemen);
            });
        })
        ->latest();
    }

    /*******************************
     ** SAVING
     *******************************/

    //non pengadaan
    public function handleStoreNewData($request){
        // dd($request->all());
        if($request->is_purchase == 'no'){
            $this->status ='waiting purchase';
            $data = $request->all();
            $this->fill($data);
            $this->perencanaan_id = null;

            $value1 = str_replace(['.', ','],'',$request->qty_agree);
            $this->qty_agree = (int)$value1;
            $value2 = str_replace(['.', ','],'',$request->HPS_unit_cost);
            $this->HPS_unit_cost = (int)$value2;
            
            $this->HPS_total_agree = $value1 * $value2;
            $this->save();
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        }
    }

    public function handleDestroy(){
        $this->beginTransaction();
        try {
            $this->delete();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }

    public function handleStoreOrUpdate($request)
    {
        $this->beginTransaction();
        try { 

            $data = $request->all();
          //  dd($data);
            $this->fill($data);
            $this->save();

            $redirect = route(request()->get('routes') . '.edit', $this->code);
            return $this->commitSaved(compact('redirect'));

        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }



    public function handleStoreListPembelian($request){ //handle data yang di checklist , untuk dibuat transaksi

        $this->beginTransaction();
        try { 
          //
            if ($request->usulan_id != null) {
                $allIds = collect($request->usulan_id)->flatten()->toArray();
                
                // $jenisUsulan = perencanaanPembelian::whereIn('perencanaan_id',$request->)
                $filter = $this->filterErrorUsulan($allIds);
                if($filter == 'none'){
                    //dd($filter);
                    $pagu = PerencanaanDetail::whereIn('id', $allIds)
                        ->sum('HPS_total_agree');
    
                    $jumlah_beli = PerencanaanDetail::whereIn('id', $allIds)
                        ->sum('qty_agree');
    
                    $data = [
                        'id' => $allIds,
                        'pagu' => $pagu,
                        'jumlah_beli' => $jumlah_beli,
                    ];
                 
                    //session
                    session(['usulan_id' => $allIds]);
    
                    $redirect = route(request()->get('routes') . '.create', $data);
                    return $this->commitSaved(compact('redirect'));
                }else{
                    return $this->rollback(
                        [
                            'message' => $filter
                        ]
                    );
                }
            }else{
                return $this->rollback(
                    [
                        'message' => 'Data Detail Usulan Harus Dipilh Tidak Boleh Kosong!'
                    ]
                );
            }
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }

    }

    public function filterErrorUsulan($idx){
       
       
        $filterTahun = PerencanaanDetail::whereIn('id', $idx)
            ->whereHas('perencanaan', function ($query) {
            $currentYear = now()->year; // Mendapatkan tahun saat ini
            $query->whereYear('procurement_year', $currentYear);
        })->count();

        $aset = PerencanaanDetail::where('id',$idx[0])->first();
        $filterAset = PerencanaanDetail::whereIn('id', $idx)
            ->whereHas('asetd', function ($query) use($aset ) {
                $query->where('id', $aset->ref_aset_id);
        })->count();

        $message = 'none';
        if($filterTahun == 0){
            $message = 'Data Tahun Pengadaan Harus Tahun '.now()->year;
            return $message;
        }elseif($filterAset != count($idx)){
            $message = 'Silahkan Pilih Aset yang Sejenis';
            return $message;
        }else{
            return $message;
        }
        

    }

    // public function getDetailUsulan($id){ // ambil data detail usulan
    //     $data = DB::table('trans_pivot_perencanaan_pengadaan')
    //         ->where('detail_usulan_id', $id)
    //         ->first();
    //     if($data !=null){ //ambil data pembelian
    //         $data1 = PembelianTransaksi::where('id',$data->pembelian_id)->pluck('id');
    //         return $data1[0];
    //     }else{
    //         return 0;
    //     }
    // }

    public function getDetailUsulan($id){ // ambil data detail usulan
        // $data = DB::table('trans_pivot_perencanaan_pengadaan')
        //     ->where('detail_usulan_id', $id)
        //     ->first();
        $data = PerencanaanDetail::where('id',$id)->pluck('id');
        // dd($data[0]);
        return $data[0];
        // if($data !=null){ //ambil data pembelian
        //     $data1 = PembelianTransaksi::where('id',$data->pembelian_id)->pluck('id');
        //     return $data1[0];
        // }else{
        //     return 0;
        // }
    }

    // public function handleStoreEditListPembelian($idp){ // hadle edit detail transaksi data untuk hapus detail usulan
    //    $this->beginTransaction();
    //    try { 
    //         if($idp != 0){ //hapus detail usulan pada data transaksi dalam kondisi edit
    //             $flag = DB::table('trans_pivot_perencanaan_pengadaan')
    //                ->where('pembelian_id', $idp)
    //                ->count('pembelian_id');
   
    //            if($flag > 1){
    //                DB::table('trans_pivot_perencanaan_pengadaan')
    //                    ->where('detail_usulan_id', $this->id)
    //                    ->where('pembelian_id',$idp)
    //                    ->delete();
   
    //                $this->status = 'waiting purchase';
    //                $this->save();
   
    //                return $this->commitDeleted([
    //                    'redirect' => route('transaksi.pengadaan-aset' . '.edit', $idp)
    //                ]);
   
    //            }else{
    //                return $this->rollback(
    //                    [
    //                        'message' => 'Data Detail Usulan Tidak Boleh Kosong!'
    //                    ]
    //                );
    //            }
    //         }else{ //hapus detail usulan pada data transaksi dalam dalam kondisi create
  
    //             $sesi = session('usulan_id');
    //             if(count($sesi) > 1){
                
    //                 $sesi = array_diff($sesi, [$this->id]);
    //                 session(['usulan_id' => $sesi]);

    //                 $allIds = collect($sesi)->flatten()->toArray();
    //                 $pagu = PerencanaanDetail::whereIn('id', $allIds)
    //                     ->sum('HPS_total_agree');
    
    //                 $jumlah_beli = PerencanaanDetail::whereIn('id', $allIds)
    //                     ->sum('qty_agree');
    
    //                 $data = [
    //                     'id' => $allIds,
    //                     'pagu' => $pagu,
    //                     'jumlah_beli' => $jumlah_beli,
    //                 ];
                 
    //                 $redirect = route(request()->get('routes') . '.create', $data);
    //                 return $this->commitSaved(compact('redirect'));
    //             }else{
    //                 return $this->rollback(
    //                     [
    //                         'message' => 'Data Detail Usulan Tidak Boleh Kosong!'
    //                     ]
    //                 );
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         return $this->rollbackSaved($e);
    //     }

    // }

    public function handleStoreEditListPembelian($idp){ // hadle edit detail transaksi data untuk hapus detail usulan
        $this->beginTransaction();
        try { 
            $trans_id = PerencanaanDetail::where('id', $idp)->pluck('trans_id');
            // dd($trans_id[0]);
            if($trans_id[0] != NULL){ //hapus detail usulan pada data transaksi dalam kondisi edit
                 $flag = PerencanaanDetail::where('trans_id', $trans_id)->count('id');
                    
                if($flag > 1){

                    PerencanaanDetail::where('id', $idp)->update(['trans_id' => NULL, 'status'=>'waiting purchase']);

                    return $this->commitDeleted([
                        'redirect' => route('transaksi.pengadaan-aset' . '.edit', $trans_id[0])
                    ]);
    
                }else{
                    return $this->rollback(
                        [
                            'message' => 'Data Detail Usulan Tidak Boleh Kosong!'
                        ]
                    );
                }
             }else{ //hapus detail usulan pada data transaksi dalam dalam kondisi create
                // dd('tes');
                 $sesi = session('usulan_id');

                 if(count($sesi) > 1){
                 
                     $sesi = array_diff($sesi, [$this->id]);
                     session(['usulan_id' => $sesi]);
 
                     $allIds = collect($sesi)->flatten()->toArray();
                     $pagu = PerencanaanDetail::whereIn('id', $allIds)
                         ->sum('HPS_total_agree');
     
                     $jumlah_beli = PerencanaanDetail::whereIn('id', $allIds)
                         ->sum('qty_agree');
     
                     $data = [
                         'id' => $allIds,
                         'pagu' => $pagu,
                         'jumlah_beli' => $jumlah_beli,
                     ];
                  
                     $redirect = route(request()->get('routes') . '.create', $data);
                     return $this->commitSaved(compact('redirect'));
                 }else{
                     return $this->rollback(
                         [
                             'message' => 'Data Detail Usulan Tidak Boleh Kosong!'
                         ]
                     );
                 }
             }
         } catch (\Exception $e) {
             return $this->rollbackSaved($e);
         }
 
     }
 
    public function checkAction($action, $perms, $record = null)
    {
        $user = auth()->user();
        switch ($action) {
            case 'edit':
                $checkStatus = in_array($this->perencanaan->status, ['new', 'draft', 'rejected']);
                return $checkStatus && $user->checkPerms($perms . '.edit');
            case 'show':
                return true;
        }

        return false;
    }
}
