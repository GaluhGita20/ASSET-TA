<?php

namespace App\Models\Inventaris;

use App\Imports\Master\OrgStructImport;
use App\Models\Globals\TempFiles;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Transaksi\PembelianTransaksi;
use App\Models\Master\Location\Location;
use App\Models\Auth\User;
use App\Models\Master\Geografis\City;
use App\Models\Traits\RaidModel;
use App\Models\Traits\ResponseTrait;
use App\Models\Master\Geografis\Province;
use App\Models\Master\Geografis\District;
use App\Models\Master\Coa\Coa;
use App\Support\Base;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\Model;
use Carbon\Carbon;

class Aset extends Model
{
    use RaidModel, ResponseTrait;

    protected $table = 'ref_kib';

    protected $fillable = [
    'trans_id',
    'usulan_id',
    'coa_id',
    'source_acq',
    'type',
    'no_register',
    'wide',
    'province_id',
    'city_id',
    'district_id',
    'address',
    'land_rights',
    'no_sertificate',
    'sertificate_date',
    'land_use',
    'merek_type_item',
    'cc_size_item',
    'material',
    'no_frame',
    'no_factory_item',
    'no_police_item',
    'no_BPKB_item',
    'no_machine_item',
    'is_graded_bld',
    'is_concreate_bld',
    'wide_bld',
    'long_JJR',
    'width_JJR',
    'title',
    'land_status',
    'spesifikasi',
    'tipe_animal',
    'size_animal',
    'creators',
    'status',
    'condition',
    'room_location',
    'description',
    'useful', //masa manfaat
    'residual_value', //nilai akhir masa manfaat
    'accumulated_depreciation', //nlai penyusutan //dicari
    'book_value',   //nilai saat ini //dicari
    'tanah_id',
    'book_date',
    ];

    /** MUTATOR **/

    /** ACCESSOR **/

    /** RELATION **/

    public function coad()
    {
        return $this->belongsTo(Coa::class, 'coa_id'); // Sesuaikan dengan kunci asing yang sesuai
    }

    public function usulans()
    {
        return $this->belongsTo(PerencanaanDetail::class, 'usulan_id');
    }

    public function trans()
    {
        return $this->belongsTo(PembelianTransaksi::class, 'trans_id');
    }

    public function locations()
    {
        return $this->belongsTo(Location::class, 'room_location');
    }

