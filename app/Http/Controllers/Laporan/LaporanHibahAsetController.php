<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PerencanaanRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailRequest;
use App\Http\Requests\Pengajuan\PerencanaanDisposisiRequest;
use App\Http\Requests\Transaksi\TransaksiRequest;
use App\Http\Requests\Transaksi\HibahAsetRequest;
use App\Http\Requests\Transaksi\HibahDetailRequest;
use App\Http\Requests\Transaksi\TransaksiPenerimaanRequest;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Transaksi\PembelianTransaksi;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\Globals\Approval;

class LaporanHibahAsetController extends Controller
{
    protected $module = 'laporan_hibah-aset';
    protected $routes = 'laporan.hibah-aset';
    protected $views = 'laporan';
    protected $perms = 'perencanaan-aset';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Transaksi Hibah Aset',
            'breadcrumb' => [
                'Home' => route('home'),
                'Transaksi Hibah Aset' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function grid(Request $request)
    {
        $user = auth()->user();

        $records = PembelianTransaksi::where('status','completed')->where('source_acq','<>','pembelian')->filters()->dtGet();
        
        return DataTables::of($records)
            ->addColumn('num', function ($detail) {
                return request()->start;
            })
            ->addColumn('trans_name', function ($detail) {
                return $detail->trans_name ? $detail->trans_name : '';
            })
            ->addColumn('vendor_id', function ($detail) {
                return $detail->vendors->name ? $detail->vendors->name : '';
            })
            ->addColumn('jenis_penerimaan', function ($detail) {
                return $detail->source_acq ? $detail->source_acq : '';
            })
            ->addColumn('tanggal_penerimaan', function ($detail) {
                return Carbon::parse($detail->receipt_date)->format('Y/m/d');
            })

            ->addColumn('status', function ($detail) {
                if($detail->status == 'Approved'){
                    return '<span class="badge bg-success text-white">Verified</span>';
                }elseif($detail->status == 'waiting.approval'){
                    return '<span class="badge bg-primary text-white">Waiting Verify</span>';
                }else{
                    return $detail->labelStatus($detail->status ?? 'draft');
                }
            })
            ->addColumn('updated_by', function ($detail) use ($user) {
                if ($detail->status === 'draf') {
                    return "";
                } else {
                    return $detail->createdByRaw();
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

                return $this->makeButtonDropdown($actions, $record->id);

            })
            ->rawColumns([
            'trans_name',
            'vendor_id',
            'tanggal_penerimaan',
            'jenis_penerimaan',
            'status',
            'updated_by',
            'action',
            ])->make(true);
    }
    
    public function index()
    {
        // $data = null;
        $this->prepare([
            'tableStruct' => [
                'url' => route($this->routes . ".grid"),
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:trans_name|label:Nama Transaksi|className:text-left|width:200px'),
                    $this->makeColumn('name:vendor_id|label:Suplier|className:text-center|width:300px'),
                    $this->makeColumn('name:tanggal_penerimaan|label:Tanggal Penerimaan|className:text-center|width:200px'),
                    $this->makeColumn('name:jenis_penerimaan|label:Jenis Penerimaan|className:text-center|width:250px'),
                    $this->makeColumn('name:status'),
                  //  $this->makeColumn('name:updated_by|label:Diperbarui|className:text-left|width:200px'),
                    $this->makeColumn('name:action|label:Aksi|width:200px'),
                ],
            ],
        ]);
    
        return $this->render($this->views . '.hibah');
    }

    public function detailGrid(PembelianTransaksi $record)
    {        
        $user = auth()->user();
        $records = PerencanaanDetail::with(['trans'])
            ->whereHas(
                'trans',
                function ($q) use ($record) {
                    $q->where('trans_id', $record->id);
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
                'ref_aset_id',
                function ($detail) {
                    return $detail->asetd->name ? $detail->asetd->name : '';
                }
            )
            ->addColumn(
                'HPS_unit_cost',
                function ($detail) {
                    return number_format($detail->HPS_unit_cost, 0, ',', ',');
                }
            )
            ->addColumn(
                'qty_agree',
                function ($detail) {
                    return $detail->labelStatus(number_format($detail->qty_agree, 0, ',', ',') ?? '0');
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

                    if (auth()->user()->checkPerms($this->perms.'.create')) {
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
            ->rawColumns(['qty_agree','action', 'action_show','updated_by','created_by'])
            ->make(true);
    }


    public function detail(PembelianTransaksi $record)
    {
        // dd($record->id);
        $this->pushBreadcrumb(['Detail' => route($this->routes . '.detail', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:200px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi|className:text-left|width:300px'),
                    $this->makeColumn('name:HPS_unit_cost|label:Harga Unit Aset (Rupiah)|className:text-center|width:250px'),
                    $this->makeColumn('name:qty_agree|label:Jumlah Diterima (unit)|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|width:300px'),
                    // $this->makeColumn('name:created_by|width:100px'),
                    $this->makeColumn('name:action|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.detail', compact('record'));
    }

    public function detailShow(PerencanaanDetail $detail)
    {
        $type ='show';
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.detail.show', compact('type','detail', 'baseContentReplace'));
    }

    

    public function show(PembelianTransaksi $record)
    {
        $this->pushBreadcrumb(['Lihat' => route($this->routes . '.show', $record)]);
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:500px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-center'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga|className:text-center|width:500px'),
                    $this->makeColumn('name:qty_agree|label:Jumlah Diterima|className:text-center,label-info'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action_show|label:Aksi'),
                ],
                'url' => route('transaksi.non-pengadaan-aset.detailGrid', $record->id),
                // ambil data dari detail grid
            ],
        ]);
        return $this->render('transaksi.non-pengadaan-aset.show', compact('record'));
        //ambil data dari show => detail
    }


}

