<?php

namespace App\Http\Controllers\Master\Pengadaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Pengadaan\PengadaanRequest;
use App\Models\Globals\Menu;
use App\Models\Master\Pengadaan\Pengadaan;
use App\Support\Base;
use Carbon\Carbon;
use Illuminate\Http\Request;
//use Yajra\DataTables\Facades\DataTables;

class PengadaanController extends Controller
{
    //
    protected $module = 'master_pengadaan';
    protected $routes = 'master.data-pengadaan';
    protected $views  = 'master.pengadaan';
    protected $perms = 'master';
    //private $datas;

    public function __construct()
    {
        $this->prepare(
            [
                'module' => $this->module,
                'routes' => $this->routes,
                'views' => $this->views,
                'perms' => $this->perms,
                'permission' => $this->perms . '.view',
                'title' => 'Jenis Pengadaan',
                'breadcrumb' => [
                    'Data Master' => rut($this->routes . '.index'),
                    // 'Jenis Pengadaan' => rut($this->routes . '.index'),
                    'Pengadaan' => rut($this->routes . '.index'),
                ]
            ]
        );
    }

    public function index()
    {
        //$data["baseContentReplace"] = true;
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num'),
                    $this->makeColumn('name:name|label:Jenis Pengadaan|className:text-left'),
                   // $this->makeColumn('name:jenis_pengadaan|label:Jenis Pengadaan|className:text-left'),
                    $this->makeColumn('name:description|label:Deskrpsi|className:text-left'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);

        return $this->render($this->views . '.index');
    }
    
    public function grid()
    {
        $user = auth()->user();
        $records = Pengadaan::grid()->filters()->dtGet();

        return \DataTables::of($records)
            ->addColumn(
                'num',
                function ($record) {
                    return request()->start;
                }
            )
            ->addColumn(
                'name',
                function ($record) {
                   return $record->name;
                }
            )
            // ->addColumn(
            //     'jenis_pengadaan',
            //     function ($record) {
            //        return $record->jenis_pengadaan;
            //     }
                // 'jenis_aset',
                // function ($record) {
                //     if($record->jenis_aset=='tanah'){
                //         return 'Tanah';
                //     }elseif($record->jenis_aset=='peralatan_mesin'){
                //         return 'Peralatan dan Mesin';
                //     }elseif($record->jenis_aset=='gedung_bangunan'){
                //         return 'Gedung dan Bangunan';
                //     }elseif($record->jenis_aset=='jalan_irigasi_jaringan'){
                //         return 'Jalan Irigasi Jaringan';
                //     }elseif($record->jenis_aset=='aset_tetap_lainya'){
                //         return 'Aset Tetap Lainya';
                //     }
                // }
            //)
            ->addColumn(
                'description',
                function ($record) {
                    return $record->description;
                }
            )
            ->addColumn(
                'updated_by',
                function ($record) {
                    return $record->createdByRaw();
                }
            )
            ->addColumn('action',function ($record) use ($user) {
                    $actions = [
                        'type:show|id:' . $record->id,
                        // 'type:edit|id:' . $record->id,
                    ];
                    if (auth()->user()->hasRole('PPK') || auth()->user()->hasRole('Sub Bagian Program Perencanaan')) {
                        $actions[] = [
                            'type' => 'edit',
                            'id' => $record->id,
                        ];
                        $actions[] = [
                            'type' => 'delete',
                            'id' => $record->id,
                            'attrs' => 'data-confirm-text="'.__('Hapus').' '.$record->name.'?"',
                        ];
                    }
                    // if ($record->canDeleted()) {
                    //     $actions[] = [
                    //         'type' => 'delete',
                    //         'id' => $record->id,
                    //         'attrs' => 'data-confirm-text="' . __('Hapus') . ' ' . $record->name . '?"',
                    //     ];
                    // }
                    return $this->makeButtonDropdown($actions);
                }
            )
            ->rawColumns(['action','name','description','updated_by'])
            ->make(true);
    }


    public function create(){
        $baseContentReplace = "base-modal--render";
        return $this->render($this->views . '.create');
    }
   
    public function show(Pengadaan $record){
        return $this->render($this->views . '.show',compact('record'));
    }

    public function store(PengadaanRequest $request){
        $record = new Pengadaan;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(Pengadaan $record)
    {
        return $this->render($this->views.'.edit',compact('record'));
    }


    public function update(Pengadaan $record, PengadaanRequest $request){
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(Pengadaan $record){
        return $record->handleDestroy();
    }

    public function getDetailAset(Request $request){
        $id_akun = $request->id;
        $aset = Pengadaan::where('id', $id)->first();
        return response()->json([
            'name' => $aset->name,
           /// 'jenis_pengadaan' => $aset->jenis_pengadaan,
            'description' => $aset->description,
        ]);
    }

}
