<?php

namespace App\Http\Controllers\Master\HakTanah;

use App\Exports\GenerateExport;
use App\Http\Controllers\Controller;
use App\Models\Master\HakTanah\HakTanah;
use App\Support\Base;
use App\Http\Requests\Master\HakTanah\HakTanahRequest;

class HakTanahController extends Controller
{
    protected $module = 'master_hakTanah';
    protected $routes = 'master.hakTanah';
    protected $views = 'master.hakTanah';
    protected $perms = 'master';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms.'.view',
            'title' => 'Master Hak Tanah',
            'breadcrumb' => [
                'Data Master' => rut($this->routes . '.index'),
                'Hak Tanah' => rut($this->routes . '.index'),
            ]
        ]);
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num'),
                    $this->makeColumn('name:name|label:Nama Hak Tanah|className:text-left'),
                    $this->makeColumn('name:description|label:Deskrpsi|className:text-left'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);
        return $this->render($this->views.'.index');
    }

    public function grid()
    {
        $user = auth()->user();
        $records = HakTanah::grid()->filters()->dtGet();

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
            ->rawColumns(['action','name','description','updated_by'])
            ->make(true);
    }


    public function create(){
        $baseContentReplace = "base-modal--render";
        return $this->render($this->views . '.create');
    }

    public function show(HakTanah $record){
        return $this->render($this->views . '.show',compact('record'));
    }

    public function store(HakTanahRequest $request){
        $record = new HakTanah;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(HakTanah $record)
    {
        return $this->render($this->views.'.edit',compact('record'));
    }


    public function update(HakTanah $record, HakTanahRequest $request){
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(HakTanah $record){
        return $record->handleDestroy();
    }

    public function getDetailAset(Request $request){
        $id_akun = $request->id;
        $aset = HakTanah::where('id', $id)->first();
        return response()->json([
            'name' => $aset->name,
           /// 'jenis_pengadaan' => $aset->jenis_pengadaan,
            'description' => $aset->description,
        ]);
    }

}
