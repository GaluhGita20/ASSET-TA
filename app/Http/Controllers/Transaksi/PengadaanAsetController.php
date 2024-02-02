<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PerencanaanRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailRequest;
use App\Http\Requests\Pengajuan\PerencanaanDisposisiRequest;
use App\Http\Requests\Transaksi\TransaksiRequest;
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

class PengadaanAsetController extends Controller
{
    protected $module ='transaksi_pengadaan-aset';
    protected $routes ='transaksi.pengadaan-aset';
    protected $views = 'transaksi.pengadaan-aset';
    protected $perms = 'transaksi.pengadaan-aset';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Pengadaan Aset',
            'breadcrumb' => [
                'Home' => route('home'),
                'Pengadaan Aset' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function grid(Request $request)
    {
        $user = auth()->user();
      
        $records = PembelianTransaksi::grid()->where('source_acq','pembelian')->filters()->dtGet();
        
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
            ->addColumn('no_spk', function ($detail) {
                return $detail->no_spk ? $detail->no_spk.'/'.Carbon::parse($detail->spk_start_date)->format('Y-m-d').'/'.Carbon::parse($detail->spk_end_date)->format('Y-m-d') : '';
            })
            ->addColumn('spk_start_date', function ($detail) {
                return  Carbon::parse($detail->spk_start_date)->format('Y-m-d');
            })
            ->addColumn('spk_end_date', function ($detail) {
                return  Carbon::parse($detail->spk_end_date)->format('Y-m-d');
            })
            ->addColumn('spk_range_time', function ($detail) {
                return $detail->spk_range_time ? $detail->spk_range_time .' Hari': '';
            })
            ->addColumn('jenis_pengadaan_id', function ($detail) {
                return $detail->pengadaans->name ? $detail->pengadaans->name : '';
            })
            ->addColumn('budget_limit', function ($detail) {
                return number_format($detail->budget_limit, 0, ',', ',');
            })
            ->addColumn('qty', function ($detail) {
                return $detail->qty ? $detail->qty : '';
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

                if ($record->checkAction('edit', $this->perms)) {
                    $actions[] = [
                        'type' => 'edit',
                        'page' => true,
                        'label' => 'Detail',
                        'icon' => 'fa fa-plus text-info',
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

                if ($record->checkAction('approval', $this->perms)) {
                    $actions[] = [
                        'type' => 'approval',
                        'label' => 'Approval',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes . '.approval', $record->id)
                    ];
                }

                if ($record->checkAction('tracking', $this->perms)) {
                    $actions[] = 'type:tracking';
                }

                if ($record->checkAction('history', $this->perms)) {
                    $actions[] = 'type:history';
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
                'url' => route($this->routes . ".grid"),
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:trans_name|label:Transaksi Aset|className:text-left|width:200px'),
                    $this->makeColumn('name:vendor_id|label:Suplier|className:text-center|width:300px'),
                    $this->makeColumn('name:no_spk|label:Nomor SPK/Tanggal SPK|className:text-center|width:200px'),
                    $this->makeColumn('name:qty|label:Jumlah Pembelian (Unit)|className:text-center|width:100px'),
                    $this->makeColumn('name:unit_cost|label:Harga Unit (Rupiah)|width:200px'),
                    $this->makeColumn('name:shiping_cost|label:Biaya Pengiriman (Rupiah)|className:text-center|width:200px'),
                    $this->makeColumn('name:tax_cost|label:Biaya Pajak (Rupiah)|className:text-center|width:200px'),
                    $this->makeColumn('name:total_cost|label:Total (Rupiah)|className:text-center|width:200px'),
                    $this->makeColumn('name:status'),
                    $this->makeColumn('name:action|label:Aksi|width:200px'),
                ],
            ],
        ]);
    
        return $this->render($this->views . '.index');
    }

    public function edit(PembelianTransaksi $record) //edit pembelian transaksi
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
                    $this->makeColumn('name:qty_agree|label:Jumlah Disetujui (Unit)|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga Satuan (Rupiah)|className:text-center|width:150px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga Disetujui (Rupiah)|className:text-center|width:150px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-center|width:150px'),
                    $this->makeColumn('name:action|label:action|width:150px'),
                ],
            ],
        ]);
        return $this->render($this->views . '.edit', compact('record','data'));
    }

    public function show(PembelianTransaksi $record) //show data
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
                    $this->makeColumn('name:qty_agree|label:Jumlah (Unit)|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga Satuan (Rupiah)|className:text-center|width:150px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga Disetujui (Rupiah)|className:text-center|width:150px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-center|width:150px'),
                ],
            ],
        ]);
        return $this->render($this->views . '.show', compact('record','data'));
    }

    public function editUpdate(Request $request)
    {
        $record = $request->input('record');

        $data = $record->getPerencanaanPengadaan($record->id);
        $this->prepare([
            'tableStruct' => [
                'url' => route('transaksi.waiting-purchase'. ".grid", compact('data')),
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:150px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-left|width:300px'),
                    $this->makeColumn('name:qty_agree|label:Jumlah Disetujui (Unit)|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga Satuan (Rupiah)|className:text-center|width:150px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga Disetujui (Rupiah)|className:text-center|width:150px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-center|width:150px'),
                    $this->makeColumn('name:action|label:action|width:150px'),
                ],
            ],
        ]);
        return $this->render($this->views . '.edit', compact('record','data'));
    }

 
    public function store(TransaksiRequest $request)
    {
        $record = new PembelianTransaksi;
        return $record->handleStoreOrUpdate($request);
    }

    public function update(TransaksiPenerimaanRequest $request, PembelianTransaksi $record)
    {
        return $record->handleStoreOrUpdate($request);
    }


    public function approval(PembelianTransaksi $record)
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
                    $this->makeColumn('name:qty_agree|label:Jumlah (Unit)|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga Satuan (Rupiah)|className:text-center|width:150px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga Disetujui (Rupiah)|className:text-center|width:150px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-center|width:150px'),
                ],
            ],
        ]);
        return $this->render($this->views . '.show', compact('record','data'));
    }
    
    public function approve(PembelianTransaksi $record, Request $request)
    {   
        // dd($record);
        return $record->handleApprove($request);   
    }

    public function reject(PembelianTransaksi $record, Request $request)
    {
        $request->validate(
            [
                'note'  => 'required',
            ]
        );
        return $record->handleReject($request);
    }

    public function destroy(PembelianTransaksi $record)
    {
        // dd($record);
        return $record->handleDestroy();
    }

    public function history(PembelianTransaksi $record)
    {
        $this->prepare(['title' => 'History Aktivitas']);
        return $this->render('globals.history', compact('record'));
    }

    // public function revisi(PembelianTransaksi $record, Request $request)
    // {
    //     return $record->handleRevisi($request);
    // }

    public function tracking(PembelianTransaksi $record)
    {
        $module = $this->module;
        if ($record->status === 'waiting.approval.revisi') {
            $module = $module . '_upgrade';
        }
        return $this->render('globals.tracking', compact('record', 'module'));
    }

    public function print(PembelianTransaksi $record, $title = '')
    {

    }

}
