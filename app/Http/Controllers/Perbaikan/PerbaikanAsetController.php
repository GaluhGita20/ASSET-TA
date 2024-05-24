<?php

namespace App\Http\Controllers\Perbaikan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PerbaikanRequest;
use App\Http\Requests\Pengajuan\PerbaikanVerifyRequest;
use App\Http\Requests\Pengajuan\HasilPerbaikanRequest;
use App\Models\Pengajuan\Perbaikan;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PerbaikanAsetController extends Controller
{
    protected $module = 'perbaikan-aset';
    protected $routes = 'perbaikan.perbaikan-aset';
    protected $views = 'perbaikan.perbaikan-aset';
    protected $perms = 'perbaikan-aset';

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
                // 'Pengajuan' => '#',
                'Pengajuan Perbaikan' => route($this->routes . '.index'),
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
                    $this->makeColumn('name:nama_aset|label:Nama Aset|className:text-center|width:250px'),
                    $this->makeColumn('name:type_aset|label:Tipe Aset|className:text-center|width:300px'),
                    $this->makeColumn('name:departemen|label:Departemen|className:text-center|width:300px'),
                    $this->makeColumn('name:status|label:Verifikasi Kerusakan'),
                    $this->makeColumn('name:tanggal_panggil|label:Tanggal Panggil|className:text-center|width:300px'),
                    $this->makeColumn('name:check_up_aset|label:Status Pemeriksaan Awal|className:text-center|width:300px'),
                    $this->makeColumn('name:disposisi|label:Pengajuan Disposisi|className:text-center'),
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
        $records = Perbaikan::grid()->where('check_up_result',null)->filters()->dtGet();

        return DataTables::of($records)
            ->addColumn('#', function ($record) {
                return request()->start;
            })
            ->addColumn(
                'nama_aset',
                function ($record) {
                    return $record->asets ? $record->asets->usulans->asetd->name : '-';
                }
            )

            ->addColumn(
                'type_aset',
                function ($record) {
                    return $record->asets ? $record->asets->type : '-';
                }
            )

            ->addColumn('no_surat', function ($record) {
                return $record->code;
            })

            ->addColumn('departemen', function ($record) {
                return $record->deps->name;
            })

            ->addColumn('tanggal_panggil', function ($record) {
                return $record->repair_date ?  $record->repair_date->format('d/m/Y') : '-';
            })

            ->addColumn('status', function ($record) use ($user) {
                if($record->status == 'completed'){
                    return '<span class="badge bg-primary text-white">Verified</span>';
                }else{
                    return $record->labelStatus($record->status ?? 'new');
                }
//                return $record->labelStatus($record->status ?? 'new');
            })

            ->addColumn('tanggal_pengajuan', function ($record) {
                return $record->submission_date ?  $record->submission_date->format('d/m/Y') : '-';
            })

            ->addColumn('check_up_aset', function ($record) use ($user) {
                if($record->check_up_result != null){
                    return $record->check_up_result ? '<span class="badge bg-success text-white"> Done</span>' : '-';
                }else{
                    return $record->repair_results ? '<span class="badge bg-danger text-white"> Not Done </span>' : '-';
                }
            })

            ->addColumn('disposisi', function ($record) use ($user) {
                if($record->is_disposisi == 'yes'){
                    return $record->check_up_result ? '<span class="badge bg-success text-white"> Yes </span>' : '-';
                }else{
                    return $record->repair_results ? '<span class="badge bg-danger text-white"> No </span>' : '-';
                }
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

                if (auth()->user()->checkPerms('perbaikan-aset.edit') && $record->status =='waiting.verify' && $record->repair_date == null ) {
                    $actions[] = [
                        'type' => 'delete',
                        'id' => $record->id,
                        'method'=>'post',
                        'url' => route($this->routes . '.destroy', $record->id),
                    ];
                }

                if(auth()->user()->hasRole('Sarpras') && $record->status=='waiting.verify'){
                    $actions[] = [
                        'type' => 'approval',
                        'label' => 'Verify',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes . '.edit', $record->id)
                    ];
                }

                if(auth()->user()->hasRole('Sarpras') && $record->status =='approved' && $record->check_up_result == null && auth()->user()->checkPerms('perbaikan-aset.edit')){
                    $actions[] = [
                        'type' => 'edit',
                        'label' => 'Perbarui Hasil Pemeriksaan',
                        'icon' => 'fa fa-wrench text-success',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes . '.edit', $record->id)
                    ];
                }
                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns([
            'hasil_perbaikan',
            'check_up_aset',
            'disposisi',
            'no_surat',
            'departemen',
            'nama_aset',
            'tanggal_pengajuan',
            'status','updated_by','action'])
            ->make(true);
    }

    public function store(PerbaikanRequest $request)
    {
        $record = new Perbaikan;
        return $record->handleStore($request);
    }

    public function edit(Perbaikan $record)
    {
        $type ='edit';
        return $this->render($this->views . '.edit', compact('record','type'));
    }

    public function detail(Perbaikan $record)
    {
        return $this->render($this->views . '.detail', compact('record'));
    }

    public function updateSummary(PerbaikanVerifyRequest $request, Perbaikan $record)
    {
        
        return $record->handleVerify($request);
    }

    public function update(HasilPerbaikanRequest $request, Perbaikan $record)
    {
        return $record->handleStoreOrUpdate($request);
    }
    

    public function destroy(Perbaikan $record)
    {
        return $record->handleDestroy();
    }

    public function submit(Perbaikan $record)
    {
        $flowApproval = $record->getFlowApproval($this->module);
        return $this->render('globals.submit', compact('record', 'flowApproval'));
    }

    public function submitSave(Perbaikan $record, Request $request)
    {
        return $record->handleSubmitSave($request);
    }

    public function approval(Perbaikan $record)
    {
        return $this->render($this->views . '.edit', compact('record'));
    }


    public function approve(Perbaikan $record, Request $request)
    {
        dd($request->all());
        return $record->handleApprove($request);
        
    }

    public function reject(Perbaikan $record, Request $request)
    {
        $request->validate(
            [
                'note'  => 'required',
            ]
        );
        return $record->handleReject($request);
    }

    public function history(Perbaikan $record)
    {
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    public function show(Perbaikan $record)
    {
        return $this->render($this->views . '.show', compact('record'));
        //ambil data dari show => detail
    }

    public function tracking(Perbaikan $record)
    {
        $module = $this->module;
        if ($record->status === 'waiting.approval.revisi') {
            $module = $module . '_upgrade';
        }
        return $this->render('globals.tracking', compact('record', 'module'));
    }

    public function print(Perbaikan $record, $title = '')
    {

    }


    //usulan sperpat

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

}


