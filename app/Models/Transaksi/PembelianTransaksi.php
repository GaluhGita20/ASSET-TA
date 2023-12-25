<?php

namespace App\Models\Transaksi;

use App\Models\Model;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Master\Aset\Aset;
use App\Models\Master\Dana\Dana;
use App\Models\Master\Vendor\Vendor;
use App\Models\Master\Pengadaan\Pengadaan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class PembelianTransaksi extends Model
{

    protected $table = 'trans_aset';

    protected $fillable = [
        'trans_name',
        'vendor_id',
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
        'condition_aset',
        'asset_test_results',
        'location_receipt',
    ];

    // protected $casts = [
    //     'spk_start_date'          => 'date',
    //     'spk_end_date'          => 'date',
    //     'receipt_date'          => 'date',
    // ];

    public function vendors()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function pengadaans()
    {
        return $this->belongsTo(Pengadaan::class, 'jenis_pengadaan_id');
    }

   
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

    public function setTotalCostAttribute($value = '')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['total_cost'] = (int) $value;
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
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['HPS_unit_cost'] = (int) $value;
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
    


    public function perencanaanPengadaan()
    {
        return $this->belongsToMany(PerencanaanDetail::class, 'trans_pivot_perencanaan_pengadaan', 'pembelian_id', 'detail_usulan_id');
    }

   

    /*******************************
     ** SCOPE
     *******************************/

     public function scopeGrid($query)
     {
         return $query->latest();
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
            $this->spk_range_time = 12;
            $this->fill($data);
            $this->save();

            $dataArray = json_decode($request->usulan_id, true);

            $this->perencanaanPengadaan()->sync($dataArray ?? []);
            foreach ($dataArray as $id) {
                PerencanaanDetail::where('id', $id)->update(['status' => 'waiting register']);
            }

            $redirect = route('transaksi.pengadaan-aset' . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    // public function getPerencanaanPengadaan($record){
    //     $data = 
    // }

    public function getPerencanaanPengadaan($record)
    {
        $usulan = DB::table('trans_pivot_perencanaan_pengadaan')
        ->where('pembelian_id', $record)
        ->pluck('detail_usulan_id');

        $usulan_id = collect($usulan)->flatten()->toArray();

        $pagu = PerencanaanDetail::whereIn('id',$usulan_id)->sum('HPS_total_agree');
        $jumlah_beli = PerencanaanDetail::whereIn('id',$usulan_id)->sum('qty_agree');

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

    // public function getPaguJumlahBeli(){
        
    // }

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
