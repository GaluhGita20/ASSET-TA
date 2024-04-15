<?php

namespace App\Models\Pemeliharaan;

use App\Models\Model;
use App\Models\Pemeliharaan\Pemeliharaan;
use App\Models\Inventaris\Aset;
use App\Models\Master\Dana\Dana;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;


class PemeliharaanDetail extends Model
{

    protected $table = 'trans_pemeliharaan_detail';

    protected $fillable = [
        'pemeliharaan_id',
        'kib_id',
        'first_condition',
        'latest_condition',
        'maintenance_action',
        'repair_officer',
    ];


    /*******************************
     ** MUTATOR
     *******************************/
    public function setExistingAmountAttribute($value = '')
    {
        $value = str_replace(['.', ','], '', $value);
        $this->attributes['existing_amount'] = (int) $value;
    }

    /*******************************
     ** RELATION
     *******************************/

    public function asetd()
    {
        return $this->belongsTo(Aset::class,'kib_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'repair_officer');
    }

    public function pemeliharaan()
    {
        return $this->belongsTo(Pemeliharaan::class, 'pemeliharaan_id');
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

    public function handleDestroy(){
        $this->beginTransaction();
        try {
            $this->delete();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
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
