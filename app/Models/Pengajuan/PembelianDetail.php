<?php

namespace App\Models\Pengajuan;

use App\Models\Model;
use App\Models\Master\Coa\COA;
use Carbon\Carbon;


class PembelianDetail extends Model
{

    protected $table = 'trans_pengajuan_pembelian_details';

    protected $fillable = [
        'pembelian_id',
        'coa_id',
        'existing_amount',
        'requirement_standard',
        'qty_req'
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

    /*******************************
     ** RELATION
     *******************************/

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public function coa()
    {
        return $this->belongsTo(COA::class, 'coa_id');
    }
    /*******************************
     ** SCOPE
     *******************************/

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
            if (!empty($request->nominal)) {
                $data['nominal'] = str_replace('.', '', $request->nominal);
            }
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
                $checkStatus = in_array($this->pj->status, ['new', 'draft', 'rejected']);
                return $checkStatus && $user->checkPerms($perms . '.edit');
            case 'show':
                return true;
        }

        return false;
    }
}
