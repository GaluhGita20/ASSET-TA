<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PerencanaanRequest;
use App\Http\Requests\Pengajuan\PerencanaanUpdateRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailUpdateHargaRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailRequest;
use App\Http\Requests\Pengajuan\PerubahanPerencanaanRequest;
use App\Http\Requests\Pengajuan\PerencanaanDisposisiRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailCreateRequest;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Pengajuan\PerubahanPerencanaan;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Dompdf\Dompdf;
use PDF;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PerubahanPerencanaanAsetUmumController extends Controller
{
    protected $module = 'perubahan-usulan-umum';
    protected $routes = 'pengajuan.perubahan-usulan-umum';
    protected $views = 'pengajuan.perubahan-perencanaan-aset';
    protected $perms = 'perubahan-usulan-umum';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Perubahan Usulan Aset',
            'breadcrumb' => [
                'Home' => route('home'),
                // 'Pengajuan' => '#',
                'Perubahan Perencanaan' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function index()
    {
        // dd('tes');
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:no_surat|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:struct|label:Unit Kerja|className:text-center|width:250px'),
                    $this->makeColumn('name:year_application|label:Periode Perencanaan|className:text-center|width:300px'),
                    $this->makeColumn('name:aset|label:Nama Aset|className:text-center'),
                //    $this->makeColumn('name:spesifikasi|label:Spesifikasi Aset|className:text-center'),
                    $this->makeColumn('name:jumlah_disetujui|label:Jumlah Disetujui|className:text-center'),
                    $this->makeColumn('name:pagu_unit|label:Pagu Unit (Rupiah)|className:text-center'),
                    $this->makeColumn('name:pagu_total|label:Total Pagu (Rupiah)|className:text-center'),
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
        //dd('tes');
        $user = auth()->user();
        $records = PerubahanPerencanaan::with('detailUsulan')->whereHas('detailUsulan', function ($qqq){
            $qqq->whereHas('perencanaan',function($y){
                $y->whereHas('struct', function ($q){
                    $q->where('id','<>',3)->whereIn('parent_id',[4,5,6]); 
                });
            });
        })->gridPelayanan()->filters()->dtGet();

        return DataTables::of($records)
            ->addColumn('#', function ($record) {
                return request()->start;
            })
            ->addColumn(
                'struct',
                function ($record) {
                    return $record->detailUsulan->perencanaan->struct ? $record->detailUsulan->perencanaan->struct->name : '-';
                }
            )

            ->addColumn('no_surat', function ($record) {
                return $record->detailUsulan->perencanaan->code ? $record->detailUsulan->perencanaan->code : '-';
            })

            ->addColumn('year_application', function ($record) {
                return  $record->detailUsulan->perencanaan->procurement_year ?  $record->detailUsulan->perencanaan->procurement_year : '-';
            })

            ->addColumn('aset', function ($record) {
                return  $record->detailUsulan->asetd->name ?  $record->detailUsulan->asetd->name : '-';
            })

            ->addColumn('spesifikasi', function ($record) {
                return  $record->detailUsulan->desc_spesification ?  $record->detailUsulan->desc_spesification : '-';
            })
            
            ->addColumn('jumlah_disetujui', function ($record) use ($user) {
                return $record->detailUsulan->qty_agree ?  number_format($record->detailUsulan->qty_agree, 0, ',', ',') : '-';
            })

            ->addColumn('pagu_unit', function ($record) use ($user) {
                return $record->detailUsulan->HPS_unit_cost ? number_format($record->detailUsulan->HPS_unit_cost, 0, ',', ',') : '-';
            })

            ->addColumn('pagu_total', function ($record) use ($user) {
                return $record->detailUsulan->HPS_total_cost ? number_format($record->detailUsulan->HPS_total_cost, 0, ',', ',') : '-';
            })

            ->addColumn('status', function ($record) use ($user) {
                return $record->labelStatus($record->status ?? 'new');
            })

            ->addColumn('updated_by', function ($record) {
                if ($record->status === 'new') {
                    //return "";
                    return $record->createdByRaw();
                } else {
                    return $record->createdByRaw();
                }
            })

            ->addColumn('action', function ($record) use ($user) {
                $actions = [];

                if($record->status == 'rejected'){
                    $actions[] = [
                        'type' => 'show',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes . '.show', $record->id),
                    ];

                    $actions[] = [
                        'type' => 'edit',
                        'page' => true,
                        'label' => 'Update Data',
                        'icon' => 'fa fa-plus text-info',
                        'id' => $record->id,
                        'url' => route($this->routes . '.updateSpesifikasi', $record->id),
                    ];

                    if ($record->checkAction('history', $this->perms)) {
                        $actions[] = 'type:history';
                    }
                }else{

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
                            'page' => true,
                            'label' => 'Detail',
                            'icon' => 'fa fa-plus text-info',
                            'id' => $record->id,
                            'url' => route($this->routes . '.detail', $record->id),
                        ];
                    }
    
                    if ($record->checkAction('edit', $this->perms)) {
                        $actions[] = [
                            'type' => 'edit',
                            'id' => $record->id,
                            'url' => route($this->routes . '.edit', $record->id),
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
    
                    $approval1 = $record->approvals()
                        ->where('target_id', $record->id)
                        ->where('status', '!=', 'approved')->where('order',1)
                        ->count();
    
                    $approval2 = $record->approvals()
                        ->where('target_id', $record->id)
                        ->where('status', '!=', 'approved')->where('order',2)
                        ->count();

                    $approval3 = $record->approvals()
                    ->where('target_id', $record->id)
                    ->where('status', '!=', 'approved')->where('order',3)
                    ->count();
    
                    if ($record->checkAction('approval', $this->perms) && $user->position->location->level == 'department' && $approval1 == 1 || $record->checkAction('approval', $this->perms) && $user->position->location->name == 'Sub Bagian Program Perencanaan dan Pelaporan') {
                        $actions[] = [
                            'type' => 'approval',
                            'label' => 'Approval',
                            'page' => true,
                            'id' => $record->id,
                            'url' => route($this->routes . '.approval', $record->id)
                        ];
                    }

                    if ($record->checkAction('approval', $this->perms) && $user->position->location->name == 'Direksi RSUD' && $approval3 == 0 ) {
                        $actions[] = [
                            'type' => 'approval',
                            'label' => 'Approval',
                            'page' => true,
                            'id' => $record->id,
                            'url' => route($this->routes . '.approval', $record->id)
                        ];
                    }
    
                    if($record->status=='waiting.approval' && auth()->user()->position->name == "Kepala Bidang Penunjang Medik dan Non Medik" && $approval1 == 0 &&  $approval2 == 1){
                        $actions[] = [
                            'type' => 'approval',
                            'label' => 'Approval',
                            'page' => true,
                            'id' => $record->id,
                            'url' => route($this->routes . '.approval', $record->id)
                        ];
    
                        $actions[] = [
                            'type' => 'edit',
                            'label' => 'Edit Harga',
                            'id' => $record->id,
                            'url' => route($this->routes . '.editHarga', $record->id),
                        ];
                    }
    
                    // if ($record->checkAction('approval', $this->perms) 
                    //     && $approval1 == 1 
                    //     && collect(auth()->user()->roles)->contains('name', 'PPK')){
    
                    //     $actions[] = [
                    //         'type' => 'approval',
                    //         'label' => 'Approval',
                    //         'page' => true,
                    //         'id' => $record->id,
                    //         'url' => route($this->routes . '.approval', $record->id)
                    //     ];
                    // }
    
                    if($record->status == 'waiting.update'){
                        $actions[] = [
                            'type' => 'edit',
                            'page' => true,
                            'label' => 'Update Data',
                            'icon' => 'fa fa-plus text-info',
                            'id' => $record->id,
                            'url' => route($this->routes . '.updateSpesifikasi', $record->id),
                        ];
                    }
    
    
                    if ($record->checkAction('tracking', $this->perms)) {
                        $actions[] = 'type:tracking';
                    }
    
                    if ($record->checkAction('history', $this->perms)) {
                        $actions[] = 'type:history';
                    }
                }


                return $this->makeButtonDropdown($actions, $record->id);
            })

            ->rawColumns(['struct',
            'no_surat',
            'year_application',
            'aset',
            'jumlah_disetujui',
            'pagu_unit',
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

    public function store(PerubahanPerencanaanRequest $request)
    {
        $record = new PerubahanPerencanaan;
        return $record->handleStore($request);
    }

    public function edit(PerubahanPerencanaan $record)
    {
        $type ='edit';
        return $this->render($this->views . '.edit', compact('record','type'));
    }

    public function editHarga(PerubahanPerencanaan $record)
    {
        $type ='edit';
        return $this->render($this->views . '.editHarga', compact('record','type'));
    }

    public function detail(PerubahanPerencanaan $record)
    {
        $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
        return $this->render($this->views . '.detail', compact('record'));
    }

    public function updateSpesifikasi(PerubahanPerencanaan $record)
    {
        return $this->render($this->views . '.updateSpesifikasi', compact('record'));
    }

    public function update(PerubahanPerencanaanRequest $request, PerubahanPerencanaan $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function updateHarga(Request $request, PerubahanPerencanaan $record)
    {
        return $record->handleStoreOrUpdateHarga($request);
    }
    
    public function updateSummary(Request $request, PerubahanPerencanaan $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function saveSpesifikasi(Request $request, PerubahanPerencanaan $record)
    {

        return $record->handleStoreOrUpdateSpesifikasi($request);
    }

    public function destroy(PerubahanPerencanaan $record)
    {
        return $record->handleDestroy();
    }

    public function submit(PerubahanPerencanaan $record)
    {
        $flowApproval = $record->getFlowApproval($this->module);
        return $this->render('globals.submit', compact('record', 'flowApproval'));
    }

    public function submitSave(PerubahanPerencanaan $record, Request $request)
    {
        return $record->handleSubmitSave($request);
    }

    public function approve(PerubahanPerencanaan $record, Request $request)
    {
        return $record->handleApprove($request);
        
    }

    public function approval(PerubahanPerencanaan $record)
    {
        return $this->render($this->views . '.show', compact('record'));
    }

    public function reject(PerubahanPerencanaan $record, Request $request)
    {
        $request->validate(
            [
                'note'  => 'required',
            ]
        );

        return $record->handleReject($request);
    }

    public function history(PerubahanPerencanaan $record)
    {
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    public function tracking(PerubahanPerencanaan $record)
    {
        $module = $this->module;
        if ($record->status === 'waiting.approval.revisi') {
            $module = $module . '_upgrade';
        }
        return $this->render('globals.tracking', compact('record', 'module'));
    }

    public function show(PerubahanPerencanaan $record)
    {
        return $this->render($this->views . '.show', compact('record'));
    }
    
    public function print(PerubahanPerencanaan $record, $title = '')
    {

    }




}



