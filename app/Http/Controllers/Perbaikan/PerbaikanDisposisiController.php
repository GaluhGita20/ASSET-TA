<?php

namespace App\Http\Controllers\Perbaikan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Perbaikan\PerbaikanDisposisiRequest;
use App\Http\Requests\Perbaikan\UsulanSperpatRequest;
use App\Models\Perbaikan\TransPerbaikanDisposisi;
use App\Models\Perbaikan\UsulanSperpat;
use App\Models\Pengajuan\Perbaikan;
use App\Models\Globals\Approval;
use App\Models\Globals\Activity;
use App\Models\inventaris\Aset;
use App\Models\Master\Org\Position;
use App\Models\Master\Org\OrgStruct;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PerbaikanDisposisiController extends Controller
{
    protected $module = 'usulan_pembelian-sperpat';
    protected $routes = 'perbaikan.usulan-sperpat';
    protected $views = 'perbaikan.usulan-sperpat';
    protected $perms = 'usulan_pembelian-sperpat';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Pengajuan Perbaikan',
            'breadcrumb' => [
                'Home' => route('home'),
                'Pengajuan Perbaikan' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function grid()
    {
        $user = auth()->user();
        $records = TransPerbaikanDisposisi::gridSperpat()->filters()->dtGet();

        return DataTables::of($records)
            ->addColumn('#', function ($record) {
                return request()->start;
            })
            ->addColumn('no_surat', function ($record) {
                    return $record->codes ? $record->codes->code : '-';
                }
            )
            ->addColumn('repair_type', function ($record) {
                if ($record->repair_type == 'sperpat') {
                // return '<span data-short="Completed" class="label label-success label-inline text-nowrap " style="">Completed</span>';
                    return '<span class="badge bg-success text-white">' . $record->repair_type . '</span>';
                } elseif($record->repair_type == 'vendor') {
                    return '<span class="badge bg-primary text-white">'.$record->repair_type.'</span>';
                }else{
                    return '<span class="badge bg-primary text-white">'.$record->repair_type.'</span>';
                }
            })

            ->addColumn('procurement_year', function ($record) {
                return $record->procurement_year;
            })

            ->addColumn('vendor', function ($record) {
                return $record->vendors->name;
            })
            
            ->addColumn('status', function ($record) use ($user) {
                return $record->labelStatus($record->sper_status ?? 'new');
            })

            ->addColumn('updated_by', function ($record) {
                if ($record->status === 'new') {
                    return "";
                } else {
                    return $record->createdByRaw();
                }
            })

            ->addColumn('action', function ($record) use ($user) {
                $actions=[];

                
                if ($record->checkAction('show', $this->perms)) {
                    $actions[] = [
                        'type' => 'show',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes.'.show', $record->id),
                    ];
                }

                if($record->sper_status === 'new' || $record->sper_status === 'draft' || $record->sper_status === 'rejected'){
                    if ($record->checkAction('edit', $this->perms)) {
                        $actions[] = [
                            'type' => 'edit',
                            // 'page' => true,
                            'id' => $record->id,
                            'url' => route($this->routes.'.edit', $record->id),
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

                    $actions[] = [
                        'type' => 'delete',
                        'id' => $record->id,
                        'method'=>'post',
                        'url' => route($this->routes . '.destroy', $record->id),
                    ];
                }

                if ($record->checkAction('history', $this->perms)) {
                    $actions[] = 'type:history';
                }

                $dep = Perbaikan::where('id',$record->perbaikan_id)->value('departemen_id');
                $parent = OrgStruct::where('id',$dep)->value('parent_id');

                if($parent == 3 || $dep == 3){
                    $this->module = 'usulan_pembelian-sperpat';
                }else{
                    $this->module = 'usulan_pembelian-sperpat-umum';
                }


                if($this->module == 'usulan_pembelian-sperpat'){
                    //unit penunjang
                    $approval1 = $record->whereHas('approvals', function ($q) use ($record) {
                        $q->where('target_id',$record->id)->where('module',$this->module)->where('role_id',5)->where('status','!=','approved');
                    })->count();
                    
                    //perencanaan
                    $approval2 = $record->whereHas('approvals', function ($q) use ($record) {
                            $q->where('target_id',$record->id)->where('status','!=','approved')->where('role_id',3);
                    })->count();

                    //direksi
                    $approval3 = $record->whereHas('approvals', function ($q) use ($record) {
                        $q->where('target_id',$record->id)->where('status','!=','approved')->where('role_id',2);
                    })->count();

                }else{
                    //unit umum
                    $approval1 = $record->whereHas('approvals', function ($q) use ($record) {
                        $q->where('target_id',$record->id)->where('module',$this->module)->where('role_id',5)->where('status','!=','approved')->where('order',1);
                    })->count();
                    
                    //unit penunjang
                    $approval2 = $record->whereHas('approvals', function ($q) use ($record) {
                        $q->where('target_id',$record->id)->where('status','!=','approved')->where('role_id',5)->where('order',2);
                    })->count();

                    //perencanaan
                    $approval3 = $record->whereHas('approvals', function ($q) use ($record) {
                        $q->where('target_id',$record->id)->where('status','!=','approved')->where('role_id',3)->where('order',3);
                    })->count();

                    //direktur
                    $approval4 = $record->whereHas('approvals', function ($q) use ($record) {
                        $q->where('target_id',$record->id)->where('status','!=','approved')->where('role_id',2)->where('order',4);
                    })->count();
                }

                    
                // yang ke 1 status di approved jadi dia null

                if($record->sper_status =='waiting.approval'){
                    $actions[] = 'type:tracking';
                    if($user->hasRole('Sub Bagian Program Perencanaan') || $user->hasRole('Direksi') || $user->position->location->level=='department'){

                        if($user->hasRole('Sub Bagian Program Perencanaan')){
                            if(($approval1 == 0)&& ($approval2 != 0) && $this->module =='usulan_pembelian-sperpat'  ){
                                $actions[] = [
                                    'type' => 'approval',
                                    'label' => 'Approval',
                                    'page' => true,
                                    'id' => $record->id,
                                    'url' => route($this->routes . '.approval', $record->id)
                                ];
                            }

                            if(($approval1 == 0) && ($approval2 == 0) && ($approval3 != 0) && $this->module =='usulan_pembelian-sperpat-umum'  ){
                                $actions[] = [
                                    'type' => 'approval',
                                    'label' => 'Approval',
                                    'page' => true,
                                    'id' => $record->id,
                                    'url' => route($this->routes . '.approval', $record->id)
                                ];
                            }

                        }

                        if($user->hasRole('Umum')){
                            // dd($approval1);
                            if($this->module == 'usulan_pembelian-sperpat-umum' && $approval1 > 0 && $user->position->location->level=='department' && $user->position->location->id == $parent){
                                $actions[] = [
                                    'type' => 'approval',
                                    'label' => 'Approval',
                                    'page' => true,
                                    'id' => $record->id,
                                    'url' => route($this->routes . '.approval', $record->id)
                                ];
                            }
                            
                            if($this->module == 'usulan_pembelian-sperpat-umum' && $approval1 == 0 && $approval2 > 0 && $user->position->location->id=='3'){

                                $actions[] = [
                                    'type' => 'approval',
                                    'label' => 'Approval',
                                    'page' => true,
                                    'id' => $record->id,
                                    'url' => route($this->routes . '.approval', $record->id)
                                ];

                                $actions[] = [
                                    'type' => 'edit',
                                    'page' => true,
                                    'label' => 'Edit Harga',
                                    // 'icon' => 'fa fa-plus text-info',
                                    'id' => $record->id,
                                    'url' => route($this->routes . '.editHarga', $record->id),
                                ];
                            }

                            if($this->module == 'usulan_pembelian-sperpat' && $approval1 > 0 && $user->position->location->id=='3'){

                                $actions[] = [
                                    'type' => 'approval',
                                    'label' => 'Approval',
                                    'page' => true,
                                    'id' => $record->id,
                                    'url' => route($this->routes . '.approval', $record->id)
                                ];
                            }

                        }

                        if($user->hasRole('Direksi') && $approval3 ==0 && $this->module == 'usulan_pembelian-sperpat-umum'||$user->hasRole('Direksi') && $approval2 ==0 && $this->module == 'usulan_pembelian-sperpat'){
                            $actions[] = [
                                'type' => 'approval',
                                'label' => 'Approval',
                                'page' => true,
                                'id' => $record->id,
                                'url' => route($this->routes . '.approval', $record->id)
                            ];
                            
                        }

                    }
                }
                
                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns(['repair_type',
            'status','updated_by','action'])
            ->make(true);
    }

    public function create()
    {
        return $this->render('perbaikan.usulan-sperpat.create');
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:no_surat|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:vendor|label:Vendor|className:text-center|width:250px'),
                    $this->makeColumn('name:procurement_year|label:Periode Usulan|className:text-center|width:300px'),
                    $this->makeColumn('name:repair_type|label:Jenis Perbaikan|className:text-center|width:250px'),
                    $this->makeColumn('name:status'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);
        return $this->render($this->views . '.index');
    }

    public function history(TransPerbaikanDisposisi $record)
    {
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    public function show(TransPerbaikanDisposisi $record)
    {
        $perbaikan =  Perbaikan::where('id',$record->perbaikan_id)->first();
        $aset = Aset::where('id', $perbaikan->kib_id)->first();
        $perbaikan = Perbaikan::where('kib_id', $perbaikan->id)->where('status', 'approved')->pluck('id')->toArray();
        $perbaikan2 = Activity::where('module', 'perbaikan-aset')->whereIn('target_id', $perbaikan)->where('message', 'LIKE', '%Update Hasil Perbaikan%')->get();

        $umur = date_diff(date_create($aset->book_date), date_create(now()));

        if($record->type =='sperpat'){
            $ts_cost = UsulanSperpat::where('trans_perbaikan_id',$record->id)->sum('total_cost');
        }else if($record->type == 'vendor'){
            $ts_cost = $record->total_cost_vendor;
        }else{
            $ts_cost1 = UsulanSperpat::where('trans_perbaikan_id',$record->id)->sum('total_cost');
            $ts_cost = $ts_cost1 + $record->total_cost_vendor;
        }

        $data = [
            'perbaikan' => $perbaikan2,
            'nilai' => $aset->book_value,
            'umur_tahun' => $umur->y,
            'umur_bulan' => $umur->m,
            // 'umur' => date_diff(date_create($aset->book_date), date_create(now()))->y, // Ambil perbedaan tahun
            'nilai_rekomen_50' => $aset->acq_value * 0.5,
            'nilai_rekomen_30' => $aset->acq_value * 0.3,
            'nilai_residu' => $aset->residual_value,
            'biaya_perbaikan' => $ts_cost,
        ];

        $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:sperpat_name|label:Nama Sperpat|className:text-left|width:200px'),
                    $this->makeColumn('name:qty|label:Jumlah|className:text-center|width:300px'),
                    $this->makeColumn('name:unit_cost|label:Harga Satuan|className:text-center|width:250px'),
                    $this->makeColumn('name:total_cost|label:Harga Total|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|label:Diperbarui|width:300px'),
                    $this->makeColumn('name:action_show|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.show', compact(['record','data']));
    }

    public function edit(TransPerbaikanDisposisi $record)
    {

        return $this->render($this->views . '.edit', compact(['record']));
    }

    public function editHarga(TransPerbaikanDisposisi $record)
    {
        $perbaikan =  Perbaikan::where('id',$record->perbaikan_id)->first();
        $aset = Aset::where('id', $perbaikan->kib_id)->first();
        $perbaikan = Perbaikan::where('kib_id', $perbaikan->id)->where('status', 'approved')->pluck('id')->toArray();
        $perbaikan2 = Activity::where('module', 'perbaikan-aset')->whereIn('target_id', $perbaikan)->where('message', 'LIKE', '%Update Hasil Perbaikan%')->get();

        $umur = date_diff(date_create($aset->book_date), date_create(now()));

        if($record->type =='sperpat'){
            $ts_cost = UsulanSperpat::where('trans_perbaikan_id',$record->id)->sum('total_cost');
        }else if($record->type == 'vendor'){
            $ts_cost = $record->total_cost_vendor;
        }else{
            $ts_cost1 = UsulanSperpat::where('trans_perbaikan_id',$record->id)->sum('total_cost');
            $ts_cost = $ts_cost1 + $record->total_cost_vendor;
        }

        $data = [
            'perbaikan' => $perbaikan2,
            'nilai' => $aset->book_value,
            'umur_tahun' => $umur->y,
            'umur_bulan' => $umur->m,
            // 'umur' => date_diff(date_create($aset->book_date), date_create(now()))->y, // Ambil perbedaan tahun
            'nilai_rekomen_50' => $aset->acq_value * 0.5,
            'nilai_rekomen_30' => $aset->acq_value * 0.3,
            'nilai_residu' => $aset->residual_value,
            'biaya_perbaikan' => $ts_cost,
        ];

        $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
        // dd($this->routes.'.detail');
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:sperpat_name|label:Nama Sperpat|className:text-left|width:200px'),
                    $this->makeColumn('name:qty|label:Jumlah|className:text-center|width:300px'),
                    $this->makeColumn('name:unit_cost|label:Harga Satuan|className:text-center|width:250px'),
                    $this->makeColumn('name:total_cost|label:Harga Total|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|label:Diperbarui|width:300px'),
                    $this->makeColumn('name:action|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.editHarga', compact(['record','data']));
    }


    public function store(PerbaikanDisposisiRequest $request)
    {
        $record = new TransPerbaikanDisposisi;
        // dd($request->all());
        return $record->handleStore($request);
    }

    public function updateSummary(PerbaikanDisposisiRequest $request, TransPerbaikanDisposisi $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function updateHarga(Request $request, TransPerbaikanDisposisi $record)
    {
        // dd($request->all());
        return $record->handleStoreOrUpdateHarga($request);
    }

    public function detailGrid(TransPerbaikanDisposisi $record)
    {        
        $user = auth()->user();
        $records = UsulanSperpat::with('perbaikans')
        ->whereHas(
            'perbaikans',
            function ($q) use ($record) {
                $q->where('trans_perbaikan_id', $record->id);
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
                'sperpat_name',
                function ($detail) {
                    return $detail->sperpat_name ? $detail->sperpat_name : '';
                }
            )
            ->addColumn(
                'qty',
                function ($detail) {
                    return $detail->qty ? $detail->qty : '';
                }
            )
            ->addColumn(
                'unit_cost',
                function ($detail) {
                    //return $detail->unit_cost;
                    return number_format($detail->unit_cost, 0, ',', ',');
                }
            )
            ->addColumn(
                'total_cost',
                function ($detail) {
                 //   return $detail->total_cost;
                    return number_format($detail->total_cost, 0, ',', ',');
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
                function ($detail) use ($user, $record) {
                    $actions = [];
                    $actions[] = [
                        'type' => 'show',
                        'url' => route($this->routes . '.detailShow', $detail->id),
                    ];                    
                    return $this->makeButtonDropdown($actions, $detail->id);
                }
            )
            ->addColumn(
                'action',
                function ($detail) use ($user, $record) {
                    
                    $actions = [];
                    // dd($record->status);

                    if($record->sper_status == 'waiting.approval'){
                        $actions[] = [
                            'type' => 'show',
                            'url' => route($this->routes . '.detailShow', $detail->id),
                        ];
    
                        $actions[] = [
                            'type' => 'edit',
                            'label' => 'Edit Harga',
                            'url' => route($this->routes . '.detailEditHarga', $detail->id),
                        ];

                    }else{
                        $actions[] = [
                            'type' => 'show',
                            'url' => route($this->routes . '.detailShow', $detail->id),
                        ];
    
                        $actions[] = [
                            'type' => 'edit',
                            'url' => route($this->routes . '.detailEdit', $detail->id),
                        ];
    
                        $actions[] = [
                            'type' => 'delete',
                            'url' => route($this->routes . '.detailDestroy', $detail->id),
                        ];
                    }

                    return $this->makeButtonDropdown($actions, $detail->id);
                }
            )
            ->rawColumns(['action','action_show','updated_by','created_by'])
            ->make(true);
    }

    public function detail(TransPerbaikanDisposisi $record)
    {
        //dd($record);
        $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:sperpat_name|label:Nama Sperpat|className:text-left|width:200px'),
                    $this->makeColumn('name:qty|label:Jumlah|className:text-center|width:300px'),
                    $this->makeColumn('name:unit_cost|label:Harga Satuan|className:text-center|width:250px'),
                    $this->makeColumn('name:total_cost|label:Harga Total|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|label:Diperbarui|width:300px'),
                    $this->makeColumn('name:action|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.detail', compact('record'));
    }

    public function submit(TransPerbaikanDisposisi $record)
    {
        $flowApproval = $record->getFlowApproval($this->module);
        return $this->render('globals.submit', compact('record', 'flowApproval'));
    }

    public function submitSave(TransPerbaikanDisposisi $record, Request $request)
    {
        return $record->handleSubmitSave($request);
    }

    public function update(Request $request, TransPerbaikanDisposisi $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(TransPerbaikanDisposisi $record)
    {
        return $record->handleDestroy();
    }

    public function tracking(TransPerbaikanDisposisi $record)
    {
        $dep = Perbaikan::where('id',$record->perbaikan_id)->value('departemen_id');
        $parent = OrgStruct::where('id',$dep)->value('parent_id');

        if($parent == 3 || $dep == 3){
            $this->module = 'usulan_pembelian-sperpat';
        }else{
            $this->module = 'usulan_pembelian-sperpat-umum';
        }
        $module = $this->module;
        

        if ($record->status === 'waiting.approval.revisi') {
            $module = $module . '_upgrade';
        }
        return $this->render('globals.tracking', compact('record', 'module'));
    }

    public function approval(TransPerbaikanDisposisi $record)
    {
        $perbaikan =  Perbaikan::where('id',$record->perbaikan_id)->first();
        $aset = Aset::where('id', $perbaikan->kib_id)->first();
        $perbaikan = Perbaikan::where('kib_id', $perbaikan->id)->where('status', 'approved')->pluck('id')->toArray();
        $perbaikan2 = Activity::where('module', 'perbaikan-aset')->whereIn('target_id', $perbaikan)->where('message', 'LIKE', '%Update Hasil Perbaikan%')->get();
        $umur = date_diff(date_create($aset->book_date), date_create(now()));
        
        if($record->type =='sperpat'){
            $ts_cost = UsulanSperpat::where('trans_perbaikan_id',$record->id)->sum('total_cost');
        }else if($record->type == 'vendor'){
            $ts_cost = $record->total_cost_vendor;
        }else{
            $ts_cost1 = UsulanSperpat::where('trans_perbaikan_id',$record->id)->sum('total_cost');
            $ts_cost = $ts_cost1 + $record->total_cost_vendor;
        }

        $data = [
            'perbaikan' => $perbaikan2,
            'nilai' => $aset->book_value,
            'umur_tahun' => $umur->y,
            'umur_bulan' => $umur->m,
            // 'umur' => date_diff(date_create($aset->book_date), date_create(now()))->y, // Ambil perbedaan tahun
            'nilai_rekomen_50' => $aset->acq_value * 0.5,
            'nilai_rekomen_30' => $aset->acq_value * 0.3,
            'nilai_residu' => $aset->residual_value,
            'biaya_perbaikan' => $ts_cost,
        ];

        $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:sperpat_name|label:Nama Sperpat|className:text-left|width:200px'),
                    $this->makeColumn('name:qty|label:Jumlah|className:text-center|width:300px'),
                    $this->makeColumn('name:unit_cost|label:Harga Satuan|className:text-center|width:250px'),
                    $this->makeColumn('name:total_cost|label:Harga Total|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|label:Diperbarui|width:300px'),
                    $this->makeColumn('name:action_show|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.show', compact(['record','data']));
    }

    public function reject(TransPerbaikanDisposisi $record, Request $request)
    {
        $request->validate(
            [
                'note'  => 'required',
            ]
        );
        return $record->handleReject($request);
    }

    public function approve(TransPerbaikanDisposisi $record, Request $request)
    {
        return $record->handleApprove($request);  
    }

    public function detailUpdate(UsulanSperpatRequest $request,  UsulanSperpat $detail)
    {
        $record = $detail->perbaikans;
        return $record->handleDetailStoreOrUpdate($request,$detail);
    }

    public function detailUpdateHarga(Request $request,  UsulanSperpat $detail)
    {
        $record = $detail->perbaikans;
        return $record->handleDetailStoreOrUpdateHarga($request,$detail);
    }


    public function detailCreate(TransPerbaikanDisposisi $record)
    {
        $baseContentReplace = 'base-modal--render';
        $type ='create';
        return $this->render($this->views . '.detail.create', compact('record', 'baseContentReplace','type'));
    }

    public function detailStore(UsulanSperpatRequest $request, TransPerbaikanDisposisi $record)
    {
        $detail = new UsulanSperpat;
        $record = TransPerbaikanDisposisi::find($request->trans_perbaikan_id);
        return $record->handleDetailStoreOrUpdate($request, $detail);
    }

    public function detailEdit(UsulanSperpat $detail)
    {
        $type='edit';
        $baseContentReplace = 'base-modal--render';
        $record = $detail->perbaikans;
        
        return $this->render($this->views . '.detail.edit', compact('record','detail','baseContentReplace','type'));
    }

    public function detailEditHarga(UsulanSperpat $detail)
    {
        $type='edit';
        $baseContentReplace = 'base-modal--render';
        $record = $detail->perbaikans;
        
        return $this->render($this->views . '.detail.editHarga', compact('record','detail','baseContentReplace','type'));
    }

    public function detailShow(UsulanSperpat $detail)
    {
        $type ='show';
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.detail.show', compact('type','detail', 'baseContentReplace'));
    }

    public function detailDestroy(UsulanSperpat $detail)
    {
        $record = $detail->perbaikans;
        return $record->handleDetailDestroy($detail);
    }


}


