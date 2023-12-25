<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PerencanaanRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailRequest;
use App\Http\Requests\Pengajuan\PerencanaanDisposisiRequest;
use App\Http\Requests\Transaksi\TransaksiRequest;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Transaksi\PembelianTransaksi;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

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
                // 'Pengajuan' => '#',
                'Pengadaan Aset' => route($this->routes . '.index'),
            ]
        ]);
    }


    public function grid(Request $request)
    {
        $user = auth()->user();
      
        $records = PembelianTransaksi::grid()->dtGet();
        
        // dd($record);
    
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
                return $detail->no_spk ? $detail->no_spk : '';
            })
            ->addColumn('spk_start_date', function ($detail) {
                return $detail->spk_start_date ? $detail->spk_start_date : '';
            })
            ->addColumn('spk_end_date', function ($detail) {
                return $detail->spk_end_date ? $detail->spk_end_date : '';
            })
            ->addColumn('spk_range_time', function ($detail) {
                return $detail->spk_range_time ? $detail->spk_range_time .' Hari': '';
            })
            ->addColumn('jenis_pengadaan_id', function ($detail) {
                return $detail->pengadaans->name ? $detail->pengadaans->name : '';
            })
            ->addColumn('budget_limit', function ($detail) {
                return $detail->budget_limit ? $detail->budget_limit : '';
            })
            ->addColumn('qty', function ($detail) {
                return $detail->qty ? $detail->qty : '';
            })
            ->addColumn('unit_cost', function ($detail) {
                return $detail->unit_cost ? $detail->unit_cost : '';
            })
            ->addColumn('shiping_cost', function ($detail) {
                return $detail->shiping_cost ? $detail->shiping_cost : '0';
            })
            ->addColumn('tax_cost', function ($detail) {
                return $detail->tax_cost ? $detail->tax_cost : '0';
            })
            ->addColumn('total_cost', function ($detail) {
                return $detail->total_cost ? $detail->total_cost : '';
            })
            ->addColumn('status', function ($detail) {
                return $detail->labelStatus($detail->status ?? 'draf');
            })
            ->addColumn('updated_by', function ($detail) use ($user) {
                if ($detail->status === 'new') {
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
                        'label' => 'Detail Edit',
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

                // if ($record->checkAction('approval', $this->perms)) {
                //     $actions[] = [
                //         'type' => 'approval',
                //         'label' => 'Approval',
                //         'page' => true,
                //         'id' => $record->id,
                //         'url' => route($this->routes . '.approval', $record->id)
                //     ];
                // }

                // if ($record->checkAction('tracking', $this->perms)) {
                //     $actions[] = 'type:tracking';
                // }

                // if ($record->checkAction('history', $this->perms)) {
                //     $actions[] = 'type:history';
                // }

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
                    $this->makeColumn('name:trans_name|label:Nama Transaksi Aset|className:text-left|width:150px'),
                    $this->makeColumn('name:vendor_id|label:Suplier|className:text-left|width:300px'),
                    $this->makeColumn('name:no_spk|label:Nomor Kontrak|className:text-center,label-info'),
                    $this->makeColumn('name:spk_start_date|label:Tanggal Mulai Kontrak|className:text-center|width:150px'),
                    $this->makeColumn('name:spk_end_date|label:Tanggal Selesai Kontrak|className:text-center|width:150px'),
                    $this->makeColumn('name:spk_range_time|label:Lama Kontrak|className:text-center|width:150px'),
                    $this->makeColumn('name:jenis_pengadaan_id|label:Jenis Pengadaan|className:text-left|width:300px'),
                    $this->makeColumn('name:qty|label:Jumlah Pembelian|className:text-left|width:150px'),
                    $this->makeColumn('name:unit_cost|label:Harga Unit|width:150px'),
                    $this->makeColumn('name:shiping_cost|label:Biaya Pengiriman|className:text-center|width:150px'),
                    $this->makeColumn('name:tax_cost|label:Biaya Pajak|className:text-left|width:150px'),
                    $this->makeColumn('name:total_cost|label:Biaya Total|width:150px'),
                    $this->makeColumn('name:status'),
                    $this->makeColumn('name:updated_by|label:Diperbarui|className:text-left|width:150px'),
                    $this->makeColumn('name:action|label:Aksi|width:150px'),
                ],
            ],
        ]);
    
        return $this->render($this->views . '.index');
    }

    public function edit(PembelianTransaksi $record)
    {
        $records = $record->getPerencanaanPengadaan($record->id);
        $data = $records['usulan_id'];
        $this->prepare([
            'tableStruct' => [
                'url' => route('transaksi.waiting-purchase'. ".grid", compact('data')),
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:150px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-left|width:300px'),
                    $this->makeColumn('name:qty_agree|label:Jumlah Disetujui|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga Satuan|className:text-center|width:150px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga Disetujui|className:text-center|width:150px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-left|width:150px'),
                    $this->makeColumn('name:action|label:action|width:150px'),
                ],
            ],
        ]);
        return $this->render($this->views . '.edit', compact('record','data'));
    }

    public function update(TransaksiRequest $request, PembelianTransaksi $record)
    {
        return $record->handleStoreOrUpdate($request);
    }

    // public function reject(Perencanaan $record, Request $request)
    // {
    //     $request->validate(
    //         [
    //             'note'  => 'required',
    //         ]
    //     );
    //     return $record->handleReject($request);
    // }

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
                    $this->makeColumn('name:qty_agree|label:Jumlah Disetujui|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga Satuan|className:text-center|width:150px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga Disetujui|className:text-center|width:150px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-left|width:150px'),
                    $this->makeColumn('name:action|label:action|width:150px'),
                ],
            ],
        ]);
        return $this->render($this->views . '.edit', compact('record','data'));
    }


 



}
