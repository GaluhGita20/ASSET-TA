<?php

namespace App\Http\Controllers\Pemeliharaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pemeliharaan\PemeliharaanRequest;
use App\Http\Requests\Pemeliharaan\PemeliharaanDetailRequest;
use App\Models\Pemeliharaan\Pemeliharaan;
use App\Models\Pemeliharaan\PemeliharaanDetail;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PemeliharaanAsetController extends Controller
{
    protected $module = 'pemeliharaan-aset';
    protected $routes = 'pemeliharaan.pemeliharaan-aset';
    protected $views = 'pemeliharaan.pemeliharaan-aset';
    protected $perms = 'pemeliharaan-aset';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Pemeliharaan Aset',
            'breadcrumb' => [
                'Home' => route('home'),
                // 'Pengajuan' => '#',
                'Pemeliharaan Aset' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:code|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:departemen|label:Unit Departemen|className:text-center|width:250px'),
                    $this->makeColumn('name:tanggal_pemeliharaan|label:Tanggal Pemeliharaan|className:text-center|width:300px'),
                    $this->makeColumn('name:status'),
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
        $records = Pemeliharaan::grid()->filters()->dtGet();

        return DataTables::of($records)
            ->addColumn('#', function ($record) {
                return request()->start;
            })
            ->addColumn(
                'code',
                function ($record) {
                    return $record->code ? $record->code : '-';
                }
            )

            ->addColumn('departemen', function ($record) {
                return $record->departemen_id ? $record->deps->name : '-';
            })

            ->addColumn('tanggal_pemeliharaan', function ($record) {
                return Carbon::parse($record->maintenance_date)->format('Y-m-d');
            })

            ->addColumn('status', function ($record) {
                return $record->status ?  $record->labelStatus($record->status) : '-';
            })

            ->addColumn('updated_by', function ($record) {
                if ($record->status === 'new') {
                    return "";
                } else {
                    return $record->createdByRaw();
                }
            })

            ->addColumn('action', function ($record) use ($user) {
                $actions = [];

                if ($record->checkAction('show', $this->perms)) {
                    $actions[] = [
                        'type' => 'show',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes . '.show', $record->id),
                    ];
                }

                if ($record->checkAction('history', $this->perms)) {
                    $actions[] = 'type:history';
                }

                if(auth()->user()->position->level == 'kepala' && $user->position->location->id == 8){
                    if ($record->checkAction('approval', $this->perms)) {
                            $actions[] = [
                                'type' => 'approval',
                                'label' => 'Approval',
                                'page' => true,
                                'id' => $record->id,
                                'url' => route($this->routes . '.approval', $record->id)
                            ];
                    }
                }

                if(auth()->user()->hasRole('Sarpras') && auth()->user()->checkPerms('pemeliharaan-aset.edit')){
                    if($record->status == 'draft' ||  $record->status == 'rejected'){
                        $actions[] = [
                            'type' => 'edit',
                            'id' => $record->id,
                            'url' => route($this->routes . '.edit', $record->id),
                        ];

                        $actions[] = [
                            'type' => 'edit',
                            'page' => true,
                            'label' => 'Detail',
                            'icon' => 'fa fa-plus text-info',
                            'id' => $record->id,
                            'url' => route($this->routes . '.detail', $record->id),
                        ];
                    }

                }

                if ($record->checkAction('tracking', $this->perms)) {
                    $actions[] = 'type:tracking';
                }

                if(auth()->user()->hasRole('Sarpras') && auth()->user()->checkPerms('pemeliharaan-aset.delete')){
                    if($record->status == 'draft' ||  $record->status == 'rejected'){
                        $actions[] = [
                            'type' => 'delete',
                            'id' => $record->id,
                            'method'=>'post',
                            'url' => route($this->routes . '.destroy', $record->id),
                        ];
                    }
                
                }
                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns([
            'code',
            'departemen',
            'dates',
            'status','updated_by','action'])
            ->make(true);
    }

    public function create()
    {
        $type= 'create';
        return $this->render($this->views . '.create', compact('type'));
    }

    public function store(PemeliharaanRequest $request)
    {
        $record = new Pemeliharaan;
        return $record->handleStore($request);
    }

    public function updateSummary(PemeliharaanRequest $request, Pemeliharaan $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function detailGrid(Pemeliharaan $record)
    {        
        $user = auth()->user();
        $records = PemeliharaanDetail::with(['pemeliharaan','asetd'])
            ->whereHas(
                'pemeliharaan',
                function ($q) use ($record) {
                    $q->where('pemeliharaan_id', $record->id);
                }
            )->orderByRaw("CASE WHEN updated_at > created_at THEN updated_at ELSE created_at END DESC")->filters()
            ->dtGet();
    
        return DataTables::of($records)
            ->addColumn(
                'num',
                function ($detail) {
                    return request()->start;
                }
            )
            ->addColumn(
                'kib_id',
                function ($detail) {
                    return $detail->asetd ? $detail->asetd->usulans->asetd->name : '-';
                }
            )
            // ->addColumn(
            //     'kib_id',
            //     function ($detail) {
            //         return $detail->asetd ? $detail->asetd->usulans->asetd->name : '-';
            //     }
            // )
            ->addColumn(
                'type',
                function ($detail) {
                    return $detail->asetd->type ? $detail->asetd->type : '-';
                }
            )
            ->addColumn(
                'merek',
                function ($detail) {
                    return $detail->asetd->merek_type_item ? $detail->asetd->merek_type_item  : '-';
                }
            )
            ->addColumn(
                'lokasi',
                function ($detail) {
                    return $detail->asetd->locations ? $detail->asetd->locations->name : '-';
                }
            )
            ->addColumn(
                'status',
                function ($detail) {
                    if($detail->maintenance_action == null){
                        return $detail->labelStatus('not completed');
                    }else{
                        return $detail->labelStatus('completed');   
                    }
                }
            )
            ->addColumn(
                'petugas',
                function ($detail) {
                    return $detail->petugas ? $detail->petugas->name : '-' ;
                }
            )
            ->addColumn(
                'updated_by',
                function ($detail) use ($record) {
                    return $detail->createdByRaw();
                }
            )
            ->addColumn(
                'action',
                function ($detail) use ($user, $record) {
                    if($detail->pemeliharaan->status == 'draft' || $detail->pemeliharaan->status == 'rejected'){
                        $actions[] = [
                            'type' => 'edit',
                            'label' => 'Perbarui Pemeliharaan',
                            'icon' => 'fa fa-wrench text-success',
                            // 'id' => $record->id,
                            'url' => route($this->routes . '.detailEdit', $detail->id),
                        ];
                        $actions[] = [
                            'type' => 'delete',
                            'url' => route($this->routes . '.detailDestroy', $detail->id),
                        ];
                    }
                    $actions = [];
                    $actions[] = [
                        'type' => 'show',
                        'url' => route($this->routes . '.detailShow', $detail->id),
                    ];
                    return $this->makeButtonDropdown($actions, $detail->id);
                }
            )
            ->rawColumns(['merek','type','status','action','updated_by','created_by'])
            ->make(true);
    }


    public function detail(Pemeliharaan $record)
    {
        // dd($record->id);
        $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:kib_id|label:Nama Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:type|label:Tipe Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:merek|label:Merek Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:lokasi|label:Lokasi Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:status|label:Status Pemeliharaan|className:text-center|width:250px'),
                    $this->makeColumn('name:petugas|label:Penaggung Jawab Pemeliharaan|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|width:300px'),
                    $this->makeColumn('name:action|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.detail', compact('record'));
    }

    public function detailCreate(Pemeliharaan $record)
    {
        $baseContentReplace = 'base-modal--render';
        $type ='create';
        
        return $this->render($this->views . '.detail.create', compact('record', 'baseContentReplace','type'));
    }

    public function detailStore(PemeliharaanDetailRequest $request, Pemeliharaan $record)
    {
        $detail = new PemeliharaanDetail;
        $record = Pemeliharaan::find($request->pemeliharaan_id);
        return $record->handleDetailStoreOrUpdate($request, $detail);
    }

    public function detailEdit(PemeliharaanDetail $detail)
    {
        $type='edit';
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.detail.edit', compact('detail','baseContentReplace','type'));
    }

    public function detailUpdate(PemeliharaanDetailRequest $request,  PemeliharaanDetail $detail)
    {
        $record = $detail->pemeliharaan;
        return $record->handleDetailStoreOrUpdate($request,$detail);
    }

    public function detailShow(PemeliharaanDetail $detail)
    {
        $type ='show';
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.detail.show', compact('type','detail', 'baseContentReplace'));
    }

    public function detailDestroy(PemeliharaanDetail $detail)
    {
        $record = $detail->pemeliharaan;
        return $record->handleDetailDestroy($detail);
    }

    public function destroy(Pemeliharaan $record)
    {
        return $record->handleDestroy();
    }

    public function history(Pemeliharaan $record)
    {
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    public function show(Pemeliharaan $record)
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:kib_id|label:Nama Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:type|label:Tipe Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:merek|label:Merek Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:lokasi|label:Lokasi Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:status|label:Status Pemeliharaan|className:text-center|width:250px'),
                    $this->makeColumn('name:petugas|label:Penanggung Jawab Pemeliharaan|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|width:300px'),
                    $this->makeColumn('name:action|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.show', compact('record'));
        // return $this->render($this->views . '.detail', compact('record'));
    }

    public function tracking(Pemeliharaan $record)
    {
        $module = $this->module;
        if ($record->status === 'waiting.approval.revisi') {
            $module = $module . '_upgrade';
        }
        return $this->render('globals.tracking', compact('record', 'module'));
    }

    public function edit(Pemeliharaan $record)
    {
        $type ='edit';
        return $this->render($this->views . '.edit',compact('record'));
    }

    public function update(Request $request, Pemeliharaan $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function print(Pemeliharaan $record, $title = '')
    {

    }

    public function approval(Pemeliharaan $record)
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:kib_id|label:Nama Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:type|label:Tipe Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:merek|label:Merek Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:lokasi|label:Lokasi Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:status|label:Status Pemeliharaan|className:text-center|width:250px'),
                    $this->makeColumn('name:petugas|label:Penanggung Jawab Pemeliharaan|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|width:300px'),
                    $this->makeColumn('name:action|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.show', compact('record'));
    }


    public function approve(Pemeliharaan $record, Request $request)
    {
        return $record->handleApprove($request);  
    }

    public function reject(Pemeliharaan $record, Request $request)
    {
        $request->validate(
            [
                'note'  => 'required',
            ]
        );
        return $record->handleReject($request);
    }

}


