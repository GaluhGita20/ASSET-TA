<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pemutihan\PemutihanRequest;
use App\Models\Pengajuan\Pemutihans;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PemutihanAsetController extends Controller
{
    protected $module = 'pemutihan-aset';
    protected $routes = 'pengajuan.pemutihan-aset';
    protected $views = 'pengajuan.pemutihan-aset';
    protected $perms = 'pemutihan-aset';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Pengajuan Pemutihan',
            'breadcrumb' => [
                'Home' => route('home'),
                'Pengajuan Pemutihan' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function grid()
    {
        $user = auth()->user();
        $records = Pemutihans::dtGet();

        return DataTables::of($records)
            ->addColumn('#', function ($record) {
                return request()->start;
            })
            ->addColumn('nama_aset', function ($record) {
                    return $record->asets ? $record->asets->asetData->name : '-';
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

            ->addColumn('clean_type', function ($record) {
                // if ($record->pemutihanType->name == 'Hibah' || $detail->trans->source_acq == 'Sumbangan' ) {
                //     return $detail->trans ? '<span class="badge bg-primary text-white">'.ucfirst($detail->trans->source_acq).'</span>' : '-';
                // } else {
                //     return $detail->trans ? '<span class="badge bg-success text-white">'.ucfirst($detail->trans->source_acq).'</span>' : '-';
                // }
                return $record->pemutihanType->name;
            })

            ->addColumn('pic', function ($record) {
                return $record->picd->name;
            })

            ->addColumn('qty', function ($record) {
                return $record->qty;
            })

            ->addColumn('target', function ($record) {
                return $record->target;
            })

            ->addColumn('value', function ($record) {
                return number_format($record->valued, 0, ',', ',');
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
                
                if ($record->checkAction('show', $this->perms)) {
                    $actions[] = [
                        'type' => 'show',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes.'.show', $record->id),
                    ];
                }

                if ($record->checkAction('edit', $this->perms)) {
                    $actions[] = [
                        'type' => 'edit',
                        // 'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes.'.edit', $record->id),
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

                if ($record->checkAction('delete', $this->perms)) {
                    $actions[] = [
                        'type' => 'delete',
                        'id' => $record->id,
                        'method'=>'post',
                        'url' => route($this->routes . '.destroy', $record->id),
                    ];
                }

                if ($record->checkAction('approval', $this->perms) && (auth()->user()->position->id == 36)) {
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
            'status','updated_by','action'])
            ->make(true);
    }

    public function create()
    {
        return $this->render('pengajuan.pemutihan-aset.create');
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:no_surat|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:nama_aset|label:Nama Aset|className:text-center|width:250px'),
                    $this->makeColumn('name:type_aset|label:Tipe Aset|className:text-center|width:250px'),
                    $this->makeColumn('name:clean_type|label:Jenis Pemutihan|className:text-center|width:250px'),
                    $this->makeColumn('name:target|label:Target Pemutihan|className:text-center|width:250px'),
                    $this->makeColumn('name:qty|label:Jumlah|className:text-center|width:250px'),
                    $this->makeColumn('name:pic|label:PIC|className:text-center|width:250px'),
                    $this->makeColumn('name:value|label:Pendapatan (Rupiah)|className:text-center|width:250px'),
                    $this->makeColumn('name:location|label:Lokasi Pemutihan|className:text-center|width:250px'),
                    $this->makeColumn('name:status'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);
        return $this->render($this->views . '.index');
    }

    public function history(Pemutihans $record)
    {
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    public function show(Pemutihans $record)
    {
        return $this->render($this->views . '.show', compact('record'));
    }

    public function edit(Pemutihans $record)
    {
        return $this->render($this->views . '.edit', compact('record'));
    }

    public function store(PemutihanRequest $request)
    {
        $record = new Pemutihans;
        return $record->handleStore($request);
    }

    public function updateSummary(PemutihanRequest $request, Pemutihans $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function detail(Pemutihans $record)
    {
        return $this->render($this->views . '.detail', compact('record'));
    }

    public function submit(Pemutihans $record)
    {
        $flowApproval = $record->getFlowApproval($this->module);
        return $this->render('globals.submit', compact('record', 'flowApproval'));
    }

    public function submitSave(Pemutihans $record, Request $request)
    {
        return $record->handleSubmitSave($request);
    }

    public function update(PemutihanRequest $request, Pemutihans $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(Pemutihans $record)
    {
        return $record->handleDestroy();
    }

    public function tracking(Pemutihans $record)
    {
        $module = $this->module;
        if ($record->status === 'waiting.approval.revisi') {
            $module = $module . '_upgrade';
        }
        return $this->render('globals.tracking', compact('record', 'module'));
    }

    public function approval(Pemutihans $record)
    {
        return $this->render($this->views . '.show', compact('record'));
    }

    public function reject(Pemutihans $record, Request $request)
    {
        $request->validate(
            [
                'note'  => 'required',
            ]
        );
        return $record->handleReject($request);
    }

    public function approve(Pemutihans $record, Request $request)
    {
        return $record->handleApprove($request);  
    }



}


