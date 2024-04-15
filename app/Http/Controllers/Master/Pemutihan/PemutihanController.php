<?php

namespace App\Http\Controllers\Master\Pemutihan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Pemutihan\PemutihanRequest;
use App\Models\Globals\Menu;
use App\Models\Master\Pemutihan\Pemutihan;
use App\Support\Base;
use Carbon\Carbon;
use Illuminate\Http\Request;
//use Yajra\DataTables\Facades\DataTables;

class PemutihanController extends Controller
{
    //
    protected $module = 'master_pemutihan';
    protected $routes = 'master.data-pemutihan';
    protected $views  = 'master.pemutihan';
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
                'title' => 'Jenis Pemutihan',
                'breadcrumb' => [
                    'Data Master' => rut($this->routes . '.index'),
                    // 'Jenis Pemutihan' => rut($this->routes . '.index'),
                    'Pemutihan' => rut($this->routes . '.index'),
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
                    $this->makeColumn('name:name|label:Jenis Pemutihan|className:text-left'),
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
        $records = Pemutihan::grid()->filters()->dtGet();

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

    public function show(Pemutihan $record){
        // dd($record);
        return $this->render($this->views . '.show',compact('record'));
    }

    public function store(PemutihanRequest $request){
        $record = new Pemutihan;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(Pemutihan $record)
    {
        return $this->render($this->views.'.edit',compact('record'));
    }


    public function update(Pemutihan $record, PemutihanRequest $request){
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(Pemutihan $record){
        return $record->handleDestroy();
    }

    public function getDetailAset(Request $request){
        $id_akun = $request->id;
        $aset = Pemutihan::where('id', $id)->first();
        return response()->json([
            'name' => $aset->name,
           /// 'jenis_pengadaan' => $aset->jenis_pengadaan,
            'description' => $aset->description,
        ]);
    }

}
