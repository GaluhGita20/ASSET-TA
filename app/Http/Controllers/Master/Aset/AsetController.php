<?php

namespace App\Http\Controllers\Master\Aset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Aset\AsetRequest;
use App\Models\Globals\Menu;
use App\Models\Master\Aset\AsetRs;
use App\Support\Base;
use Carbon\Carbon;
use Illuminate\Http\Request;
//use Yajra\DataTables\Facades\DataTables;

class AsetController extends Controller
{
    //
    protected $module = 'master_aset';
    protected $routes = 'master.data-aset';
    protected $views  = 'master.aset';
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
                'title' => 'Jenis Aset Rumah Sakit',
                'breadcrumb' => [
                    'Data Master' => rut($this->routes . '.index'),
                    // 'Jenis Aset' => rut($this->routes . '.index'),
                    'Aset' => rut($this->routes . '.index'),
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
                    $this->makeColumn('name:name|label:Nama Aset|className:text-left'),
                   // $this->makeColumn('name:jenis_pengadaan|label:Jenis Pengadaan|className:text-left'),
                    $this->makeColumn('name:jenis_aset|label:Kategori Aset|className:text-left'),
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
        $records = AsetRs::grid()->filters()->dtGet();

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
            ->addColumn(
                'jenis_aset',
                function ($record) {
                    return $record->jenis_aset;
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
                    if (auth()->user()->hasRole('Sarpras')) {
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
            ->rawColumns(['action','name','jenis_aset','updated_by'])
            ->make(true);
    }


    public function create(){
        $baseContentReplace = "base-modal--render";
        return $this->render($this->views . '.create');
    }
   
    public function show(AsetRs $record){
        return $this->render($this->views . '.show',compact('record'));
    }

    public function store(AsetRequest $request){
        $record = new AsetRs;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(AsetRs $record)
    {
        return $this->render($this->views.'.edit',compact('record'));
    }


    public function update(AsetRs $record, AsetRequest $request){
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(AsetRs $record){
        return $record->handleDestroy();
    }

    public function getDetailAset(AsetRequest $request){
        $id_akun = $request->id;
        $aset = AsetRs::where('id', $id)->first();
        return response()->json([
            'name' => $aset->name,
           /// 'jenis_pengadaan' => $aset->jenis_pengadaan,
            'description' => $aset->description,
        ]);
    }

}
