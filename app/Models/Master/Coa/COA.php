<?php

namespace App\Models\Master\Coa;

use App\Models\Auth\User;
use App\Models\Master\Org\OrgStruct;
use App\Models\Model;
use App\Models\Traits\HasApprovals;
use App\Models\Traits\HasFiles;
use App\Models\Traits\RaidModel;
use App\Models\Traits\ResponseTrait;
use App\Models\Traits\Utilities;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class COA extends Model
{
    use HasFiles,  RaidModel, ResponseTrait, Utilities, HasApprovals;
    // use HasUuids;
    // protected $primaryKey = 'uuid';
    protected $table = 'ref_kode_aset_bmd';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'tipe_akun',
        'deskripsi',
        'status',
    ];


     /*******************************
     ** MUTATOR
     *******************************/


    /*******************************
     ** ACCESSOR
     *******************************/
    public function getShowTipeAkunAttribute()
    {
        switch ($this->tipe_akun) {
            case 'KIB A':
                return __('Tanah');
            case 'KIB B':
                return __('Mesin');
            default:
                return ucfirst($this->tipe_akun);
        }
    }

    public function checkAction($action, $perms)
    {
        $user = auth()->user();
        switch ($action) {
            case 'create':
                return $user->checkPerms($perms.'.create');

            case 'edit':
                return $user->checkPerms($perms.'.edit');

            case 'show':
                return true;

            case 'delete':
                return $user->checkPerms($perms.'.delete');
        }

        return false;
    }



    /*******************************
     ** RELATION
     *******************************/


    /*******************************
     ** SCOPE
     *******************************/
    public function scopeGrid($query)
    {
        // return $query->orderBy('id');
        return $query->orderBy('kode_akun','DESC');
    }

    public function scopeFilters($query)
    {
        return $query
        ->filterBy(['tipe_akun'])
        ->filterBy(['nama_akun']);
    }

    /*******************************
     ** SAVING
     *******************************/
    public function handleStoreOrUpdate($request)
    {
        $this->beginTransaction();
        try {
            $data = $request->all();
            // $data['module'] = $request->module;
            $this->fill($data);
            if($request->module == 'master_coa_tanah'){
                $this->tipe_akun = 'KIB A';
            }elseif($request->module == 'master_coa_peralatan'){
                $this->tipe_akun = 'KIB B';
            }elseif($request->module == 'master_coa_bangunan'){
                $this->tipe_akun = 'KIB C';
            }elseif($request->module == 'master_coa_jalan_irigasi'){
                $this->tipe_akun = 'KIB D';
            }elseif($request->module == 'master_coa_aset_lainya'){
                $this->tipe_akun = 'KIB E';
            }elseif($request->module == 'master_coa_kontruksi_pembangunan'){
                $this->tipe_akun = 'KIB F';
            }
            
            $this->save();
            $this->saveLogNotify();

            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleDestroy()
    {
        $this->beginTransaction();
        try {
            $this->saveLogNotify();
            $this->delete();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }

    public function saveLogNotify()
    {
        $data = $this->name;
        $routes = request()->get('routes');
        switch (request()->route()->getName()) {
            case $routes . '.store':
                $this->addLog('Membuat Data ' . $data);
                break;
            case $routes . '.update':
                $this->addLog('Mengubah Data ' . $data);
                break;
            case $routes . '.destroy':
                $this->addLog('Menghapus Data ' . $data);
                break;
            case $routes . '.importSave':
                auth()->user()->addLog('Import Data Master Struktur Organisasi');
                break;
        }
    }
}
