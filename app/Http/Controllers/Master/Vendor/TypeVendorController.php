<?php

namespace App\Http\Controllers\Master\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Vendor\TypeVendorRequest;
use App\Models\Globals\Menu;
use App\Models\Master\Vendor\TypeVendor;
use App\Support\Base;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TypeVendorController extends Controller
{
    protected $module = 'master.type-vendor';
    protected $routes = 'master.type-vendor';
    protected $views  = 'master.vendor.type-vendor';
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
                'title' => 'Jenis Usaha Vendor',
                'breadcrumb' => [
                    'Data Master' => route($this->routes . '.index'),
                    'Vendor' => route($this->routes . '.index'),
                    'Jenis Usaha Vendor' => route($this->routes . '.index'),
                ]
            ]
        );
    }

    public function grid()
    {
        $user = auth()->user();
        $records = TypeVendor::grid()->filters()->dtGet();

        return DataTables::of($records)
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
            ->addColumn('action', function ($record) use ($user) {
                $actions = [];
                if ($record->checkAction('show', $this->perms)) {
                    $actions[] = 'type:show|id:' . $record->id;
                }
                if (auth()->user()->hasRole('PPK') || auth()->user()->hasRole('Sarpras')) {
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
                // if ($record->checkAction('delete', $this->perms)) {
                //     $actions[] = [
                //         'type' => 'delete',
                //         'id' => $record->id,
                //         'attrs' => 'data-confirm-text="' . __('Hapus Parameter Mata Anggaran') . ' ' . $record->mata_anggaran . '?"',
                //     ];
                // }
                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns(
                [
                    'name',
                    'descrption',
                    'updated_by',
                    'action',
                ]
            )
            ->make(true);
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num'),
                    $this->makeColumn('name:name|label:Nama Jenis Usaha|className:text-center'),
                    $this->makeColumn('name:description|label:Deskripsi|className:text-left'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);

        return $this->render($this->views . '.index');
    }

    public function create()
    {
        $page_action = "create";
        return $this->render($this->views . '.create', compact("page_action"));
    }

    public function show(TypeVendor $record)
    {
        $baseContentReplace = false;
        $page_action = "show";
        return $this->render($this->views . '.edit', compact("page_action", "record", "baseContentReplace"));
    }
    public function store(TypeVendorRequest $request)
    {
        $record = new TypeVendor;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(TypeVendor $record)
    {
        $page_action = "edit";
        return $this->render($this->views . '.edit', compact("page_action", "record"));
    }

    public function update(TypeVendor $record, TypeVendorRequest $request)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(TypeVendor $record)
    {
        return $record->handleDestroy();
    }

    public function getDetailTypeVendor(Request $request)
    {
        $id = $request->id;
        $type_vendor = TypeVendor::where('id', $id)->first()->name;
        return response()->json([
            'Jenis Usaha' => $type_vendor,
        ]);
    }
}
