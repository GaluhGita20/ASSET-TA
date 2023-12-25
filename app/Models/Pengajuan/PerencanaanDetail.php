<?php

namespace App\Models\Pengajuan;

use App\Models\Model;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Master\Aset\Aset;
use App\Models\Master\Dana\Dana;
use App\Models\Transaksi\PembelianTransaksi;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class PerencanaanDetail extends Model
{

    protected $table = 'trans_usulan_details';

    protected $fillable = [
        'perencanaan_id',
        'ref_aset_id',
        'desc_spesification',
        'existing_amount',
        'requirement_standard',
        'qty_req',
        'status',
        'HPS_unit_cost',
        'HPS_total_cost',
        'HPS_total_agree',
        'sumber_biaya_id',
    ];

    public function asetd()
    {
        return $this->belongsTo(Aset::class, 'ref_aset_id');
    }

    public function danad()
    {
        return $this->belongsTo(Dana::class, 'sumber_biaya_id');
    }

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

    public function perencanaan()
    {
        return $this->belongsTo(Perencanaan::class, 'perencanaan_id');
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
        return $query;
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

            $redirect = route(request()->get('routes') . '.edit', $this->code);
            return $this->commitSaved(compact('redirect'));

        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }


    public function handleStoreListPembelian($request){ //handle data yang di checklist , untuk dibuat transaksi

        $this->beginTransaction();
        try { 

            if ($request->usulan_id != null) {
                $allIds = collect($request->usulan_id)->flatten()->toArray();
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
                        'message' => 'Data Detail Usulan Harus Dipilh Tidak Boleh Kosong!'
                    ]
                );
            }

            
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }

    }

    public function getDetailUsulan($id){ // ambil data detail usulan
        $data = DB::table('trans_pivot_perencanaan_pengadaan')
            ->where('detail_usulan_id', $id)
            ->first();
        if($data !=null){
            $data1 = PembelianTransaksi::where('id',$data->pembelian_id)->pluck('id');
            return $data1[0];
        }else{
            return 0;
        }
    }

    public function handleStoreEditListPembelian($idp){ // hadle edit detail transaksi data untuk hapus detail usulan
       $this->beginTransaction();
       try { 
            if($idp != 0){ //hapus detail usulan pada data transaksi dalam kondisi edit
                $flag = DB::table('trans_pivot_perencanaan_pengadaan')
                   ->where('pembelian_id', $idp)
                   ->count('pembelian_id');
   
               if($flag > 1){
                   DB::table('trans_pivot_perencanaan_pengadaan')
                       ->where('detail_usulan_id', $this->id)
                       ->where('pembelian_id',$idp)
                       ->delete();
   
                   $this->status = 'Draf';
                   $this->save();
   
                   return $this->commitDeleted([
                       'redirect' => route('transaksi.pengadaan-aset' . '.edit', $idp)
                   ]);
   
               }else{
                   return $this->rollback(
                       [
                           'message' => 'Data Detail Usulan Tidak Boleh Kosong!'
                       ]
                   );
               }
            }else{ //hapus detail usulan pada data transaksi dalam dalam kondisi create
  
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
