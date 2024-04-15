<?php

namespace App\Http\Controllers\Master\StatusTanah;

use App\Exports\GenerateExport;
use App\Http\Controllers\Controller;
use App\Models\Master\StatusTanah\StatusTanah;
use App\Support\Base;
use App\Http\Requests\Master\StatusTanah\StatusTanahRequest;

class StatusTanahController extends Controller
{
    protected $module = 'master_statusTanah';
    protected $routes = 'master.statusTanah';
    protected $views = 'master.statusTanah';
    protected $perms = 'master';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms.'.view',
            'title' => 'Master Status Tanah',
            'breadcrumb' => [
                'Data Master' => rut($this->routes . '.index'),
                'Status Tanah' => rut($this->routes . '.index'),
            ]
        ]);
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num'),
                    $this->makeColumn('name:name|label:Nama Status Tanah|className:text-left'),
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
        $records = StatusTanah::grid()->filters()->dtGet();

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
                        'type:edit|id:' . $record->id,
                    ];
                    if ($record->canDeleted()) {
                        $actions[] = [
                            'type' => 'delete',
                            'id' => $record->id,
                            'attrs' => 'data-confirm-text="' . __('Hapus') . ' ' . $record->name . '?"',
                        ];
                    }
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
   
    public function show(StatusTanah $record){
        return $this->render($this->views . '.show',compact('record'));
    }

    public function store(StatusTanahRequest $request){
        $record = new StatusTanah;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(StatusTanah $record)
    {
        return $this->render($this->views.'.edit',compact('record'));
    }


    public function update(StatusTanah $record, StatusTanahRequest $request){
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(StatusTanah $record){
        return $record->handleDestroy();
    }

    public function getDetailAset(Request $request){
        $id_akun = $request->id;
        $aset = StatusTanah::where('id', $id)->first();
        return response()->json([
            'name' => $aset->name,
           /// 'jenis_pengadaan' => $aset->jenis_pengadaan,
            'description' => $aset->description,
        ]);
    }

}
