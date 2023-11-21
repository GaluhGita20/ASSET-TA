<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PembelianRequest;
use App\Http\Requests\Pengajuan\PembelianDetailRequest;
use App\Models\Pengajuan\Pembelian;
use App\Models\Pengajuan\PembelianDetail;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PembelianAsetController extends Controller
{
    protected $module = 'pengajuan_pembelian-aset';
    protected $routes = 'pengajuan.pembelian-aset';
    protected $views = 'pengajuan.pembelian-aset';
    protected $perms = 'pengajuan.pembelian-aset';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Pengajuan Pembelian',
            'breadcrumb' => [
                'Home' => route('home'),
                'Pengajuan' => '#',
                'Pengajuan Pembelian' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:pengajuan|label:Pengajuan Pembelian|className:text-center'),
                    $this->makeColumn('name:struct|label:Unit Kerja|className:text-center'),
                    $this->makeColumn('name:perihal|label:Perihal|className:text-center|width:200px'),
                    $this->makeColumn('name:version|label:Revisi|className:text-center'),
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
        $records = Pembelian::with('struct')
            ->grid()
            ->filters()
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
            ->addColumn('pengajuan', function ($record) {
                if ($record->code) {
                    return $record->code . "<br>" . $record->date->translatedFormat('d/m/Y');
                }
                return '';
            })
            ->addColumn('perihal', function ($record) {
                return $record->regarding;
            })
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
                        'url' => route($this->routes . '.destroy', $record->id),
                    ];
                }
                if ($record->checkAction('approval', $this->perms)) {
                    $actions[] = 'type:approval|page:true|label:Approval';
                }
                if ($record->checkAction('tracking', $this->perms)) {
                    $actions[] = 'type:tracking';
                }
                if ($record->checkAction('history', $this->perms)) {
                    $actions[] = 'type:history';
                }

                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns(['pengajuan', 'jenis_pembayaran', 'kategori', 'action', 'updated_by', 'status', 'pengajuan', 'perihal', 'version'])
            ->make(true);
    }

    public function create()
    {
        return $this->render($this->views . '.create');
    }

    public function store(PembelianRequest $request)
    {
        $record = new Pembelian;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(Pembelian $record)
    {
        return $this->render($this->views . '.edit', compact('record'));
    }

    public function detailGrid(Pembelian $record)
    {
        // dd(233, json_decode($record));
        $user = auth()->user();
        $records = PembelianDetail::with(['pembelian'])
            ->whereHas(
                'pembelian',
                function ($q) use ($record) {
                    $q->where('id', $record->id);
                }
            )->orderByRaw("CASE WHEN updated_at > created_at THEN updated_at ELSE created_at END DESC")
            ->dtGet();

        return DataTables::of($records)
            ->addColumn(
                'num',
                function ($detail) {
                    return request()->start;
                }
            )
            ->addColumn(
                'coa',
                function ($detail) {
                    return $detail->coa->nama_akun;
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
            ->rawColumns(['action', 'action_show', 'updated_by'])
            ->make(true);
    }


    public function detail(Pembelian $record)
    {
        $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:coa|label:Nama Akun|className:text-left|width:500px'),
                    $this->makeColumn('name:requirement_standard|label:Standar Kebutuhan|className:text-center'),
                    $this->makeColumn('name:existing_amount|label:Jumlah yang Ada|className:text-center'),
                    $this->makeColumn('name:qty_req|label:Jumlah Pengajuan|className:text-center'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.detail', compact('record'));
    }

    public function detailCreate(Pembelian $record)
    {
        $baseContentReplace = 'base-modal--render';

        return $this->render($this->views . '.detail.create', compact('record', 'baseContentReplace'));
    }

    public function detailStore(PembelianDetailRequest $request, Pembelian $record)
    {
        $detail = new PembelianDetail;
        return $record->handleDetailStoreOrUpdate($request, $detail);
    }

    public function detailEdit(PembelianDetail $detail)
    {
        $record = $detail->pj;
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.detail.edit', compact('record', 'detail', 'baseContentReplace'));
    }

    public function detailUpdate(PembelianDetailRequest $request, PembelianDetail $detail)
    {
        $record = $detail->pj;
        return $record->handleDetailStoreOrUpdate($request, $detail);
    }

    public function detailShow(PembelianDetail $detail)
    {
        $record = $detail->pj;
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.detail.show', compact('record', 'detail', 'baseContentReplace'));
    }

    public function detailDestroy(PembelianDetail $detail)
    {
        $record = $detail->pj;
        return $record->handleDetailDestroy($detail);
    }

    public function update(PembelianRequest $request, Pembelian $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function updateSummary(PembelianDetailRequest $request, Pembelian $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    public function destroy(Pembelian $record)
    {
        return $record->handleDestroy();
    }

    public function submit(Pembelian $record)
    {
        $flowApproval = $record->getFlowApproval($this->module);
        return $this->render('globals.submit', compact('record', 'flowApproval'));
    }

    public function submitSave(Pembelian $record, Request $request)
    {
        return $record->handleSubmitSave($request);
    }

    public function approval(Pembelian $record)
    {
        return $this->render($this->views . '.approval', compact('record'));
    }

    public function approve(Pembelian $record, Request $request)
    {
        return $record->handleApprove($request);
    }

    public function reject(Pembelian $record, Request $request)
    {
        $request->validate(
            [
                'note'  => 'required',
            ]
        );
        return $record->handleReject($request);
    }

    public function history(Pembelian $record)
    {
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    public function show(Pembelian $record)
    {
        return $this->render($this->views . '.show', compact('record'));
    }

    public function revisi(Pembelian $record, Request $request)
    {
        return $record->handleRevisi($request);
    }

    public function tracking(Pembelian $record)
    {
        $module = $this->module;
        if ($record->status === 'waiting.approval.revisi') {
            $module = $module . '_upgrade';
        }
        return $this->render('globals.tracking', compact('record', 'module'));
    }

    public function print(Pembelian $record, $title = '')
    {

    }
}
