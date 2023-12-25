<?php

namespace App\Models\Master\Vendor;

use App\Imports\Master\ExampleImport;
use App\Models\Globals\TempFiles;
use App\Models\Master\Geografis\City;
use App\Models\Master\Geografis\Province;
use App\Models\Master\Geografis\District;
use App\Models\Master\Vendor\TypeVendor;
use App\Models\Model;
use App\Models\Traits\RaidModel;
use App\Models\Traits\ResponseTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Vendor extends Model
{
    use RaidModel, ResponseTrait;
    // use HasUuids;
    // protected $primaryKey = 'uuid';

    public $table = 'ref_vendor';

    protected $fillable = [
        "name",
        // "id_vendor",
        "leader", 
        "instansi_code",
        "telp",
        "email",
        "contact_person",
        "address",
        "province_id",
        "city_id",
        "district_id"
    ];

    public function provinsi() {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function daerah() {
        return $this->belongsTo(District::class, 'district_id');
    }

    // public function jenisUsaha() {
    //     return $this->hasMany(TypeVendorDetails::class, 'type_vendor_id');
    // }
    public function jenisUsaha()
    {
        return $this->belongsToMany(TypeVendor::class, 'ref_type_vendor_details','vendor_id');
    }

    public function kota() {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function getProvinceName() {
        return $this->provinsi->name;
    }

    public function getJenisUsahaName() {
        return $this->jenisUsaha->name;
    }
    
    public function getCityName() {
        return $this->kota->name;
    }

    /*******************************
     ** MUTATOR
     *******************************/

    /*******************************
     ** ACCESSOR
     *******************************/

    /*******************************
     ** RELATION
     *******************************/
    public function barang()
    {
        return $this->hasMany(Barang::class, 'vendor_id', 'uuid');
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
        return $query->filterBy(['name']);
    }

    /*******************************
     ** SAVING
     *******************************/
    public function handleStoreOrUpdate($request)
    {
        $this->beginTransaction();
        try {
            $this->fill($request->all());
            $this->save();
          
            // $this->jenisUsaha()->sync($request->jenisUsaha);
            //type_id masuk ke type_vendor_id  && $this->id masuk ke $vendor_id

            //$this->childOfGroup()->sync($request->department);

            $this->saveLogNotify();
            // $this->dd($request->all());

            return $this->commitSaved();
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleDestroy()
    {
        $this->beginTransaction();
        try {
            $this->jenisUsaha()->detach($this->vendor_id);
            $this->saveLogNotify();
            $this->delete();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }

    public function handleImport($request)
    {
        $this->beginTransaction();
        try {
            $file = TempFiles::find($request->uploads['temp_files_ids'][0]);
            if (!$file || !\Storage::disk('public')->exists($file->file_path)) {
                $this->rollback('File tidak tersedia!');
            }

            $filePath = \Storage::disk('public')->path($file->file_path);
            \Excel::import(new ExampleImport(), $filePath);

            $this->saveLogNotify();

            return $this->commitSaved();
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function checkAction($action, $perms)
    {
        $user = auth()->user();
        switch ($action) {
            case 'create':
                return $user->checkPerms($perms . '.create');

            case 'edit':
                return $user->checkPerms($perms . '.edit');

            case 'show':
                return true;

            case 'delete':
                return $this->canDeleted() && $user->checkPerms($perms . '.delete');
        }

        return false;
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

    /*******************************
     ** OTHER FUNCTIONS
     *******************************/
    public function canDeleted()
    {
        // if($this->barang->count() || $this->pembelian->count()) return false;
        return true;
    }
}
