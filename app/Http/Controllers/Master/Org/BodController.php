<?php

namespace App\Http\Controllers\Master\Org;

use App\Exports\GenerateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Org\BodRequest;
use App\Models\Master\Org\OrgStruct;
use Illuminate\Http\Request;

class BodController extends Controller
{
    protected $module = 'master_org_bod';
    protected $routes = 'master.org.bod';
    protected $views = 'master.org.bod';
    protected $perms = 'master';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms.'.view',
            'title' => 'Direktorat',
            'breadcrumb' => [
                'Data Master' => rut($this->routes . '.index'),
                'Stuktur Organisasi' => rut($this->routes . '.index'),
                'Direktorat' => rut($this->routes.'.index'),
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
                    $this->makeColumn('name:parent|label:Instansi|className:text-center'),
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
        $records = OrgStruct::bod()->filters()->dtGet();

        return \DataTables::of($records)
            ->addColumn('num', function ($record) {
                return request()->start;
            })
            ->addColumn('code', function ($record) {
                return  $record->code ?? null;
            })
            ->addColumn('name', function ($record) {
                return $record->name ?? null;
            })
            ->addColumn('parent', function ($record) {
                return $record->parent->name ?? null;
            })
            ->addColumn('updated_by', function ($record) {
                return $record->createdByRaw();
            })
            ->addColumn('action', function ($record) use ($user) {
                $actions = [
                    'type:show|id:'.$record->id,
                    // 'type:edit|id:'.$record->id,
                ];
                
                if ($record->canDeleted() || auth()->user()->hasRole('Administrator')) {
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
                return $this->makeButtonDropdown($actions);
            })
            ->rawColumns(['action','updated_by', 'parent', 'name', 'code'])
            ->make(true);
    }

    public function create()
    {
        return $this->render($this->views.'.create');
    }

    public function store(BodRequest $request)
    {
        $record = new OrgStruct;
        return $record->handleStoreOrUpdate($request, 'bod');
    }

    public function show(OrgStruct $record)
    {
        return $this->render($this->views.'.show', compact('record'));
    }

    public function edit(OrgStruct $record)
    {
        return $this->render($this->views.'.edit', compact('record'));
    }

    public function update(BodRequest $request, OrgStruct $record)
    {
        return $record->handleStoreOrUpdate($request, 'bod');
    }

    public function destroy(OrgStruct $record)
    {
        return $record->handleDestroy();
    }

    public function import()
    {
        if (request()->get('download') == 'template') {
            return $this->template();
        }
        return $this->render($this->views.'.import');
    }

    public function template()
    {
        $fileName = date('Y-m-d').' Template Import Data '. $this->prepared('title') .'.xlsx';
        $view = $this->views.'.template';
        $data = [];
        return \Excel::download(new GenerateExport($view, $data), $fileName);
    }

    public function importSave(Request $request)
    {
        $request->validate([
            'uploads.uploaded' => 'required',
            'uploads.temp_files_ids.*' => 'required',
        ],[],[
            'uploads.uploaded' => 'Lampiran',
            'uploads.temp_files_ids.*' => 'Lampiran',
        ]);

        $record = new OrgStruct;
        return $record->handleImport($request, 'bod');
    }
}