    // public function users()
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }


    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /** SCOPE **/

    public function scopeGrid($query)
    {
        $user = auth()->user();
        return $query->when(empty(array_intersect(['PPK','Keuangan','Sarpras','Direksi','Sub Bagian Program Perencanaan','BPKAD'], $user->roles->pluck('name')->toArray())), 
        function ($q) use ($user) { 
        $q->when($user->position->imKepalaDeparetemen(), 
            function ($qq) use ($user) {
                // Ambil semua id struktur yang terkait dengan departemen dan anaknya
                $structIds = $user->position->location->getIdsWithChild();
                
                // Filter berdasarkan relasi dengan model Usulan dan Perencanaan
                $qq->whereHas('usulans', function ($qUsulan) use ($structIds) {
                    $qUsulan->whereHas('perencanaan', function ($qPerencanaan) use ($structIds) {
                        $qPerencanaan->whereIn('struct_id', $structIds);
                    });
                });
            },
            function ($qq) use ($user) {
                // Jika bukan Kepala Departemen, filter langsung berdasarkan struct_id
                $qq->whereHas('usulans', function ($qUsulan) use ($user) {
                    $qUsulan->whereHas('perencanaan', function ($qPerencanaan) use ($user) {
                        $qPerencanaan->where('struct_id', $user->position->location->id);
                    });
                });
            }
        );
    })->latest();       
    }

    public function scopeFilters($query)
    {
        return $query
        ->when($jenis_jenis_aset = request()->jenis_aset,
            function ($q) use ($jenis_jenis_aset){
                $q->whereHas('usulans', function ($qq) use ($jenis_jenis_aset){
                    $qq->whereHas('asetd',function ($qqq) use ($jenis_jenis_aset){
                        $qqq->where('name','LIKE', '%' . $jenis_jenis_aset . '%');
                    });
                });
            }
        )->latest();
    }

    public function handleSubmitKib($request){
        // if(count($request->usulan_id) > 1){
        //     return $this->rollback(__('Checklist 1 Data Untuk Di Inventarisasikan'));
        // }
        // dd($request['usulan_id']);
        if($request['usulan_id'] != null || $request->usulan_id != null ){
            if($request['usulan_id'] != null){
                $customValue = $request['customValue'];
                $usulan_id = $request['usulan_id'];
                $usulan = PerencanaanDetail::where('id',$usulan_id)->get();
            }else{
                // dd('tes');
                if( count($request->usulan_id) > 1 ){
                    return $this->rollback(__('Pilih Satu Data Untuk Di Inventarisasikan'));
                }
                $customValue = $request->customValue;
                $usulan_id = $request->usulan_id;
                $usulan = PerencanaanDetail::find($usulan_id);
            }
    
            foreach ($usulan as $data_pembelian) {
                $trans = $data_pembelian->perencanaanPembelianDetail;
            }

            $usulan = $usulan->first();
            $trans = $trans->first();
            $aset = Aset::where('usulan_id',$usulan_id)->count(); //jumlah terdaptar
            $jumlah_item= abs($usulan->qty_agree - $aset); //jumlah saat ini = jumlah rill - jumlah terdaptar
    
            if($jumlah_item != 0){
                $data = [
                    'usulan_id' => $usulan->id,
                    'trans_id' => $trans->id,
                    'jumlah' => $jumlah_item,
                    'customValue'=> $customValue,
                ];
                $redirect = route(request()->get('routes') . '.create', $data );
                return $this->commitSaved(compact('redirect'));
            }else{
                // dd($request->usulan_id);
                PerencanaanDetail::where('id',$request->usulan_id)->update(['status'=>'completed']);
                $redirect = route(request()->get('routes') . '.index');
                return $this->commitSaved(compact('redirect'));
            }
        }else{
            return $this->rollback(__('Silahkan Checklist 1 Data Untuk Diinventarisasikan'));
        }
    }

    /** SAVE DATA **/

    public function handleStoreOrUpdateKibB($request){

    //    dd($request->all());
       $jumlah_item = $request->jumlah_semua; //jumlah semua - jumlah diimput
       $flagInv = $jumlah_item - $request->qty;
       $data = $request->all();
       if($request->qty > $jumlah_item ){
            return $this->rollback(__('Jumlah Tidak Sesuai'));
       }
       if($request->qty == 0){
            return $this->rollback(__('Jumlah Tidak Boleh Kosong'));
       }
        $value6 = str_replace(['.', ','],'',$request->residual_value);
        $residu = (int)$value6;

        $value7 = str_replace(['.', ','],'',$request->unit_cost);
        $cost = (int)$value7;


        if($request->qty > 1){
            if($request->no_factory_item !=null || $request->no_police_item !=null || $request->no_BPKB_item !=null || $request->no_machine_item != null){
                return $this->rollback(__('Tidak Bisa Melakukan Percepatan Karena Spesifikas Detail Sama'));
            }else{
                $no_inventaris=0;
                for ($i = 0 ; $i < $request->qty ; $i++) {
                    $aset = new Aset();
                    $aset->fill($data);
                    $aset->type = 'KIB B';
                    $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
                    $aset->no_register = $no_inventaris + 1;
                    $aset->accumulated_depreciation = 0;
                    //nilai residu    = harga unit - residu / masa manfaat
                    // $aset->residual_depresi = ($cost - $residu) / $request->useful;
                    $aset->book_value = $cost; ///nilai saat ini
                    $aset->residual_value= $value6;
                    $aset->status = 'active';
                    $aset->save();
                    // tanggal akhir masa manfaat
                    // $tanggalDuaTahunKemudian = date('Y-m-d', strtotime($tanggalSaatIni . ' +2 years'));
                }
            }
        }else{
            // $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
            $this->fill($data);
            $this->type='KIB B';
            $this->residual_value= $value6;
            $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
            $this->no_register = $no_inventaris + 1;
            $this->accumulated_depreciation =0;
            // $this->residual_depresi = ($cost - $residu) / $request->useful;
            $this->book_value = $cost;
            $this->status = 'active';
            $this->save();
        }

        if($flagInv == 0){
            // $this->saveLogNotify();
            PerencanaanDetail::where('id',$request->usulan_id)->update(['status'=>'completed']);
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        }else{
            $this->saveLogNotify();
            $d= new Aset;
            $usulan = $request->usulan_id;
            $data = ['usulan_id' => $usulan, 'customValue'=>'B'];
            return $d->handleSubmitKib($data);
        }
    }


    public function handleStoreOrUpdateKibE($request){

        //    dd($request->all());
           $jumlah_item = $request->jumlah_semua; //jumlah semua - jumlah diimput
           $flagInv = $jumlah_item - $request->qty;
           $data = $request->all();
           if($request->qty > $jumlah_item ){
                return $this->rollback(__('Jumlah Tidak Sesuai'));
           }
           if($request->qty == 0){
                return $this->rollback(__('Jumlah Tidak Boleh Kosong'));
           }
            $value6 = str_replace(['.', ','],'',$request->residual_value);
            $residu = (int)$value6;
    
            $value7 = str_replace(['.', ','],'',$request->unit_cost);
            $cost = (int)$value7;
    
    
            if($request->qty > 1){
                if($request->no_factory_item !=null || $request->no_police_item !=null || $request->no_BPKB_item !=null || $request->no_machine_item != null){
                    return $this->rollback(__('Tidak Bisa Melakukan Percepatan Karena Spesifikas Detail Sama'));
                }else{
                    $no_inventaris=0;
                    for ($i = 0 ; $i < $request->qty ; $i++) {
                        $aset = new Aset();
                        $aset->fill($data);
                        $aset->type = 'KIB E';
                        $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
                        $aset->no_register = $no_inventaris + 1;
                        $aset->accumulated_depreciation = 0;
                        //nilai residu    = harga unit - residu / masa manfaat
                        // $aset->residual_depresi = ($cost - $residu) / $request->useful;
                        $aset->book_value = $cost; ///nilai saat ini
                        $aset->residual_value= $value6;
                        $aset->status = 'active';
                        $aset->save();
                        // $aset->saveLogNotify();
                        // tanggal akhir masa manfaat
                        // $tanggalDuaTahunKemudian = date('Y-m-d', strtotime($tanggalSaatIni . ' +2 years'));
                    }
                    
                }
            }else{
                // $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
                $this->fill($data);
                $this->type='KIB E';
                $this->residual_value= $value6;
                $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
                $this->no_register = $no_inventaris + 1;
                $this->accumulated_depreciation =0;
                // $this->residual_depresi = ($cost - $residu) / $request->useful;
                $this->book_value = $cost;
                $this->status = 'active';
                $this->save();
            }
    
            if($flagInv == 0){
                PerencanaanDetail::where('id',$request->usulan_id)->update(['status'=>'completed']);
                $redirect = route(request()->get('routes') . '.index');
                return $this->commitSaved(compact('redirect'));
            }else{
                $this->saveLogNotify();
                $d= new Aset;
                $usulan = $request->usulan_id;
                $data = ['usulan_id' => $usulan, 'customValue'=>'E'];
                return $d->handleSubmitKib($data);
            }
        }



    public function handleStoreOrUpdateKibA($request){

        //$jumlah_item = $request->jumlah_semua; //jumlah semua - jumlah diimput
        //$flagInv = $jumlah_item - $request->qty;
        $jumlah_item = $request->jumlah_semua; //jumlah semua - jumlah diimput
        $flagInv = $jumlah_item - $request->qty;

        $data = $request->all();

        $value6 = str_replace(['.', ','],'',$request->wide);
        $wide = (int)$value6;

        $value7 = str_replace(['.', ','],'',$request->unit_cost);
        $cost = (int)$value7;

        $sertif_date = Carbon::createFromFormat('d/m/Y', $request->sertificate_date);
 
        // $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
        $this->fill($data);
        $this->sertificate_date = $sertif_date;
        $this->type='KIB A';
        $this->book_value = $cost;
        $this->wide= $wide;
        $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
        $this->no_register = $no_inventaris + 1;
        $this->status = 'active';
        $this->save();
        $this->saveLogNotify();
         
        if($flagInv == 0){
            PerencanaanDetail::where('id',$request->usulan_id)->update(['status'=>'completed']);
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        }else{
            $this->saveLogNotify();
            $d= new Aset;
            $usulan = $request->usulan_id;
            $data = ['usulan_id' => $usulan, 'customValue'=>'A'];
            return $d->handleSubmitKib($data);
        }
        // PerencanaanDetail::where('id',$request->usulan_id)->update(['status'=>'completed']);
    }


    public function handleStoreOrUpdateKibC($request){
        $jumlah_item = $request->jumlah_semua; //jumlah semua - jumlah diimput
        $flagInv = $jumlah_item - $request->qty;

        $data = $request->all();

        $value6 = str_replace(['.', ','],'',$request->wide);
        $wide = (int)$value6;

        if($request->type == 'KIB C'){
            $value7 = str_replace(['.', ','],'',$request->wide_bld);
            $wide_bld = (int)$value7;
        }

        $sertif_date = Carbon::createFromFormat('d/m/Y', $request->sertificate_date);

        $value8 = str_replace(['.', ','],'',$request->residual_value);
        $residu = (int)$value8;

        $value9 = str_replace(['.', ','],'',$request->unit_cost);
        $cost = (int)$value9;
 
        // $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
        $this->fill($data);
        $this->residual_value= $residu;
        $this->sertificate_date = $sertif_date;
        // $this->type='KIB C';
        // if($type == 'F'){
        //     $this
        // }
        $this->wide= $wide;

        if($request->type == 'KIB C'){
            $this->wide_bld= $wide_bld;
        }

        $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
        $this->no_register = $no_inventaris + 1;
        $this->accumulated_depreciation = 0;
        $this->book_value = $cost;
        $this->status = 'active';
        $this->save();
        $this->saveLogNotify();
        if($flagInv == 0){
            PerencanaanDetail::where('id',$request->usulan_id)->update(['status'=>'completed']);
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        }else{
            $this->saveLogNotify();
            $d= new Aset;
            $usulan = $request->usulan_id;
            $data = ['usulan_id' => $usulan, 'customValue'=> $request->cst_val];
            return $d->handleSubmitKib($data);
        }
    }


    public function handleStoreOrUpdateKibD($request){
        $jumlah_item = $request->jumlah_semua; //jumlah semua - jumlah diimput
        $flagInv = $jumlah_item - $request->qty;

        $data = $request->all();

        $value6 = str_replace(['.', ','],'',$request->wide);
        $wide = (int)$value6;

        $value7 = str_replace(['.', ','],'',$request->long_JJR);
        $long_JJR = (int)$value7;

        $value8 = str_replace(['.', ','],'',$request->width_JJR);
        $width_JJR = (int)$value8;

        $value9 = str_replace(['.', ','],'',$request->residual_value);
        $residu = (int)$value9;

        $value10 = str_replace(['.', ','],'',$request->unit_cost);
        $cost = (int)$value10;

        $sertif_date = Carbon::createFromFormat('d/m/Y', $request->sertificate_date);
 
        // $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
        $this->fill($data);
        $this->sertificate_date = $sertif_date;
        $this->residual_value= $residu;
        $this->type='KIB D';
        $this->wide= $wide;
        $this->long_JJR= $long_JJR;
        $this->width_JJR= $width_JJR;
        $no_inventaris = Aset::where('coa_id',$request->coa_id)->count();
        $this->no_register = $no_inventaris + 1;
        $this->accumulated_depreciation = 0;
        // $this->residual_depresi = ($cost - $residu) / $request->useful;
        $this->book_value = $cost;
        $this->status = 'active';
        $this->save();
        $this->saveLogNotify();

        if($flagInv == 0){
            PerencanaanDetail::where('id',$request->usulan_id)->update(['status'=>'completed']);
            $redirect = route(request()->get('routes') . '.index');
            return $this->commitSaved(compact('redirect'));
        }else{
            $this->saveLogNotify();
            $d= new Aset;
            $usulan = $request->usulan_id;
            $data = ['usulan_id' => $usulan, 'customValue'=>'KIB D'];
            return $d->handleSubmitKib($data);
        }

    }


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
        if($this->usulans != null){
            $data = $this->usulans->asetd->name;
        }else{
            $m = Aset::with('usulans.asetd')->latest()->first();
            $data = $m->usulans->asetd->name;
        }
        $user = auth()->user()->name;
        $routes = request()->get('routes');
        switch (request()->route()->getName()) {
            case $routes . '.storeDetailKibA':
                $this->addLog('Menambah Data Aset Tanah :' . $data);
                $pesan = $user.' Menambah Data Aset Tanah :' . $data;
                $this->sendNotification($pesan);
                break;
            case $routes . '.storeDetailKibB':
                $this->addLog('Menambah Data Aset Peralatan Mesin: ' . $data);
                $pesan = $user.' Menambah Data Aset Peralatan Mesin :' . $data;
                $this->sendNotification($pesan);
                break;
            case $routes . '.storeDetailKibC':
                $this->addLog('Menambah Data Aset Gedung Bangunan :' . $data);
                $pesan = $user.' Menambah Data Aset Gedung Bangunan :' . $data;
                $this->sendNotification($pesan);
                break;
            case $routes . '.storeDetailKibD':
                $this->addLog('Menambah Data Aset Jalan Jaringan Irigasi :' . $data);
                $pesan = $user.' Menambah Data Aset Jalan Jaringan Irigasi :' . $data;
                $this->sendNotification($pesan);
                break;
            case $routes . '.storeDetailKibE':
                $this->addLog('Menambah Data Aset Tetap Lainya :' . $data);
                $pesan = $user.' Menambah Data Aset Tetap Lainya :' . $data;
                $this->sendNotification($pesan);
                break;
            case $routes . '.storeDetailKibF':
                $this->addLog('Menambah Data Aset Pembangunan dalam Kontruksi : ' . $data);
                $pesan = $user.' Menambah Data Aset Pembangunan dalam Kontruksi : ' . $data;
                $this->sendNotification($pesan);
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

    public function sendNotification($pesan)
    {
        $chatId = '-4054507555'; // Ganti dengan chat ID penerima notifikasi

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $pesan,
        ]);
    }

   

}
