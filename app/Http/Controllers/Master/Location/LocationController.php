<?php

namespace App\Http\Controllers\Master\Location;

use App\Exports\GenerateExport;
use App\Http\Controllers\Controller;
use App\Models\Master\Location\Location;
use App\Support\Base;
use App\Http\Requests\Master\Location\LocationRequest;
// use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    protected $module = 'master_location';
    protected $routes = 'master.location';
    protected $views = 'master.location.';
    protected $perms = 'master';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms.'.view',
            'title' => 'Master Ruang',
            'breadcrumb' => [
                'Data Master' => rut($this->routes . '.index'),
                'Lokasi' => rut($this->routes . '.index'),
                'Ruang' => rut($this->routes.'.index'),
            ]
        ]);
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num'),
                    $this->makeColumn('name:code|label:Kode|className:text-center'),
                    $this->makeColumn('name:name|label:Nama|className:text-left'),
                    $this->makeColumn('name:departemen|label:Unit|className:text-center'),
                    $this->makeColumn('name:pic_id|label:PIC|className:text-center'),
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
        $records = Location::grid()->filters()->dtGet();

        return \DataTables::of($records)
            ->addColumn('num', function ($record) {
                return request()->start;
            })
            ->addColumn('code', function ($record) {
                return  $record->space_code ?? null;
            })
            ->addColumn('name', function ($record) {
                return $record->name ?? null;
            })
            ->addColumn('departemen', function ($record) {
                return $record->orgLocation->name ?? null;
            })
            ->addColumn('pic_id', function ($record) {
                return $record->user->name ?? null;
            })
            ->addColumn('updated_by', function ($record) {
                return $record->createdByRaw();
            })
            ->addColumn('action', function ($record) use ($user) {
                $actions = [
                    'type:show|id:'.$record->id,
                    'type:edit|id:'.$record->id,
                ];
                if ($record->canDeleted()) {
                    $actions[] = [
                        'type' => 'delete',
                        'id' => $record->id,
                        'attrs' => 'data-confirm-text="'.__('Hapus').' '.$record->name.'?"',
                    ];
                }
                return $this->makeButtonDropdown($actions);
            })
            ->rawColumns(['action','updated_by', 'pic_id','departemen','name','code'])
            ->make(true);
    }

    public function create()
    {
        return $this->render($this->views.'create');
    }

    public function store(LocationRequest $request)
    {
        $record = new Location;
        return $record->handleStoreOrUpdate($request);
    }

    public function show(Location $record)
    {
        return $this->render($this->views.'show', compact('record'));
    }

    public function edit(Location $record)
    {
    
        return $this->render($this->views.'edit', compact('record'));
    }

    public function update(LocationRequest $request, Location $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(Location $record)
    {
        return $record->handleDestroy();
    }

   
}




// public function import()
// {
//     if (request()->get('download') == 'template') {
//         return $this->template();
//     }
//     return $this->render($this->views.'.import');
// }

// public function template()
// {
//     $fileName = date('Y-m-d').' Template Import Data '. $this->prepared('title') .'.xlsx';
//     $view = $this->views.'.template';
//     $data = [];
//     return \Excel::download(new GenerateExport($view, $data), $fileName);
// }

// public function importSave(Request $request)
// {
//     $request->validate([
//         'uploads.uploaded' => 'required',
//         'uploads.temp_files_ids.*' => 'required',
//     ],[],[
//         'uploads.uploaded' => 'Lampiran',
//         'uploads.temp_files_ids.*' => 'Lampiran',
//     ]);

//     $record = new Location;
//     return $record->handleImport($request, 'bod');
// }