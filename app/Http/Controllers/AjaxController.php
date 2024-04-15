<?php

namespace App\Http\Controllers;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Globals\Notification;
use App\Models\Globals\TempFiles;
use App\Models\Master\Coa\COA;
use App\Models\Master\Aset\AsetRs;
use App\Models\Pemeliharaan\PemeliharaanDetail;
use App\Models\Perbaikan\TransPerbaikanDisposisi;
use App\Models\Perbaikan\UsulanSperpat;
use App\Models\Inventaris\Aset;
use App\Models\Master\Location\Location;
use App\Models\Master\Pengadaan\Pengadaan;
use App\Models\Master\Pemutihan\Pemutihan;
use App\Models\Pengajuan\Perbaikan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Master\Dana\Dana;
use App\Models\Master\Geografis\City;
use App\Models\Master\Geografis\Province;
use App\Models\Master\Geografis\District;
use App\Models\Master\Vendor\TypeVendor;
use App\Models\Master\HakTanah\HakTanah;
use App\Models\Master\StatusTanah\StatusTanah;
use App\Models\Master\BahanAset\BahanAset;
use App\Models\Master\Vendor\Vendor;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use Illuminate\Http\Request;
use Carbon\Carbon;

// use App\Models\Geografis\Province;
use Illuminate\Support\Str;



class AjaxController extends Controller
{

