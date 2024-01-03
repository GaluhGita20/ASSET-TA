<?php

namespace App\Models\Inventaris\Penyusutan;

use App\Imports\Master\OrgStructImport;
use App\Models\Globals\TempFiles;
// use App\Models\Master\Geografis\City;
use App\Models\Auth\User;
// use App\Models\Master\Org\OrgStruct;
use App\Support\Base;
use App\Models\Model;

class Aset extends Model
{
    protected $table = 'sys_penyusutan_aset';

    protected $fillable = [
         'kib_id'
        ,'acquisition_val'
        ,'residual_val'
        ,'depreciation_rate'
        ,'depreciation_period'
        ,'depreciation_val'
        ,'final_value_recorded'  
    ];

    /** MUTATOR **/

    /** ACCESSOR **/


    /** RELATION **/
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'space_manager_id');
    // }

    // public function orgLocation()
    // {
    //     return $this->belongsTo(OrgStruct::class, 'departemen_id');;
    // }

    /** SCOPE **/



    public function scopeGrid($query)
    {
        return $query->orderBy('jenis_aset');
    }

    public function scopeFilters($query)
    {
        return $query->filterBy(['name','jenis_aset'])
        ->when(
            $jenis_jenis_aset = request()->jenis_jenis_aset,
            function ($q) use ($jenis_jenis_aset){
                $q->whereHas('jenis_aset', function ($qq) use ($jenis_jenis_aset){
                    $qq->where('jenis_aset', $jenis_jenis_aset);
                });
            }
        )
         ->latest();
    }

    /** SAVE DATA **/
    public function handleStoreOrUpdate($request)
    {
        $this->beginTransaction();
        try {
            $this->fill($request->all());
            $this->updated_at = now();
            $this->save();

            $this->saveLogNotify();

            return $this->commitSaved();
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
        }
    }

    public function handleDestroy()
    {
        $this->beginTransaction();
        try {
            if (!$this->canDeleted()) {
                return $this->rollback(__('base.error.related'));
            }
            $this->saveLogNotify();
            $this->delete();

            return $this->commitDeleted();
        } catch (\Exception $e) {
            return $this->rollbackDeleted($e);
        }
    }

    public function handleImport($request, $level)
    {
        $this->beginTransaction();
        try {
            $file = TempFiles::find($request->uploads['temp_files_ids'][0]);
            if (!$file || !\Storage::disk('public')->exists($file->file_path)) {
                $this->rollback('File tidak tersedia!');
            }

            $filePath = \Storage::disk('public')->path($file->file_path);
            \Excel::import(new OrgStructImport($level), $filePath);

            $this->saveLogNotify();

            return $this->commitSaved();
        } catch (\Exception $e) {
            return $this->rollbackSaved($e);
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

    /** OTHER FUNCTIONS **/
    public function canDeleted()
    {
        return true;
    }

   

}
