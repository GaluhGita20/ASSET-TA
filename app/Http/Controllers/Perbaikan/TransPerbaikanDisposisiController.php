<?php

namespace App\Http\Controllers\Perbaikan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Perbaikan\TransPerbaikanDisposisiRequest;
use App\Http\Requests\Perbaikan\UsulanSperpatRequest;
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

class TransPerbaikanDisposisiController extends Controller
{
    protected $module = 'trans-sperpat';
    protected $routes = 'perbaikan.trans-sperpat';
    protected $views = 'perbaikan.trans-sperpat';
    protected $perms = 'trans-sperpat';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Transaksi Sperpat',
            'breadcrumb' => [
                'Home' => route('home'),
                'Transaksi Sperpat' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function grid()
    {
        $user = auth()->user();
        // if($record == null){
        $records = TransPerbaikanDisposisi::where('sper_status','completed')->grid()->filters()->dtGet();
        // }else{
        //     $records = TransPerbaikanDisposisi::where('perbaikan_id',$record->id)->grid()->filters()->dtGet();
        // }

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
            })

            ->addColumn('spk_start_date', function ($record) {
                return $record->spk_start_date ? Carbon::parse($record->spk_start_date)->format('Y/m/d'):'-';
            })

            ->addColumn('spk_end_date', function ($record) {
                return $record->spk_start_date ? Carbon::parse($record->spk_start_date)->format('Y/m/d'):'-';
            })

            ->addColumn('no_spk', function ($record) {
                return $record->no_spk ? $record->no_spk:'-';
            })

            ->addColumn('total', function ($record) {
                return $record->total_cost ? number_format($record->total_cost, 0, ',', ',') :'-';
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
                if($record->status === 'completed' || $record->status === 'waiting.approval' || $record->status === 'rejected' || $record->status === 'draft' || $record->status === 'new'){
                    if ($record->checkAction('show', $this->perms)) {
                        $actions[] = [
                            'type' => 'show',
                            'page' => true,
                            'id' => $record->id,
                            'url' => route($this->routes.'.show', $record->id),
                        ];
                    }
                }

                if($record->status === 'new' || $record->status === 'draft' || $record->status === 'rejected'){
                    if($user->hasRole('Sarpras')){
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

                if($record->status != 'draft'){
                    if ($record->checkAction('history', $this->perms)) {
                        $actions[] = 'type:history';
                    }
                }

                
                if($record->status === 'waiting.approval'){
                    if ($record->checkAction('tracking', $this->perms)) {
                        $actions[] = 'type:tracking';
                    }
                    

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
                
                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns(['repair_type',
            'status','updated_by','action'])
            ->make(true);
    }

    public function index()
    {
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
        return $this->render($this->views . '.index');
    }

    public function history(TransPerbaikanDisposisi $record)
    {
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    public function show(TransPerbaikanDisposisi $record)
    {
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
        $ts_cost = UsulanSperpat::where('trans_perbaikan_id',$record->id)->sum('total_cost');
        return $this->render($this->views . '.show', compact('record','ts_cost'));
    }

    public function store(TransPerbaikanDisposisiRequest $request)
    {
        $record = new TransPerbaikanDisposisi;
        return $record->handleStore($request);
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

                    $actions[] = [
                        'type' => 'show',
                        'url' => route($this->routes . '.detailShow', $detail->id),
                    ];
                    
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
        $ts_cost = UsulanSperpat::where('trans_perbaikan_id',$record->id)->sum('total_cost');
        return $this->render($this->views . '.detail', compact('record','ts_cost'));
    }

    public function submit(TransPerbaikanDisposisi $record)
    {

        $flowApproval = $record->getFlowApproval($this->module);
        return $this->render('globals.submit', compact('record', 'flowApproval'));
    }

    public function submitSave(TransPerbaikanDisposisi $record, Request $request)
    {
        return $record->handleSubmitSaveTrans($request);
    }

    public function update(TransPerbaikanDisposisiRequest $request, TransPerbaikanDisposisi $record)
    {
        // dd($request->all());
        return $record->handleTransStoreOrUpdate($request);
    }

    public function tracking(TransPerbaikanDisposisi $record)
    {
        $module = $this->module;
        if ($record->status === 'waiting.approval.revisi') {
            $module = $module . '_upgrade';
        }
        return $this->render('globals.tracking', compact('record', 'module'));
    }

    public function approval(TransPerbaikanDisposisi $record)
    {
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
        $ts_cost = UsulanSperpat::where('trans_perbaikan_id',$record->id)->sum('total_cost');
        return $this->render($this->views . '.show', compact('record','ts_cost'));
    }

    public function reject(TransPerbaikanDisposisi $record, Request $request)
    {
        $request->validate(
            [
                'note'  => 'required',
            ]
        );
        return $record->handleRejectTrans($request);
    }

    public function approve(TransPerbaikanDisposisi $record, Request $request)
    {
        return $record->handleApproveTrans($request);  
    }

    public function detailShow(UsulanSperpat $detail)
    {
        $type ='show';
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.detail.show', compact('type','detail', 'baseContentReplace'));
    }


}