    public function saveTempFiles(Request $request)
    {
        $this->beginTransaction();
        $mimes = null;
        if ($request->accept == '.xlsx') {
            $mimes = 'xlsx';
        }
        if ($request->accept == '.png, .jpg, .jpeg') {
            $mimes = 'png,jpg,jpeg';
        }
        if ($mimes) {
            $request->validate(
                ['file' => ['mimes:' . $mimes]]
            );
        }
        try {
            if ($file = $request->file('file')) {
                $file_path = str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName());
                $file_path .= '-' . time() . '.' . $file->getClientOriginalExtension();

                $temp = new TempFiles;
                $temp->file_name = $file->getClientOriginalName();
                $temp->file_path = $file->storeAs('temp-files', $file_path, 'public');
                // $temp->file_type = $file->extension();
                $temp->file_size = $file->getSize();
                $temp->flag = $request->flag;
                $temp->save();
                return $this->commit(
                    [
                        'file' => TempFiles::find($temp->id)
                    ]
                );
            }
            return $this->rollback(['message' => 'File not found']);
        } catch (\Exception $e) {
            return $this->rollback(['error' => $e->getMessage()]);
        }
    }
    public function testNotification($emails)
    {
        if ($rkia = Rkia::latest()->first()) {
            request()->merge(
                [
                    'module' => 'rkia_operation',
                ]
            );
            $emails = explode('--', trim($emails));
            $user_ids = User::whereIn('email', $emails)->pluck('id')->toArray();
            $rkia->addNotify(
                [
                    'message' => 'Waiting Approval RKIA ' . $rkia->show_category . ' ' . $rkia->year,
                    'url' => rut('rkia.operation.summary', $rkia->id),
                    'user_ids' => $user_ids,
                ]
            );
            $record = Notification::latest()->first();
            return $this->render('mails.notification', compact('record'));
        }
    }

    public function userNotification()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->simplePaginate(25);
        return $this->render('layouts.base.notification', compact('notifications'));
    }

    public function userNotificationRead(Notification $notification)
    {
        auth()->user()
            ->notifications()
            ->updateExistingPivot($notification, array('readed_at' => now()), false);
        return redirect($notification->full_url);
    }

    public function selectKib(Request $request)
    {
        // $with_where_has_fn = function ($q) use ($request) {
        //     $q->orderBy('nama_aktiva');
        // };
        
        $items = Aset::with('asetData')->where('status', 'notactive')
        ->where('condition', 'rusak berat')
        ->get();
    
        // return $items;
    $unique_items = collect();
    
    foreach ($items as $item) {
        $is_duplicate = $unique_items->contains(function ($unique_item) use ($item) {
            // Periksa apakah ada duplikat berdasarkan merek_type_item
            // if($unique_item->merek_type_item === $item->merek_type_item && $unique_item->coa_id === $item->coa_id && $unique_item->no_factory_item === $item->no_factory_item || $unique_item->no_frame === $item->no_frame ){
                return $unique_item->merek_type_item === $item->merek_type_item && $unique_item->coa_id === $item->coa_id && $unique_item->no_factory_item === $item->no_factory_item && $unique_item->no_frame === $item->no_frame;
            // }
          //  return $unique_item->merek_type_item === $item->merek_type_item && $unique_item->coa_id === $item->coa_id  ;
        });
    
        if (!$is_duplicate) {
            $unique_items->push($item);
        }
    }
    
    $results = $unique_items->map(function ($item) {
        
        return ['id' => $item->id, 'text' => $item->usulans->asetd->name. ' - ' . $item->merek_type_item];
    });
    
    $more = false; // Karena kita tidak menggunakan paginate()
    
    return response()->json(compact('results', 'more'));
    
    }

    public function getKibById(Request $request)
    {
        $item = Aset::with('asetData')->where('id',$request->id)->where('status','notactive')->where('condition','rusak berat')->get();
        // $item = AsetRs::where('id')
        return $item;
    }

    public function cekSperpat(Request $request)
    {
        $flags = 
        TransPerbaikanDisposisi::where('perbaikan_id', $request->id)
        ->where('vendor_id', $request->vendor)
        ->where('repair_type', $request->tip)
        ->first();

        
        if ($flags) {
            $item = UsulanSperpat::where('trans_perbaikan_id', $flags->id)->count();
        }else{
            $item =0;
        }
        return $item;
    }


    public function checkAset(Request $request)
    {   
        $dep = $request->dep;
        $loc = Location::where('departemen_id', $dep)->pluck('id')->toArray();
        //ambil data departemen x , dapat data roomnya yaitu room 1 2 3 -> ambil id rooms
        // return $loc;
        $item = Aset::where('id', $request->id)
            ->whereIn('location_hibah_aset',$loc)->orWhereIn('room_location',$loc) //where room di dalam id data
            ->where('status', 'clean') // Mengubah 'actives' menjadi 'active'
            ->count();
        
        // $item2 = Aset::where('id', $request->id)
        // ->WhereIn('location_hibah_aset',$loc) //where room di dalam id data
        // ->where('status', 'clean') // Mengubah 'actives' menjadi 'active'
        // ->count();

        // return $item2;

        //$item = $item1+$item2;

        return $item;
    }

    // checkAset

    public function selectRole($search, Request $request)
    {
        $items = Role::where('name', '!=', 'Administrator')->keywordBy('name')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;

            case 'approver':
                $perms = str_replace('_', '.', $request->perms) . '.approve';
                $items = $items->whereHas(
                    'permissions',
                    function ($q) use ($perms) {
                        $q->where('name', $perms);
                    }
                );
                break;

            default:
                $items = $items->whereNull('id');
                break;
        }
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectLevelPosition($search, Request $request)
    {
        $items = LevelPosition::keywordBy('name')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            case 'find':
                return $items->find($request->id);
            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }


    public function selectRooms($search, Request $request)
    {
        $items = Location::keywordBy('name')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            case 'find':
                return $items->find($request->id);
            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectPerbaikan()
    {
        $items = Perbaikan::where('status','approved')->where('repair_results','BELUM')->where('is_disposisi','yes');
        $items = $items->paginate();
        return $this->responseSelect2($items, 'code', 'id');
    }


    public function selectCostComponent($search, Request $request)
    {
        $items = CostComponent::keywordBy('name')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            case 'find':
                return $items->find($request->id);

            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectLevelJabatan($search, Request $request){
        $items = OrgStruct::keywordBy('name')->orderBy('level')->orderBy('name');
        switch ($search) {
            case 'by_level':
                $items = $items->where('level', $request->level_id);
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');

    }

    public function selectPemeliharaan(Request $request){

        $imKepalaDepartemen =$request->input('departemen_id');
        if($imKepalaDepartemen){
            $search = 'parent_subsection';
        }   

        $time = Carbon::now();
        $allOrg = OrgStruct::pluck('id')->toArray(); // Ambil ID dari OrgStruct
        $items = OrgStruct::orderBy('name');

        $data = OrgStruct::orderBy('name');
        $org = $data->whereHas('pemeliharaan', function ($q) use ($time) {
            $q->whereMonth('maintenance_date', $time->month);
        })->pluck('id')->toArray(); 

        //$arr = array_diff($allOrg, $org);

        //$items = $items->whereIn('id', $arr)->get();
        //dd($arr);
        //dd($departmentsToDisplay);

        $items = $items->when(
            $not = $org,
            function ($q) use ($not) {
                $q->whereNotIn('id',$not);
            }
        )->get();

        $results = [];
        $more = false;

        $levels = ['root','bod', 'department', 'subdepartment', 'subsection'];
        $i = 0;
        
        if($imKepalaDepartemen){
            foreach ($levels as $level) {
                if ($items->where('level', $level)->count()) {
                    foreach ($items->where('level', $level) as $item) {
                        if($item->parent_id == $request->input('departemen_id') ){
                           // $results[$i]['text'] = strtoupper($item->show_level);
                            $results[$i]['children'][] = ['id' => $item->id, 'text' => $item->name];
                        }
                    }
                    $i++;
                }
            }
            $departemen  = OrgStruct::where('id',$imKepalaDepartemen)->first();
            array_unshift($results, ['id' => $departemen->id, 'text' => $departemen->name]);
        }else{
            foreach ($levels as $level) {
                if ($items->where('level', $level)->count()) {
                    foreach ($items->where('level', $level) as $item) {
                        $results[$i]['text'] = strtoupper($item->show_level);
                        $results[$i]['children'][] = ['id' => $item->id, 'text' => $item->name];
                    }
                    $i++;
                }
            }
        }
        
        return response()->json(compact('results', 'more'));


    }

    public function selectStruct($search, Request $request)
    {
        $imKepalaDepartemen =$request->input('departemen_id');
        if($imKepalaDepartemen){
            $search = 'parent_subsection';
        }   

        $items = OrgStruct::keywordBy('name')->orderBy('level')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            case 'object_aset':
                $items = $items->whereIn('level', ['department', 'subdepartment', 'subsection']);
                break;
            case 'parent_bod':
                $items = $items->whereIn('level', ['root']);
                break;
            case 'parent_department':
                $items = $items->whereIn('level', ['bod']);
                break;
            case 'parent_subdepartment':
                $items = $items->whereIn('level', ['department']);
                break;
            case 'parent_subsection':
                $items = $items->whereIn('level', ['subdepartment']);
                break;
            case 'by_level':
                $req = $request->input('level_id');
                $items = $items->whereIn('level', [$req]);
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->when(
            $not = $request->not,
            function ($q) use ($not) {
                $q->where('id', '!=', $not);
            }
        )->get();
        $results = [];
        $more = false;

        $levels = ['root','bod', 'department', 'subdepartment', 'subsection'];
        $i = 0;
        
        if($imKepalaDepartemen){
            foreach ($levels as $level) {
                if ($items->where('level', $level)->count()) {
                    foreach ($items->where('level', $level) as $item) {
                        if($item->parent_id == $request->input('departemen_id') ){
                           // $results[$i]['text'] = strtoupper($item->show_level);
                            $results[$i]['children'][] = ['id' => $item->id, 'text' => $item->name];
                        }
                    }
                    $i++;
                }
            }
            $departemen  = OrgStruct::where('id',$imKepalaDepartemen)->first();
            array_unshift($results, ['id' => $departemen->id, 'text' => $departemen->name]);
           // $results[] = ['id' => $departemen->id, 'text' => $departemen->name];
        }else{
            foreach ($levels as $level) {
                if ($items->where('level', $level)->count()) {
                    foreach ($items->where('level', $level) as $item) {
                        $results[$i]['text'] = strtoupper($item->show_level);
                        $results[$i]['children'][] = ['id' => $item->id, 'text' => $item->name];
                    }
                    $i++;
                }
            }
        }
        
        return response()->json(compact('results', 'more'));
    }

    public function selectPosition($search, Request $request)
    {
        $items = Position::keywordBy('name')->orderBy('name');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            case 'by_location':
                //dd($request->org_struct);
                $req = $request->input('org_struct');
                $items = $items->where('location_id', $req);
                break;
            case 'divisi_spi':
                $location_id = OrgStruct::where('name', 'Satuan Pengawas Internal')->firstOrFail();
                $items = $items->where('location_id', $location_id);
                break;
            case 'auditor':
                $items = $items->whereHas(
                    'location',
                    function ($qq) {
                        $qq->inAudit();
                    }
                );
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }


    public function selectDetailUsulan($search)
    {
   
        $query = PerencanaanDetail::with('asetd')
        ->select('ref_aset_id', \DB::raw('COUNT(*) as total'))
        ->where('status', 'waiting.purchase');
        $items = $query->groupBy('ref_aset_id')->paginate();
        
        $items->getCollection()->transform(function ($item) {
            $item->aset_id = $item->asetd->id;
            $item->aset_name = $item->asetd->name;
            return $item;
        });

        return $this->responseSelect2($items, 'aset_name','aset_id');
    }

    public function selectAsetBeli($search, Request $request)
    {
        $req = $request->input('aset_id');

        $items = PerencanaanDetail::with(['asetd','perencanaan.struct.name'])
            ->select('ref_aset_id', 'id', 'desc_spesification', 'qty_agree')
            ->where('ref_aset_id', $req)
            ->where('status', 'waiting.purchase')->get();
        $results = [];

       // $structNames = $items->pluck('perencanaan.struct.name');


        foreach ($items as $item) {
            $results[] = [
                'id' => $item->id,
                'text' => "Aset".' : '.$item->asetd->name.', '.'Spesifikasi'.' : '.$item->desc_spesification.', '.'Jumlah'.' : '.$item->qty_agree.', '.'Departemen'.' : '.$item->struct->name,
                // 'spesifikasi' => $item->desc_spesification,
                // 'jumlah' => $item->qty_agree,
            ];
        }
    
        return response()->json(compact('results'));
        //$items = $items->paginate();

    //     $results = [];
    //    // $more = $items->hasMorePages();

    //     foreach ($items as $item) {
    //         $results[] = [
    //             'id' => $item->id,
    //             'Nama Aset' => $item->asetd->name,
    //             'Spesifikasi' => $item->desc_spesification,
    //             'jumlah pembelian' => $item->qty_agree,
    //         ];
    //     }
        //return response()->json(compact('results'));

        // $results = [];
        // $more = $items->hasMorePages();
        // foreach ($items as $item) {
        //     $results[] = ['id' => $item->id, 'text' => $item->name . ' (' . ($item->position->name ?? '') . ')'];
        // }
        // return response()->json(compact('results', 'more'));

    }



    public function selectUser($search, Request $request)
    {
        $items = User::keywordBy('name')
            ->has('position')
            ->where('status', 'active')
            ->orderBy('name');

        switch ($search) {
            case 'all':
                $items = $items
                    ->when(
                        $with_admin = $request->with_admin,
                        function ($q) use ($with_admin) {
                            $q->orWhere('id', 1);
                        }
                    );
                break;
            case 'level_bod':
                $items = $items->whereHas(
                    'position',
                    function ($q) {
                        $q->whereHas(
                            'location',
                            function ($qq) {
                                $qq->where('level', 'bod');
                            }
                        );
                    }
                );
                break;
            case 'level_department':
                $items = $items->whereHas(
                    'position',
                    function ($q) {
                        $q->whereHas(
                            'location',
                            function ($qq) {
                                $qq->where('level', 'department');
                            }
                        );
                    }
                );
                break;
            case 'org_struct':
                $req = $request->input('org_struct');
                $items = $items->whereHas('position',function ($q) use($req) {
                        $q->whereHas(
                            'location',
                            function ($qq) use ($req) {
                                $qq->where('location_id', $req);
                            }
                        );
                    }
                );
                break;
            case 'sarpras':
                $items = $items->whereHas(
                    'position',
                    function ($q) {
                        $q->whereHas(
                            'location',
                            function ($qq) {
                                $qq->where('name', 'IPSRS');
                            }
                        );
                    }
                );
                break;
            case 'BPKAD':
                $items = $items->whereHas(
                    'position',
                    function ($q) {
                        $q->whereHas(
                            'location',
                            function ($qq) {
                                $qq->where('location_id', 55);
                            }
                        );
                    }
                );
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }
        $items = $items->paginate();

        $results = [];
        $more = $items->hasMorePages();
        foreach ($items as $item) {
            $results[] = ['id' => $item->id, 'text' => $item->name . ' (' . ($item->position->name ?? '') . ')'];
        }
        return response()->json(compact('results', 'more'));
    }

    public function selectCoa($search, Request $request){
        $items = COA::keywordBy('nama_akun')->orderBy('nama_akun');
        // $search = $request->input('coa_id');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            case 'a':
                $items = $items->where('tipe_akun','KIB A');
                break;
            case 'b':
                $items = $items->where('tipe_akun','KIB B');
                break;
            case 'c':
                $items = $items->where('tipe_akun','KIB C');
                break;
            case 'd':
                $items = $items->where('tipe_akun','KIB D');
                break;
            case 'e':
                $items = $items->where('tipe_akun','KIB E');
                break;
            case 'f':
                $items = $items->where('tipe_akun','KIB F');
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->when(
            $not = $request->not,
            function ($q) use ($not) {
                $q->where('id', '!=', $not);
            }
        )->get();
        $results = [];
        $more = false;

        $tipe_akuns = ['KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'KIB F'];
        $i = 0;
        foreach ($tipe_akuns as $tipe_akun) {
            if ($items->where('tipe_akun', $tipe_akun)->count()) {
                foreach ($items->where('tipe_akun', $tipe_akun) as $item) {
                    $results[$i]['text'] = strtoupper($item->show_tipe_akun);
                    $results[$i]['children'][] = ['id' => $item->id, 'text' => $item->nama_akun.' ( '.$item->kode_akun.' ) '];
                }
                $i++;
            }
        }
        return response()->json(compact('results', 'more'));

    }


    public function selectAsetRS($search, Request $request){
        $items = AsetRs::keywordBy('name')->orderBy('jenis_aset');
        switch ($search) {
            case 'all':
                $items = $items;
                break;
            default:
                $items = $items->whereNull('id');
                break;
        }

        $items = $items->when(
            $not = $request->not,
            function ($q) use ($not) {
                $q->where('id', '!=', $not);
            }
        )->get();
        $results = [];
        $more = false;

        $jenis_asets = ['Tanah', 'Peralatan Mesin', 'Gedung Bangunan', 'Jalan Irigasi Jaringan', 'Aset Tetap Lainya'];
        $i = 0;
        foreach ($jenis_asets  as $tipe_akun) {
            if ($items->where('jenis_aset', $tipe_akun)->count()) {
                foreach ($items->where('jenis_aset', $tipe_akun) as $item) {
                    $results[$i]['text'] = strtoupper($item->jenis_aset);
                    $results[$i]['children'][] = ['id' => $item->id, 'text' => $item->name];
                }
                $i++;
            }
        }
        return response()->json(compact('results', 'more'));
    }

    public function selectAsetKib($search, Request $request){
        $pem = $request->input('pem'); // id pem
        $peliharaan = PemeliharaanDetail::with('pemeliharaan')->where('pemeliharaan_id', $pem)->orderBy('kib_id')->pluck('kib_id')->toArray();
        //dd($peliharaan);
        $excludedIds = $peliharaan; // ID aset yang tidak boleh tampil
        $req = $request->input('lokasi');
        // dd($req);
        $items = Aset::with('usulans')
            ->where('condition', 'baik')
            ->where('status', 'actives')
            ->whereNotIn('id',$peliharaan)
            ->where(function ($query) use ($req) {
                $query->orWhere(function ($q) use ($req) {
                    $q->whereHas('usulans', function ($qq) use ($req) {
                        $qq->whereHas('perencanaan', function ($qqq) use ($req) {
                            $qqq->where('struct_id', $req);
                        });
                    });
                })
                ->orWhere('location_hibah_aset', $req);
            })->orderBy('type');

        $items = $items->when(
            $not = $request->not,
            function ($q) use ($not) {
                $q->where('id', '!=', $not);
            }
        )->get();

        $results = [];
        $more = false;

        $jenis_asets = ['KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E','KIB F'];
        $i = 0;
        foreach ($jenis_asets  as $tipe_akun) {
            if ($items->where('type', $tipe_akun)->count()) {
                foreach ($items->where('type', $tipe_akun) as $item) {
                    $results[$i]['text'] = strtoupper($item->type);
                    $results[$i]['children'][] = ['id' => $item->id, 'text' => $item->usulans->asetd->name . ($item->merek_type_item ? ' (Merek ' . $item->merek_type_item . ')' : '( Merek Tidak Tersedia)')];
                }
                $i++;
            }
        }

        return response()->json(compact('results', 'more'));
    }

    public function  selectAsetItem($search, Request $request){
        $req = $request->input('ids');
        $items = Aset::where('id',$req);
        $items = $items->paginate();
        return $this->responseSelect2($items, 'merek_type_item', 'id');
    }

    public function selectCity($search, Request $request){
        $req = $request->input('province_id');
        $items = City::where('province_id',$req);
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectRoom($search, Request $request){
        $req = $request->input('departemen_id');
        $items = Location::where('departemen_id',$req);
       // return $req;
        $items = $items->paginate();

        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectDistrict($search, Request $request){
        $req = $request->input('city_id');
        $items = District::where('city_id',$req);
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectProvince($search, Request $request){
        $items = Province::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectVendor($search, Request $request){
        $items = Vendor::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectJenisPengadaan($search, Request $request){
        $items = Pengadaan::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectJenisPemutihan($search, Request $request){
        $items = Pemutihan::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectSSBiaya($search, Request $request){
        $items = Dana::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name', 'id');
    }

    public function selectJenisUsaha($search, Request $request){
        $items = TypeVendor::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name','id');
    }

    public function selectStatusTanah($search, Request $request){
        $items = StatusTanah::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name','id');
    }

    public function selectHakTanah($search, Request $request){
        $items = HakTanah::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name','id');
    }

    public function selectBahanAset($search, Request $request){
        $items = BahanAset::all();
        $items = $items->paginate();
        return $this->responseSelect2($items, 'name','id');
    }
}
