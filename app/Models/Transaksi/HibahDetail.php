<?php

namespace App\Models\Pengajuan;

use App\Models\Model;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Master\Aset\Aset;
use App\Models\Master\Dana\Dana;
use App\Models\Transaksi\PembelianTransaksi;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;


class HibahDetail extends Model
{

    protected $table = 'trans_usulan_details';

    protected $fillable = [
        'trans_hibah_id',
        'ref_aset_id',
        'desc_spesification',
        'status',
        'HPS_unit_cost',
    ];


    /*******************************
     ** MUTATOR
     *******************************/
    

    /*******************************
     ** RELATION
     *******************************/

     public function asetd()
     {
         return $this->belongsTo(Aset::class, 'ref_aset_id');
     }
 
     public function trans()
     {
         return $this->belongsTo(Dana::class, 'trans_hibah_id');
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
        return $query->when(
            $jenis_jenis_aset = request()->jenis_aset,
            function ($q) use ($jenis_jenis_aset){
                $q->whereHas('asetd', function ($qq) use ($jenis_jenis_aset){
                $qq->where('name','LIKE', '%' . $jenis_jenis_aset . '%');
            });
        })->latest();
            
    }

    /*******************************
     ** SAVING
     *******************************/

    //non pengadaan
    public function handleStoreNewData($request){
        // dd($request->all());
        // if($request->is_purchase == 'no'){
        $this->status ='waiting receipt';
        $data = $request->all();
        $this->fill($data);
        $this->perencanaan_id = null;

        $value1 = str_replace(['.', ','],'',$request->qty_agree);
        $this->qty_agree = (int)$value1;
        $value2 = str_replace(['.', ','],'',$request->HPS_unit_cost);
        $this->HPS_unit_cost = (int)$value2;
        
        // $this->HPS_total_agree = $value1 * $value2;
        $this->save();
        $redirect = route(request()->get('routes') . '.index');
        return $this->commitSaved(compact('redirect'));
        // }
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
            $this->fill($data);
            $this->save();

            $redirect = route(request()->get('routes') . '.edit', $this->code);
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
                $checkStatus = in_array($this->perencanaan->status, ['new', 'draft', 'rejected']);
                return $checkStatus && $user->checkPerms($perms . '.edit');
            case 'show':
                return true;
        }

        return false;
    }
}
