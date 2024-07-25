<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PenghapusanRequest;
use App\Models\Pengajuan\Penghapusan;
use App\Models\Pengajuan\Pemutihans;
use App\Models\Globals\Approval;
use App\Models\Inventaris\Aset;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PenghapusanAsetController extends Controller
{
    protected $module = 'penghapusan-aset';
    protected $routes = 'pengajuan.penghapusan-aset';
    protected $views = 'pengajuan.penghapusan-aset';
    protected $perms = 'penghapusan-aset';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Pengajuan Penghapusan',
            'breadcrumb' => [
                'Home' => route('home'),
                // 'Pengajuan' => '#',
                'Pengajuan Penghapusan' => route($this->routes . '.index'),
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
                    $this->makeColumn('name:type_aset|label:Tipe Aset|className:text-center|width:250px'),
                    $this->makeColumn('name:nama_aset|label:Nama Aset|className:text-center|width:250px'),
                    $this->makeColumn('name:departemen|label:Departemen|className:text-center|width:300px'),
                    $this->makeColumn('name:nilai_hapus|label:Nilai Aset Dihapus (Rupiah)|className:text-center|width:300px'),
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
        $records = Penghapusan::grid()->filters()->dtGet();

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

            ->addColumn('status', function ($record) use ($user) {
                return $record->labelStatus($record->status ?? 'new');
            })

            ->addColumn('nilai_hapus', function ($record) use ($user) {
                return number_format($record->asets->book_value, 0, ',', ',') ?? '0' ;
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

                if ($record->checkAction('tracking', $this->perms)) {
                    $actions[] = 'type:tracking';
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


                // dd(auth()->user()->position->location_id);
                if ($record->checkAction('delete', $this->perms)) {
                    $actions[] = [
                        'type' => 'delete',
                        'id' => $record->id,
                        'method'=>'post',
                        'url' => route($this->routes . '.destroy', $record->id),
                    ];
                }

                if ( ($record->checkAction('approval', $this->perms) && (auth()->user()->position->location_id == 55 )) || (($record->checkAction('approval', $this->perms))  && (auth()->user()->position->location_id == 8)) ) {
                    // dd(auth()->user()->position->location_id);
                    $actions[] = [
                        'type' => 'approval',
                        'label' => 'Approval',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes . '.approval', $record->id)
                    ];
                }
                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns([
            'no_surat',
            'departemen',
            'nama_aset',
            'status','updated_by','action'])
            ->make(true);
    }

    public function store(PenghapusanRequest $request)
    {
        $record = new Penghapusan;
        return $record->handleStore($request);
    }

    public function edit(Penghapusan $record)
    {
        $type ='edit';

        $aset = Aset::where('id', $record->kib_id)->first();
        $umur = date_diff(date_create($aset->book_date), date_create(now()));
        
        $maut = $record->calculateUtilityScore($aset);
        
        $data = [
            'nilai' => $aset->book_value,
            'umur_tahun' => $umur->y,
            'umur_bulan' => $umur->m,
            // 'umur' => date_diff(date_create($aset->book_date), date_create(now()))->y, // Ambil perbedaan tahun
            'nilai_rekomen_50' => $aset->acq_value * 0.5,
            'nilai_rekomen_30' => $aset->acq_value * 0.3,
            'nilai_residu' => $aset->residual_value,
            'MAUT_score' => $maut,
        ];

        return $this->render($this->views . '.show', compact('record','type','data'));
    }

    public function detail(Penghapusan $record)
    {
        $aset = Aset::where('id', $record->kib_id)->first();
        $umur = date_diff(date_create($aset->book_date), date_create(now()));
        
        $maut = $record->calculateUtilityScore($aset);
        
        $data = [
            'nilai' => $aset->book_value,
            'umur_tahun' => $umur->y,
            'umur_bulan' => $umur->m,
            // 'umur' => date_diff(date_create($aset->book_date), date_create(now()))->y, // Ambil perbedaan tahun
            'nilai_rekomen_50' => $aset->acq_value * 0.5,
            'nilai_rekomen_30' => $aset->acq_value * 0.3,
            'nilai_residu' => $aset->residual_value,
            'MAUT_score' => $maut,
        ];

        return $this->render($this->views . '.detail', compact('record','data'));
    }

    public function update(Request $request, Penghapusan $record)
    {
        return $record->handleStoreOrUpdate($request);
    }
    
    public function destroy(Penghapusan $record)
    {
        return $record->handleDestroy();
    }

    public function submit(Penghapusan $record)
    {
        $flowApproval = $record->getFlowApproval($this->module);
        return $this->render('globals.submit', compact('record', 'flowApproval'));
    }

    public function submitSave(Penghapusan $record, Request $request)
    {
       // dd(($request->all()));
        return $record->handleSubmitSave($request);
    }

    public function approval(Penghapusan $record)
    {
        $aset = Aset::where('id', $record->kib_id)->first();
        $umur = date_diff(date_create($aset->book_date), date_create(now()));
        
        $maut = $record->calculateUtilityScore($aset);
        
        $data = [
            'nilai' => $aset->book_value,
            'umur_tahun' => $umur->y,
            'umur_bulan' => $umur->m,
            // 'umur' => date_diff(date_create($aset->book_date), date_create(now()))->y, // Ambil perbedaan tahun
            'nilai_rekomen_50' => $aset->acq_value * 0.5,
            'nilai_rekomen_30' => $aset->acq_value * 0.3,
            'nilai_residu' => $aset->residual_value,
            'MAUT_score' => $maut,
        ];

        return $this->render($this->views . '.show', compact('record','data'));
    }


    public function approve(Penghapusan $record, Request $request)
    {
        return $record->handleApprove($request);  
    }

    public function reject(Penghapusan $record, Request $request)
    {
        // dd('tes');
        $request->validate(
            [
                'note'  => 'required',
            ]
        );
        return $record->handleReject($request);
    }

    public function history(Penghapusan $record)
    {
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    public function show(Penghapusan $record)
    {
        $aset = Aset::where('id', $record->kib_id)->first();
        $umur = date_diff(date_create($aset->book_date), date_create(now()));
        
        $maut = $record->calculateUtilityScore($aset);
        
        $data = [
            'nilai' => $aset->book_value,
            'umur_tahun' => $umur->y,
            'umur_bulan' => $umur->m,
            // 'umur' => date_diff(date_create($aset->book_date), date_create(now()))->y, // Ambil perbedaan tahun
            'nilai_rekomen_50' => $aset->acq_value * 0.5,
            'nilai_rekomen_30' => $aset->acq_value * 0.3,
            'nilai_residu' => $aset->residual_value,
            'MAUT_score' => $maut,
        ];

        return $this->render($this->views . '.show', compact('record','data'));
    }

    public function tracking(Penghapusan $record)
    {
        $module = $this->module;
        if ($record->status === 'waiting.approval.revisi') {
            $module = $module . '_upgrade';
        }
        return $this->render('globals.tracking', compact('record', 'module'));
    }

    public function print(Penghapusan $record, $title = '')
    {

    }

}


