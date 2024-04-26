<?php

namespace App\Http\Controllers\Master\Coa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Coa\CoaRequest;
use App\Models\Globals\Menu;
use App\Models\Master\Coa\COA;
use App\Support\Base;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CoaTanahController extends Controller
{
    //
    protected $module = 'master_coa_tanah';
    protected $routes = 'master.coa.tanah';
    protected $views  = 'master.coa';
    protected $perms = 'master';
    private $datas;

    public function __construct()
    {
        $this->prepare(
            [
                'module' => $this->module,
                'routes' => $this->routes,
                'views' => $this->views,
                'perms' => $this->perms,
                'permission' => $this->perms . '.view',
                'title' => 'Chart of Accounts Tanah',
                'breadcrumb' => [
                    'Data Master' => rut($this->routes . '.index'),
                    'Chart Of Accouts' => rut($this->routes . '.index'),
                    'COA Tanah' => rut($this->routes . '.index'),
                ]
            ]
        );
    }
    
    public function grid()
    {
        $user = auth()->user();
        $records = Coa::grid()->where('tipe_akun','=','KIB A')->filters()->dtGet();

        return DataTables::of($records)
            ->addColumn(
                'num',
                function ($record) {
                    return request()->start;
                }
            )
            ->addColumn(
                'kode_akun',
                function ($record) {
                    return $record->kode_akun;
                }
            )
            ->addColumn(
                'nama_akun',
                function ($record) {
                    return $record->nama_akun;
                }
            )
            ->addColumn(
                'tipe_akun',
                function ($record) {
                    return $record->tipe_akun;
                }
            )
            ->addColumn(
                'updated_by',
                function ($record) {
                    return $record->createdByRaw();
                }
            )
            ->addColumn('action', function ($record) use ($user) {
                $actions = [];
                if ($record->checkAction('show', $this->perms)) {
                    $actions[] = 'type:show|id:' . $record->id;
                }
                if (auth()->user()->hasRole('BPKAD')) {
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
                // if ($record->checkAction('edit', $this->perms)) {
                //     $actions[] = 'type:edit|id:' . $record->id;
                // }
                // if ($record->checkAction('delete', $this->perms) && ($record->nama_akun != "Bank" && $record->nama_akun != "Ump")) {
                //     $actions[] = [
                //         'type' => 'delete',
                //         'id' => $record->id,
                //         'attrs' => 'data-confirm-text="' . __('Hapus Parameter Chart of Accounts (COA) ') .$record->kode_akun . '?"',
                //     ];
                // }
                // if ($record->checkAction('show', $this->perms)) {
                //     $actions[] = 'type:show|id:' . $record->id;
                // }
                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns(
                [
                    'kode_akun',
                    'nama_akun',
                    'tipe_akun',
                    //'deskripsi',
                    'updated_by',
                    'action'
                ]
            )
            ->make(true);
    }

    public function index()
    {
        $data["baseContentReplace"] = true;
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num'),
                    $this->makeColumn('name:kode_akun|label:Kode Akun|className:text-left'),
                    $this->makeColumn('name:nama_akun|label:Nama Akun|className:text-left'),
                    $this->makeColumn('name:tipe_akun|label:Tipe Akun Utama|className:text-center'),
                    //$this->makeColumn('name:deskripsi|label:Deskripsi|width:200px|classname:text-left'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);

        return $this->render($this->views . '.index', $data);
    }

    public function create(){
        $page_action = "create";
        $tipe_akun = "KIB A";
        $baseContentReplace = "base-modal--render";
        return $this->render($this->views . '.create',compact("tipe_akun"));
    }

    public function show(COA $record){
        $page_action = "show";
        $tipe_akun = "KIB A";
        return $this->render($this->views . '.detail', compact("page_action", "record","tipe_akun"));
    }
    public function store(CoaRequest $request){
        $record = new COA;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(COA $record)
    {
        $page_action = "edit";
        $tipe_akun = "KIB A";
        return $this->render($this->views.'.detail', compact("page_action","record","tipe_akun"));
    }


    // public function edit(COA $record){
    //     $this->prepare(
    //         [
    //             'breadcrumb' => [
    //                 'Konsol Admin' => route($this->routes . '.index'),
    //                 'Parameter' => route($this->routes . '.index'),
    //                 'Jurnal' => route($this->routes . '.index'),
    //                 'COA' => route($this->routes . '.index'),
    //                 'Detil' => route($this->routes . '.edit', $record->id),
    //             ]
    //         ]
    //     );
    //     $page_action = "edit";
    //     return $this->render($this->views . '.detail', compact("page_action", "record"));
    // }

    public function update(COA $record, CoaRequest $request){
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(COA $record){
        return $record->handleDestroy();
    }

    public function getDetailCOA(Request $request){
        $id_akun = $request->id_akun;
        $coa = COA::where('id', $id_akun)->first();
        return response()->json([
            'kode_akun' => $coa->kode_akun,
            'tipe_akun' => $coa->tipe_akun,
        ]);
    }

}
