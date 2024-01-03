<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PerencanaanRequest;
use App\Http\Requests\Pengajuan\PerencanaanUpdateRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailUpdateHargaRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailRequest;
use App\Http\Requests\Pengajuan\PerencanaanDisposisiRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailCreateRequest;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PerencanaanAsetController extends Controller
{
    protected $module = 'perencanaan-aset';
    protected $routes = 'pengajuan.perencanaan-aset';
    protected $views = 'pengajuan.perencanaan-aset';
    protected $perms = 'perencanaan-aset';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Pengajuan Perencanaan',
            'breadcrumb' => [
                'Home' => route('home'),
                // 'Pengajuan' => '#',
                'Pengajuan Perencanaan' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:no_surat|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:struct|label:Unit Kerja|className:text-center|width:250px'),
                    $this->makeColumn('name:perihal|label:Perihal|className:text-center|width:300px'),
                    // $this->makeColumn('name:is_repair|label:Jenis Usulan|className:text-center|width:300px'),
                    $this->makeColumn('name:procurement_year|label:Tahun Pengadaan|className:text-center|width:300px'),
                 //   $this->makeColumn('name:version|label:Revisi|className:text-center'),
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
        $records = Perencanaan::with('struct')
            ->grid()->filters()
            ->dtGet();

        return DataTables::of($records)
            ->addColumn('#', function ($record) {
                return request()->start;
            })
            ->addColumn(
                'struct',
                function ($record) {
                    return $record->struct ? $record->struct->name : '-';
                }
            )

            ->addColumn('no_surat', function ($record) {
                if ($record->code) {
                    return $record->code . "<br>" . $record->date->translatedFormat('d/m/Y');
                }
                return '';
            })

            ->addColumn('perihal', function ($record) {
                return $record->regarding;
            })

            ->addColumn('year_application', function ($record) {
                return $record->procurement_year ?  $record->procurement_year : '-';
            })

            // ->addColumn('is_repair', function ($record) {
            //     if($record->is_repair == 'no'){
            //         return 'Usulan Pengadaan Aset';
            //     }elseif($record->is_repair == 'yes'){
            //         return 'Usulan Perbaikan Aset';
            //     }
            // })

            ->addColumn(
                'version',
                function ($record) use ($user) {
                    return $record->version;
                }
            )

            ->addColumn('status', function ($record) use ($user) {
                return $record->labelStatus($record->status ?? 'new');
            })

            ->addColumn('updated_by', function ($record) {
                if ($record->status === 'new') {
                    return "";
                } else {
                    return $record->createdByRaw();
                }
            })

            // ->addColumn('created_by', function ($record)  {
            //     $detail->
            // })

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

                if ($record->checkAction('edit', $this->perms)) {
                    $actions[] = [
                        'type' => 'edit',
                        'id' => $record->id,
                        'url' => route($this->routes . '.edit', $record->id),
                    ];
                }

                if ($record->checkAction('edit', $this->perms)) {
                    $actions[] = [
                        'type' => 'edit',
                        'page' => true,
                        'label' => 'Detail',
                        'icon' => 'fa fa-plus text-info',
                        'id' => $record->id,
                        'url' => route($this->routes . '.detail', $record->id),
                    ];
                }

                if ($record->checkAction('delete', $this->perms)) {
                    $actions[] = [
                        'type' => 'delete',
                        'id' => $record->id,
                        'method'=>'post',
                        'url' => route($this->routes . '.destroy', $record->id),
                    ];
                }

                if ($record->checkAction('approval', $this->perms)) {
                    if($user->position->location->level == 'department' || $user->position->location->id = 13 || $user->position->location->level == 'bod' ){
                        $actions[] = [
                            'type' => 'approval',
                            'label' => 'Approval',
                            'page' => true,
                            'id' => $record->id,
                            'url' => route($this->routes . '.approval', $record->id)
                        ];
                    }
                }


                if ($record->checkAction('tracking', $this->perms)) {
                    $actions[] = 'type:tracking';
                }

                if ($record->checkAction('history', $this->perms)) {
                    $actions[] = 'type:history';
                }

                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns(['struct',
            'no_surat',
            'perihal',
            'procurement_year',
            'is_repair',
            'version',
            'status','updated_by','action'])
            ->make(true);
    }

    public function create()
    {
        $type= 'create';
        $position = auth()->user()->position_id;
        $departemen = Position::with('location')->where('id',$position)->first();
        return $this->render($this->views . '.create', compact('departemen','type'));
    }

    public function store(PerencanaanRequest $request)
    {
        $record = new Perencanaan;
        return $record->handleStore($request);
    }

    public function edit(Perencanaan $record)
    {
        
        $type ='edit';
        $position = auth()->user()->position_id;
        $departemen = Position::with('location')->where('id',$position)->first();

        return $this->render($this->views . '.edit', compact('record','type','departemen'));
    }

    public function detailGrid(Perencanaan $record)
    {        
        $user = auth()->user();
        $records = PerencanaanDetail::with(['perencanaan'])
            ->whereHas(
                'perencanaan',
                function ($q) use ($record) {
                    $q->where('perencanaan_id', $record->id);
                }
            )->orderByRaw("CASE WHEN updated_at > created_at THEN updated_at ELSE created_at END DESC")->filters()
            ->dtGet();

        $approval1 = $record->whereHas('approvals', function ($q) use ($record) {
            $q->where('target_id',$record->id)->where('status','!=','approved')->where('role_id',5);
        })->count();

        $approval2 = $record->whereHas('approvals', function ($q) use ($record) {
            $q->where('target_id',$record->id)->where('status','!=','approved')->where('role_id',3);
        })->count();
    
        return DataTables::of($records)
            ->addColumn(
                'num',
                function ($detail) {
                    return request()->start;
                }
            )
            ->addColumn(
                'ref_aset_id',
                function ($detail) {
                    return $detail->asetd->name ? $detail->asetd->name : '';
                }
            )
            ->addColumn(
                'desc_spesification',
                function ($detail) {
                    return $detail->desc_spesification ? $detail->desc_spesification : '';
                }
            )
            ->addColumn(
                'existing_amount',
                function ($detail) {
                    return $detail->existing_amount;
                }
            )
            ->addColumn(
                'requirement_standard',
                function ($detail) {
                    return $detail->requirement_standard;
                }
            )
            ->addColumn(
                'qty_req',
                function ($detail) {
                    return $detail->qty_req;
                }
            )
            ->addColumn(
                'HPS_unit_cost',
                function ($detail) {
                    return number_format($detail->HPS_unit_cost, 0, ',', ',');
                }
            )
            ->addColumn(
                'HPS_total_cost',
                function ($detail) {
                    return number_format($detail->HPS_total_cost, 0, ',', ',');
                }
            )
          
            ->addColumn(
                'qty_agree',
                function ($detail) {
                    return $detail->labelStatus(number_format($detail->qty_agree, 0, ',', ',') ?? '0');
                }
            )
            ->addColumn(
                'HPS_total_agree',
                function ($detail) {
                    return number_format($detail->HPS_total_agree, 0, ',', ',');
                }
            )
            ->addColumn(
                'sumber_dana',
                function ($detail) {
                    return $detail->danad ? $detail->danad->name : '-';
                }
            )
            ->addColumn(
                'updated_by',
                function ($detail) use ($record) {
                    return $detail->createdByRaw();
                }
            )
            ->addColumn(
                'action_show',
                function ($detail) use ($user, $record, $approval1, $approval2) {
                    $actions = [];
                    $actions[] = [
                        'type' => 'show',
                        'url' => route($this->routes . '.detailShow', $detail->id),
                    ];                    
                    return $this->makeButtonDropdown($actions, $detail->id);
                }
            )
            ->addColumn(
                'action_approval',
                function ($detail) use ($user, $record, $approval1, $approval2) {
                    $actions = [];
                    if($user->hasRole('Sub Bagian Program Perencanaan') && ($approval2 > 0) && ($approval1 == 0) ){
                        if($detail->status == 'draf'){
                            $actions[] = [
                                'type'=>'approval',
                                'url' => route($this->routes . '.detailApprove', $detail->id),
                            ];
                            $actions[] = [
                                'type' => 'edit',
                                'url' => route($this->routes . '.detailEditHarga', $detail->id),
                            ];
                        }
                    }elseif($user->position->location->level == 'department' && ($approval1 > 0) ){
                        if($detail->status == 'draf'){
                            $actions[] = [
                                'type' => 'edit',
                                'url' => route($this->routes . '.detailEditHarga', $detail->id),
                            ];
                        }
                    }
                    if($detail->status != 'draf'){
                        $actions = [];
                        $actions[] = [
                            'type' => 'show',
                            'url' => route($this->routes . '.detailShow', $detail->id),
                        ];              
                    }                  
                    return $this->makeButtonDropdown($actions, $detail->id);
                }
            )
            ->addColumn(
                'action',
                function ($detail) use ($user, $record) {
                    $actions = [];

                    $actions[] = [
                        'type' => 'show',
                        'url' => route($this->routes . '.detailShow', $detail->id),
                    ];

                    $actions[] = [
                        'type' => 'edit',
                        'url' => route($this->routes . '.detailEdit', $detail->id),
                    ];

                    // $actions[] = [
                    //     'type'=>'history',
                    //     'url' => route($this->routes . '.historyDetail', $record)
                    // ];

                    if ($detail->perencanaan->checkAction('edit', $this->perms)) {
                        $actions[] = [
                            'type' => 'delete',
                            'url' => route($this->routes . '.detailDestroy', $detail->id),
                        ];
                    }
                    return $this->makeButtonDropdown($actions, $detail->id);
                }
            )
            ->rawColumns(['qty_agree','action', 'action_show','action_approval' ,'updated_by','created_by'])
            ->make(true);
    }


    public function detail(Perencanaan $record)
    {
        // dd($record->id);
        $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:200px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi|className:text-left|width:300px'),
                    $this->makeColumn('name:requirement_standard|label:Standar Kebutuhan|className:text-center|width:250px'),
                    $this->makeColumn('name:existing_amount|label:Tersedia|className:text-center|width:250px'),
                    $this->makeColumn('name:qty_req|label:Pengajuan|className:text-center|width:250px'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga (Rupiah)|className:text-center|width:250px'),
                    $this->makeColumn('name:HPS_total_cost|label:Total Harga (Rupiah) |className:text-center|width:250px'),
                    $this->makeColumn('name:qty_agree|label:Disetujui|className:text-center|width:250px'),
                    $this->makeColumn('name:HPS_total_agree|label:Harga Total Pembelian (Rupiah)|className:text-center|width:250px'),
                    $this->makeColumn('name:sumber_dana|label:Sumber Dana|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|width:300px'),
                    // $this->makeColumn('name:created_by|width:100px'),
                    $this->makeColumn('name:action|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.detail', compact('record'));
    }

    public function detailCreate(Perencanaan $record)
    {
        // dd(json_decode($record));
        $baseContentReplace = 'base-modal--render';
        $type ='create';
        
        return $this->render($this->views . '.detail.create', compact('record', 'baseContentReplace','type'));
    }

    public function detailStore(PerencanaanDetailCreateRequest $request, Perencanaan $record)
    {
        $detail = new PerencanaanDetail;
        $record = Perencanaan::find($request->perencanaan_id);

        return $record->handleDetailStoreOrUpdate($request, $detail);
    }

    public function detailEdit(PerencanaanDetail $detail)
    {
        ///$record = $detail->perencanaan;
        $type='edit';
        $baseContentReplace = 'base-modal--render';
        $record = $detail->perencanaan;
        
        return $this->render($this->views . '.detail.edit', compact('record','detail','baseContentReplace','type'));
    }

    public function detailEditHarga(PerencanaanDetail $detail)
    {
        ///$record = $detail->perencanaan;
        $type='edit';
        $baseContentReplace = 'base-modal--render';
        $record = $detail->perencanaan;
        
        return $this->render($this->views . '.detail.editHarga', compact('record','detail','baseContentReplace','type'));
    }

    public function detailApprove(PerencanaanDetail $detail)
    {
        ///$record = $detail->perencanaan;
        $baseContentReplace = 'base-modal--render';
        $record = $detail->perencanaan;
        //dd($detail);
        $type='edit';
        return $this->render($this->views . '.detail.approve', compact('record','detail','baseContentReplace','type'));
    }
    

    public function detailUpHarga(PerencanaanDetailUpdateHargaRequest $request,  PerencanaanDetail $detail)
    {
        $record = $detail->perencanaan;
        return $record->handleDetailStoreOrUpdate($request,$detail);
    }

    public function detailUpdate(PerencanaanDetailCreateRequest $request,  PerencanaanDetail $detail)
    {
        $record = $detail->perencanaan;
        return $record->handleDetailStoreOrUpdate($request,$detail);
    }

    public function detailUpApprove(PerencanaanDetailRequest $request,  PerencanaanDetail $detail)
    {
        // dd($request->all());
        $record = $detail->perencanaan;
        return $record->handleDetailStoreOrUpdate($request,$detail);
    }

    public function detailShow(PerencanaanDetail $detail)
    {
        // $record = $detail->pj;
        // dd($detail);
        $type ='show';
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.detail.show', compact('type','detail', 'baseContentReplace'));
    }

    public function detailDestroy(PerencanaanDetail $detail)
    {
        //dd($detail); //detail data;
        $record = $detail->perencanaan;

        //dd($record); //perencanaan data
        return $record->handleDetailDestroy($detail);
    }

    public function update(PerencanaanDisposisiRequest $request, Perencanaan $record)
    {
        return $record->handleStoreOrUpdate($request);
    }
    
    public function updateSummary(PerencanaanUpdateRequest $request, Perencanaan $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(Perencanaan $record)
    {
        return $record->handleDestroy();
    }

    public function submit(Perencanaan $record)
    {
        $flowApproval = $record->getFlowApproval($this->module);
        return $this->render('globals.submit', compact('record', 'flowApproval'));
    }

    public function submitSave(Perencanaan $record, Request $request)
    {
        return $record->handleSubmitSave($request);
    }

    public function approval(Perencanaan $record)
    {
        $this->pushBreadcrumb(['Approval' => route($this->routes . '.approval', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:500px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-center'),
                    $this->makeColumn('name:requirement_standard|label:Standar Kebutuhan|className:text-center'),
                    $this->makeColumn('name:existing_amount|label:Tersedia|className:text-center'),
                    $this->makeColumn('name:qty_req|label:Jumlah Pengajuan|className:text-center'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga (Rupiah)|className:text-left|width:500px'),
                    $this->makeColumn('name:HPS_total_cost|label:Harga Total (Rupiah)|className:text-center'),
                    $this->makeColumn('name:qty_agree|label:Disetujui|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_total_agree|label:Harga Total Disetujui (Rupiah)|className:text-center'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action_approval|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);

        return $this->render($this->views . '.show', compact('record'));
    }

    public function verify(Perencanaan $record, Request $request)
    {
        return $record->handleVerify($request);
    }

    public function approve(Perencanaan $record, Request $request)
    {
        
        $data = $record->handleDetailApproval($record);
        if($data['status'] == 'gagal'){
            $request = [
                'message' => $data['message'],
                'module' => null,
            ];

            return $this->rollback(
                [
                    'message' => 'Silahkan lakukan approval pada  !.'.$data['message'].'.'
                ]
            );

        }else{
            return $record->handleApprove($request);
        }
    }

    public function reject(Perencanaan $record, Request $request)
    {
        // dd($request->all());
        // dd($request->all());
        $request->validate(
            [
                'note'  => 'required',
            ]
        );
        return $record->handleReject($request);
    }

    public function history(Perencanaan $record)
    {
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }
    
    public function historyDetail($record)
    {
       // dd($record);
        $record = Perencanaan::where('id',16)->first();
        // dd($newRecord);
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    public function show(Perencanaan $record)
    {
        $this->pushBreadcrumb(['Lihat' => route($this->routes . '.show', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:500px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-center'),
                    $this->makeColumn('name:requirement_standard|label:Standar Kebutuhan|className:text-center'),
                    $this->makeColumn('name:existing_amount|label:Jumlah Tersedia|className:text-center'),
                    $this->makeColumn('name:qty_req|label:Jumlah Pengajuan|className:text-center'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga|className:text-left|width:500px'),
                    $this->makeColumn('name:HPS_total_cost|label:Harga Total Usulan|className:text-center'),
                    $this->makeColumn('name:qty_agree|label:Disetujui|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_total_agree|label:Harga Total Disetujui|className:text-center'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action_show|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
                // ambil data dari detail grid
            ],
        ]);
        return $this->render($this->views . '.show', compact('record'));
        //ambil data dari show => detail
    }

    public function revisi(Perencanaan $record, Request $request)
    {
        return $record->handleRevisi($request);
    }

    public function tracking(Perencanaan $record)
    {
        $module = $this->module;
        if ($record->status === 'waiting.approval.revisi') {
            $module = $module . '_upgrade';
        }
        return $this->render('globals.tracking', compact('record', 'module'));
    }

    public function print(Perencanaan $record, $title = '')
    {

    }

}


// if ($record->checkAction('verification', $this->perms)) {
//     $actions[] = [
//         'type' => 'approval',
//         'page' => true,
//         'label' => 'Verifikasi',
//         'id' => $record->id,
//         'url' => route($this->routes . '.approval', $record->id),
//     ];
// }

