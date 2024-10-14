<?php

namespace App\Models\Master\Org;

use App\Imports\Master\PositionImport;
use App\Models\Auth\User;
use App\Models\Globals\TempFiles;
use App\Models\Master\Org\OrgStruct;
use App\Models\Model;

class Position extends Model
{
    // $table menentukan bahwa model ini berhubungan dengan tabel bernama ref_positions.
    protected $table = 'ref_positions';

    // protected // Hanya bisa diakses dari dalam kelas atau subclass
    // private, properti atau metode tersebut hanya bisa diakses dari dalam kelas itu sendiri, tidak bisa diakses dari luar kelas atau dari kelas turunan.
    // public: Dapat diakses dari mana saja (dalam, luar, dan subclass).

    protected $fillable = [
        'location_id',
        // 'level_id',
        'level',
        'name',
        'code',
        'telegram_user_id'
    ];

    // $fillable adalah properti larangan mass assignment dalam Laravel. Properti ini menentukan kolom-kolom mana yang boleh diisi secara massal ketika menggunakan fungsi seperti create() atau update() untuk memasukkan data ke database.

    // Ini bertujuan untuk melindungi data dari "mass assignment vulnerability", yaitu ketika ada data yang tidak diharapkan diisi oleh pengguna atau proses otomatis.

    /*******************************
     ** MUTATOR
     *******************************/

    /*******************************
     ** ACCESSOR
     *******************************/

    /*******************************
     ** RELATION
     *******************************/
    public function location()
    {
        return $this->belongsTo(OrgStruct::class,'location_id');
    }

    // public function level()
    // {
    //     return $this->belongsTo(LevelPosition::class, 'level_id');
    // }

    public function struct()
    {
        // many-to-one (banyak ke satu) antara model posisi dengan model struct yang artinya 1 posisi hanya boleh punya 1 struct


        return $this->belongsTo(OrgStruct::class, 'location_id');
    }
    
    public function users()
    {
         // hasmany (banyak ke banyak) antara model posisi dengan model user yang artinya 1 posisi memiliki banyak user
        return $this->hasMany(User::class,'position_id');
    }
    /*******************************
     ** SCOPE
     *******************************/
    public function scopeGrid($query)
    {
        return $query->orderBy('location_id');
        // return $query->whereHas('struct', function  ($q){
        //     $q->orderBy('code');
        // });
    }

    public function scopeFilters($query)
    {
        return $query->filterBy(['name'])
            ->when(
                $location_id = request()->post('location_id'),
                function ($q) use ($location_id) {
                    $q->where('location_id', $location_id);
                }
            );
    }

    /*******************************
     ** SAVING
     *******************************/
    public function handleStoreOrUpdate($request)
    {
        $this->beginTransaction();
        try {
            $this->fill($request->all());
            // $this = instance dari model saat ini, fill merupakan method untuk mengisi value pada model ini
            // dd($request->all());
            // if()
            $root = $request->root_id;
            $this->code = $this->code ? : $this->getNewCode($request);
            
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
                throw new \Exception('#' . __('base.error.related'));
            }
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
            \Excel::import(new PositionImport(), $filePath);

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
            // fungsi global dalam Laravel yang mengembalikan instance dari request saat ini.
            //  metode pada objek request yang mengembalikan informasi tentang rute yang sedang diproses
            // getName() adalah metode pada objek rute yang mengembalikan nama dari rute saat ini.
            case $routes . '.store':
                $this->addLog('Membuat Data ' . $data);
                break;
            case $routes . '.update':
                $this->addLog('Mengubah Data ' . $data);
                break;
            case $routes . '.destroy':
                $this->addLog('Menghapus Data ' . $data);
                break;
        }
    }

    /*******************************
     ** OTHER FUNCTIONS
     *******************************/
    public function canDeleted()
    {
        if ($this->users()->exists()) return false;
        return true;
    }

    public function getNewCode($request)
    {
        // request objek global dan mengambil properti root_id
        $instansi = OrgStruct::where('id',$request->root_id)->value('name');


        if($instansi == 'RSUD Kabupaten Lombok Utara'){
            $data =[1001,2000];
            $max = static::whereBetween('code',$data)->max('code');
            // static = menyatakan bahwa kita akan beroperasi pada model saat ini.
            return $max ? $max + 1 : 1001;
        }else{
            $max = static::max('code');
            return $max ? $max + 1 : 2001;
        }
    }

    public function imAuditor()
    {
        // isset() dalam PHP digunakan untuk memeriksa apakah sebuah variabel telah dideklarasikan dan memiliki nilai yang bukan null. Jika variabel tersebut ada dan tidak bernilai null, isset() akan mengembalikan true

        // isset artinya memastikan bahwa sudah di setting / ada nilai, maka akan bernilai true
        return $this->location->code == 3001 || (isset($this->location->parent->code) && $this->location->parent->code == 3001);
    }

    public function imAuditorBranchEvaluasi()
    {
        $temp = OrgStruct::where(function ($q) {
            $q->where(function ($qq) {
                $qq->seksiEvaluasi();
            });
        })->get();

        $lists = [];
        foreach($temp as $dd){
            $lists = array_merge($lists, $dd->getIdsWithChild());
        }
        return in_array($this->location_id , $lists);
    }

    public function imKepalaDeparetemen()
    {
        $temp = OrgStruct::department()->get();
        // ambil semua data struct organisasi

        $user = auth()->user();
        // ambil data user yang login

        $lists = [];
        foreach($temp as $dd){
            // objek -> method relasi ->properti dari position
            if($user->position->level == 'kepala' && $user->position->location_id == $dd->id){
                return true;
            }
        }
        return false;
    }

    public function imKepalaDeparetemenWithLocation($locaton_id)
    {
        $temp = OrgStruct::department()->get();
        $user = auth()->user();

        $lists = [];
        foreach($temp as $dd){
            if(in_array($location_id, $dd->getIdsWithChild())){
                // $dd->getIdsWithChild()
                // $dd objek dari struct dan memiliki method getIdsWWithchild
                if($user->position->level == 'kepala' && $user->positon->location_id == $dd->id){
                    return true;
                }
            }
        }
        return false;
    }


    // public function imLevelManajerSPI()
    // {
    //     $temp = Position::whereHas('level', function ($q) {
    //         $q->where('name', 'LIKE', '%' . 'Manajer SPI');
    //     })->pluck('id')->toArray();
    //     return in_array($this->id, $temp);
    // }

    public function isAuditor($request)
    {
        if ($this->where([['name', 'like', '%Audit%'], ['id', '=', $request]])->exists()) return true;

        return false;
    }
}
