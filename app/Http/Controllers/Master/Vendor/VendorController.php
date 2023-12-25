<?php

namespace App\Http\Controllers\Master\Vendor;

use App\Exports\GenerateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Vendor\VendorRequest;
use App\Models\Master\Vendor\Vendor;
use App\Models\Master\Vendor\TypeVendor;
use App\Models\Master\Geografis\City;
use App\Models\Master\Geografis\Province;
use App\Models\Master\Geografis\District;
use App\Support\Base;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    protected $module = 'master.vendor';
    protected $routes = 'master.vendor';
    protected $views  = 'master.vendor.vendor_barang';
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
                'title' => 'Vendor',
                'breadcrumb' => [
                    'Master' => route($this->routes . '.index'),
                    'Vendor' => route($this->routes . '.index'),
                    'Vendor' => route($this->routes . '.index'),
                ]
            ]
        );
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num'),
                    $this->makeColumn('name:name|label:Vendor|className:text-left'),
                    $this->makeColumn('name:jenis_usaha|label:Jenis Usaha|className:text-left'),
                    $this->makeColumn('name:address|label:Alamat|className:text-left'),
                    $this->makeColumn('name:email|label:Email|className:text-left'),
                    $this->makeColumn('name:telp|label:Nomor Telpon Instansi|className:text-left'),
                    // $this->makeColumn('name:province|label:Provinsi|className:text-left'),
                    // $this->makeColumn('name:city|label:Kota|className:text-left'),
                    // $this->makeColumn('name:telp|label:Telepon|className:text-left'),
                    // $this->makeColumn('name:email|label:Email|className:text-left'),
                    $this->makeColumn('name:contact_person|label:Contact Person|className:text-center'),
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
        $records = Vendor::grid()->filters()->dtGet();

        return DataTables::of($records)
            ->addColumn(
                'num',
                function ($record) {
                    return request()->start;
                }
            )
            ->addColumn(
                'jenis_usaha',
                function ($record) {
                    return implode('<br>, ', $record->jenisUsaha->pluck('name')->toArray());
                }
            )
            ->addColumn(
                'name',
                function ($record) {
                    return $record->name;
                }
            )
            ->addColumn(
                'contact_person',
                function ($record) {
                    return  $record->contact_person;
                }
            )
            ->addColumn(
                'telp',
                function ($record) {
                    return $record->telp;
                }
            )
            ->addColumn(
                'address',
                function ($record) {
                    return  $record->address;
                }
            )
            ->addColumn(
                'email',
                function ($record) {
                    return  $record->email;
                }
            )
            ->addColumn(
                'updated_by',
                function ($record) {
                    return $record->createdByRaw();
                }
            )
            ->addColumn(
                'action',
                function ($record) use ($user) {
                    $actions = [];

                    if ($record->checkAction('show', $this->perms)) {
                        $actions[] = 'type:show|id:' . $record->id;
                    }
                    if ($record->checkAction('edit', $this->perms)) {
                        $actions[] = 'type:edit|id:' . $record->id;
                    }
                    if ($record->checkAction('delete', $this->perms)) {
                        $actions[] = [
                            'type' => 'delete',
                            'id' => $record->id,
                            'attrs' => 'data-confirm-text="' . __('Hapus Parameter Vendor') . ' ' . $record->name . '?"',
                        ];
                    }
                    return $this->makeButtonDropdown($actions);
                }
            )
        
            ->rawColumns(['contact_person','email', 'telp', 'address', 'name','jenis_usaha','action', 'updated_by'])
            ->make(true);
    }

    public function create()
    {
        $page_action = "create";
        $type ="create";
        $record= new Vendor;
        return $this->render($this->views . '.create', compact('record','type'));
        //return $this->render($this->views . '.create', compact("page_action"));
    }


    public function store(VendorRequest $request)
    {
        $record = new Vendor;
        return $record->handleStoreOrUpdate($request);
    }

    public function show(Vendor $record)
    {
        $page_action = "show";
        return $this->render($this->views . '.show', compact('record', 'page_action'));
    }

    public function edit(Vendor $record)
    {
        return $this->render($this->views . '.edit', compact('record'));
    }

    public function update(VendorRequest $request, Vendor $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(Vendor $record)
    {
        return $record->handleDestroy();
    }

    public function import()
    {
        if (request()->get('download') === 'template') {
            return $this->template();
        }
        return $this->render($this->views . '.import');
    }

    public function template()
    {
        $fileName = date('Y-m-d') . ' Template Import Data ' . $this->prepared('title') . '.xlsx';
        $view = $this->views . '.template';
        $data = [];
        return \Excel::download(new GenerateExport($view, $data), $fileName);
    }

    public function importSave(Request $request)
    {
        $request->validate([
            'uploads.uploaded' => 'required',
            'uploads.temp_files_ids.*' => 'required',
        ], [], [
            'uploads.uploaded' => 'File',
            'uploads.temp_files_ids.*' => 'File',
        ]);

        $record = new Vendor;
        return $record->handleImport($request);
    }
}
