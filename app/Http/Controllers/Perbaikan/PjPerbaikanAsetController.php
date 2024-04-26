<?php

namespace App\Http\Controllers\Perbaikan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PerbaikanRequest;
use App\Http\Requests\Pengajuan\PerbaikanVerifyRequest;
use App\Http\Requests\Pengajuan\HasilPerbaikan2Request;
use App\Models\Perbaikan\TransPerbaikanDisposisi;
use App\Models\Perbaikan\UsulanSperpat;
use App\Models\Pengajuan\Perbaikan;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PjPerbaikanAsetController extends Controller
{
    protected $module = 'pj-perbaikan-aset';
    protected $routes = 'perbaikan.pj-perbaikan';
    protected $views = 'perbaikan.pj-perbaikan';
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
                    $this->makeColumn('name:is_disposisi|label:Status Diposisi|className:text-center'),
                    $this->makeColumn('name:status|label:Verifikasi Kerusakan'),
                    $this->makeColumn('name:tanggal_panggil|label:Tanggal Panggil|className:text-center|width:300px'),
                    $this->makeColumn('name:hasil_perbaikan|label:Hasil Perbaikan|className:text-center|width:300px'),
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
        $records = Perbaikan::grid()->where('status', 'Approved')->where('check_up_result','<>',null) // Atur urutan tambahan jika diperlukan
        ->filters()
        ->dtGet();

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
            ->addColumn('is_disposisi', function ($record) use ($user) {
                if($record->is_disposisi == 'yes'){
                    return '<span class="badge bg-success text-white"> Yes </span>';
                }else{
                    return '<span class="badge bg-danger text-white"> No </span>';
                }
            })

            ->addColumn('status', function ($record) use ($user) {
                return $record->labelStatus($record->status ?? 'new');
            })

            ->addColumn('tanggal_pengajuan', function ($record) {
                return $record->submission_date ?  $record->submission_date->format('d/m/Y') : '-';
            })

            ->addColumn('hasil_perbaikan', function ($record) use ($user) {
                if($record->repair_results == 'SELESAI'){
                    return '<span class="badge bg-success text-white">'.ucfirst($record->repair_results).'</span>';
                }elseif($record->repair_results == 'BELUM' || $record->repair_results == null){
                    return '<span class="badge bg-warning text-white">'.'BELUM SELESAI'.'</span>'; 
                }else{
                    return '<span class="badge bg-danger text-white">'.ucfirst($record->repair_results).'</span>';
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

                if(auth()->user()->hasRole('Sarpras') && $record->status =='approved' && ($record->repair_results == 'BELUM') && auth()->user()->checkPerms('perbaikan-aset.edit')){
                    $actions[] = [
                        'type' => 'edit',
                        'label' => 'Perbarui Hasil Perbaikan',
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
            'no_surat',
            'departemen',
            'nama_aset',
            'is_disposisi',
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
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:no_surat|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:vendor|label:Vendor|className:text-center|width:250px'),
                    $this->makeColumn('name:repair_type|label:Jenis Perbaikan|className:text-center|width:250px'),
                    $this->makeColumn('name:total|label:Total Biaya |className:text-center|width:250px'),
                    $this->makeColumn('name:sper_status|label:Sperpat Status|className:text-center'),
                    $this->makeColumn('name:status|label:Status Transaksi|className:text-center'),
                    $this->makeColumn('name:updated_by'),
                    // $this->makeColumn('name:action'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.edit', compact('record','type'));
    }

    public function detailGrid(Perbaikan $record)
    {        
        $user = auth()->user();
        $records = TransPerbaikanDisposisi::with('codes')->where('perbaikan_id',$record->id)
        ->orderByRaw("CASE WHEN updated_at > created_at THEN updated_at ELSE created_at END DESC")->filters()
            ->dtGet();
    
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
                } else {
                    return '<span class="badge bg-primary text-white">'.$record->repair_type.'</span>';
                }
            })

            ->addColumn('vendor', function ($record) {
                return $record->vendors->name;
                //return $record->id;
            })

            ->addColumn('spk_start_date', function ($record) {
                return $record->spk_start_date ? Carbon::parse($record->spk_start_date)->format('Y-m-d'):'-';
            })

            ->addColumn('spk_end_date', function ($record) {
                return $record->spk_start_date ? Carbon::parse($record->spk_start_date)->format('Y-m-d'):'-';
            })

            ->addColumn('no_spk', function ($record) {
                return $record->no_spk ? $record->no_spk:'-';
            })

            ->addColumn('total', function ($record) {
                return $record->total_cost ? number_format($record->total_cost, 0, ',', ',') :'-';
            })

            ->addColumn('sper_status', function ($record) use ($user) {
                return $record->labelStatus($record->sper_status ?? 'new');
            })

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

            ->addColumn('action', function ($record) use ($user) {
                $actions=[];
                //dd($record->id);
                $data = $record->id;
                $actions[] = [
                    'type' => 'show',
                    'page' => true,
                    'label' => 'Detail',
                    'icon' => 'fa fa-plus text-info',
                    'id' => $record->id,
                    'url' => route($this->routes . '.detailShow', 13),
                    // dd($record->id)
                ];
                
                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns(['repair_type','sper_status',
            'status','updated_by','action'])
            ->make(true);
    }

    public function detailShow(TransPerbaikanDisposisi $record){
        // dd($record->id);

        // $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
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
                // $record = TransPerbaikanDisposisi::where()
                'url' => route('perbaikan.usulan-sperpat'.'.detailGrid',13),
            ],
        ]);
        $record = TransPerbaikanDisposisi::find(13)->first();
        // dd($record->id);
        $ts_cost = UsulanSperpat::where('trans_perbaikan_id',13)->sum('total_cost');
        return $this->render('perbaikan.pj-perbaikan.detail.show', compact('record','ts_cost'));
    }

    public function detail(Perbaikan $record)
    {
         //dd($record);
        $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:no_surat|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:vendor|label:Vendor|className:text-center|width:250px'),
                    $this->makeColumn('name:repair_type|label:Jenis Perbaikan|className:text-center|width:250px'),
                    $this->makeColumn('name:no_spk|label:Nomor Kontrak |className:text-center|width:250px'),
                    $this->makeColumn('name:spk_start_date|label:Tanggal Mulai Kontrak |className:text-center|width:250px'),
                    $this->makeColumn('name:spk_end_date|label:Tanggal Selesai Kontrak |className:text-center|width:250px'),
                    $this->makeColumn('name:total|label:Total Biaya |className:text-center|width:250px'),
                    $this->makeColumn('name:status'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);
        return $this->render($this->views . '.detail', compact('record'));
    }

    public function updateSummary(PerbaikanVerifyRequest $request, Perbaikan $record)
    {
        return $record->handleVerify($request);
    }

    public function update(HasilPerbaikan2Request $request, Perbaikan $record)
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
        // dd($record);
        //$module = 'perbaikan-perbaikan-aset';
        $this->module= 'perbaikan-aset';
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    public function show(Perbaikan $record)
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:no_surat|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:vendor|label:Vendor|className:text-center|width:250px'),
                    $this->makeColumn('name:repair_type|label:Jenis Perbaikan|className:text-center|width:250px'),
                    $this->makeColumn('name:total|label:Total Biaya |className:text-center|width:250px'),
                    $this->makeColumn('name:sper_status|label:Sperpat Status|className:text-center'),
                    $this->makeColumn('name:status|label:Status Transaksi|className:text-center'),
                    $this->makeColumn('name:updated_by'),
                    // $this->makeColumn('name:action'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.show', compact('record'));

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


