<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Transaksi\PembelianTransaksi;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LaporanPenerimaanController extends Controller
{
    protected $module = 'laporan_penerimaan-aset';
    protected $routes = 'laporan.penerimaan-aset';
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
            'title' => 'Laporan',
            'breadcrumb' => [
                'Home' => route('home'),
            //    'Laporan' => '#',
                'Penerimaan Aset' => route($this->routes . '.index'),
            ]
        ]);
    }


    public function grid()
    {
        $user = auth()->user();
        $records = PembelianTransaksi::where('source_acq','pembelian')->filters()->dtGet();
        
        return DataTables::of($records)
            ->addColumn('num', function ($detail) {
                return request()->start;
            })
            ->addColumn('trans_name', function ($detail) {
                return $detail->trans_name ? $detail->trans_name : '-';
            })
            ->addColumn('vendor_id', function ($detail) {
                return $detail->vendors->name ? $detail->vendors->name : '-';
            })
            ->addColumn('no_spk', function ($detail) {
                return $detail->no_spk ? $detail->no_spk.'/'.Carbon::parse($detail->spk_start_date)->format('Y-m-d').'/'.Carbon::parse($detail->spk_end_date)->format('Y-m-d') : '-';
            })
            ->addColumn('spk_start_date', function ($detail) {
                return  Carbon::parse($detail->spk_start_date)->format('Y-m-d');
            })
            ->addColumn('spk_end_date', function ($detail) {
                return  Carbon::parse($detail->spk_end_date)->format('Y-m-d');
            })
            ->addColumn('spk_range_time', function ($detail) {
                return $detail->spk_range_time ? $detail->spk_range_time .' Hari': '-';
            })
            ->addColumn('jenis_pengadaan_id', function ($detail) {
                return $detail->pengadaans ? $detail->pengadaans->name : '-';
            })
            ->addColumn('budget_limit', function ($detail) {
                return number_format($detail->budget_limit, 0, ',', ',');
            })
            ->addColumn('qty', function ($detail) {
                return $detail->qty ? $detail->qty : '-';
            })
            ->addColumn('unit_cost', function ($detail) {
                return number_format($detail->unit_cost, 0, ',', ',');
            })
            ->addColumn('shiping_cost', function ($detail) {
                if($detail->shiping_cost != 0){
                    return number_format($detail->shiping_cost, 0, ',', ',');
                }else{
                    return 0;
                }
            })
            ->addColumn('tax_cost', function ($detail) {
                if($detail->tax_cost != 0){
                    return number_format($detail->tax_cost, 0, ',', ',');
                }else{
                    return 0;
                }
            })
            ->addColumn('total_cost', function ($detail) {
                return number_format($detail->total_cost, 0, ',', ',');
            })
            ->addColumn('status', function ($detail) {
                return $detail->labelStatus($detail->status ?? 'draft');
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
            'no_spk',
            'spk_start_date',
            'spk_end_date',
            'spk_range_time',
            'jenis_pengadaan_id',
            'budget_limit',
            'qty',
            'unit_cost',
            'shiping_cost',
            'tax_cost',
            'total_cost',
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
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:trans_name|label:Transaksi Aset|className:text-left|width:200px'),
                    $this->makeColumn('name:vendor_id|label:Suplier|className:text-center|width:300px'),
                    $this->makeColumn('name:no_spk|label:Nomor SPK/Tgl SPK|className:text-center|width:200px'),
                    $this->makeColumn('name:qty|label:Jumlah Pembelian|className:text-center|width:100px'),
                    $this->makeColumn('name:unit_cost|label:Harga Unit|width:200px'),
                    $this->makeColumn('name:shiping_cost|label:Biaya Pengiriman|className:text-center|width:200px'),
                    $this->makeColumn('name:tax_cost|label:Biaya Pajak|className:text-center|width:200px'),
                    $this->makeColumn('name:total_cost|label:Total|width:200px'),
                    $this->makeColumn('name:status'),
                  //  $this->makeColumn('name:updated_by|label:Diperbarui|className:text-left|width:200px'),
                    $this->makeColumn('name:action|label:Aksi|width:200px'),
                ],
            ],
        ]);
    
        return $this->render($this->views.'.penerimaan');
    }

    public function show(PembelianTransaksi $record)
    {
        $records = $record->getPerencanaanPengadaan($record->id);
        $data = $records['usulan_id'];
        $this->prepare([
            'tableStruct' => [
                'url' => route('transaksi.waiting-purchase'. ".grid", compact('data')),
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-center|width:150px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-center|width:300px'),
                    $this->makeColumn('name:qty_agree|label:Jumlah|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga Satuan|className:text-center|width:150px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga Disetujui|className:text-center|width:150px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-center|width:150px'),
                ],
            ],
        ]);
        return $this->render($this->views . '.show', compact('record','data'));
    }
    
}