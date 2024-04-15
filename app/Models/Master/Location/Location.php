<?php

namespace App\Models\Master\Location;

use App\Imports\Master\OrgStructImport;
use App\Models\Globals\TempFiles;
use App\Models\Master\Geografis\City;
use App\Models\Auth\User;
use App\Models\Master\Org\OrgStruct;
use App\Support\Base;
use App\Models\Model;

class Location extends Model
{
    protected $table = 'ref_location_aset';

    protected $fillable = [
        'name',
        'space_code',
        'departemen_id', 
        'pic_id',
        'floor_position',
    ];

    /** MUTATOR **/

    /** ACCESSOR **/


    /** RELATION **/
    public function user()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function orgLocation()
    {
        return $this->belongsTo(OrgStruct::class, 'departemen_id');;
    }

   
    /** SCOPE **/

    public function scopeFilters($query)
    {
        return $query->filterBy(['name','departemen_id'])
        ->when(
            $departemen_departemen_id = request()->departemen_departemen_id,
            function ($q) use ($departemen_departemen_id){
                $q->whereHas('orgLocation', function ($qq) use ($departemen_departemen_id){
                    $qq->where('departemen_id', $departemen_departemen_id);
                });
            }
        ) ->latest();
    }   
    


    public function scopeGrid($query)
    {
        //return $query->latest();
        return $query->orderBy('space_code','DESC');
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
        // if (in_array($this->type, [1, 2, 3, 4, 5])) return false;
        // if (in_array($this->level, ['root', 'bod'])) return false;
        // // if ($this->child()->exists()) return false;
        // if ($this->structGroup()->exists()) return false;
        // if ($this->positions()->exists()) return false;
        return true;
    }

}
