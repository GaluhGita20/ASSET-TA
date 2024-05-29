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
use App\Models\Transaksi\HibahTransaksi;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\Globals\Approval;

class LaporanPenerimaanHibahController extends Controller
{
    protected $module = 'laporan_penerimaan-hibah-aset';
    protected $routes = 'laporan.penerimaan-hibah-aset';
    protected $views = 'laporan';
    protected $perms = 'report-transaksi';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Laporan Transaksi Hibah Aset',
            'breadcrumb' => [
                'Home' => route('home'),
                'Laporan Transaksi Hibah Aset' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function grid(Request $request)
    {
        $user = auth()->user();

        $records = PembelianTransaksi::where('source_acq','<>','pembelian')->where('status','completed')->filterHibah()->dtGet();
        
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
                if($detail->status == 'completed'){
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


                    $actions[] = [
                        'type' => 'show',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes . '.show', $record->id),
                    ];
                
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
                // 'url' => route($this->routes . ".grid"),
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
        $jumlah = PembelianTransaksi::whereYear('receipt_date',date('Y'))->where('status','completed')->where('source_acq','<>','pembelian')
        ->count('id');  // Include the related asets

        $value = PembelianTransaksi::whereYear('receipt_date', date('Y'))
        ->where('trans_aset.status', 'completed')
        ->where('trans_aset.source_acq', '<>', 'pembelian')
        ->join('trans_usulan_details', 'trans_aset.id', '=', 'trans_usulan_details.trans_id')
        ->sum('trans_usulan_details.qty_agree');
    
        return $this->render($this->views . '.penerimaanHibah', compact(['jumlah','value']));
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

            ->rawColumns(['qty_agree','action_show','updated_by','created_by'])
            ->make(true);
    }


    public function show(PembelianTransaksi $record)
    {

        $this->pushBreadcrumb(['Detail' => route($this->routes . '.show', $record)]);
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
                    // $this->makeColumn('name:action_show|label:Aksi'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);
        return $this->render($this->views . '.detail.penerimaanHibah', compact('record'));
    }

    public function detailShow(PerencanaanDetail $detail)
    {
        $type ='show';
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.detail.show', compact('type','detail', 'baseContentReplace'));
    }






}

