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

class ListPembelianController extends Controller
{
    protected $module ='transaksi_waiting-purchase';
    protected $routes ='transaksi.waiting-purchase';
    protected $views = 'transaksi.waiting-purchase';
    protected $perms = 'transaksi.waiting-purchase';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'List Pembelian Aset',
            'breadcrumb' => [
                'Home' => route('home'),
                // 'Pengajuan' => '#',
                'List Pembelian Aset' => route($this->routes . '.index'),
            ]
        ]);
    }


    public function grid(Request $request)
    {
        $user = auth()->user();
      
        if($request->data != null){
            $allIds = collect($request->data)->flatten()->toArray();
            $records = PerencanaanDetail::with('perencanaan')
                ->whereIn('id', $allIds)
                ->get();
        }else{
            $records = PerencanaanDetail::with(['perencanaan'])
                ->orderBy("ref_aset_id")->grid()
                ->dtGet();
        }
        // dd($record);
    
        return DataTables::of($records)
            ->addColumn('num', function ($detail) {
                return request()->start;
            })
            ->addColumn('struct', function ($detail) {
                return $detail->perencanaan->struct->name ? $detail->perencanaan->struct->name : '';
            })
            ->addColumn('ref_aset_id', function ($detail) {
                return $detail->asetd->name ? $detail->asetd->name : '';
            })
            ->addColumn('checkbox', function ($detail) {
                return '<input type="checkbox" class="usulan" id="'.$detail->id.'" name="usulan_id[' . $detail->id . ']" value="' . $detail->id . '">';
            })
            ->addColumn('action', function ($detail){
                $actions[] = [
                    'type' => 'delete',
                    'url' => route($this->routes . '.detailDestroy', $detail->id),
                ];
                return $this->makeButtonDropdown($actions, $detail->id);
            })
            ->rawColumns(['checkbox','action'])
            ->make(true);
    }
    
    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'url' => route($this->routes . ".grid"),
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:150px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-left|width:300px'),
                    $this->makeColumn('name:qty_agree|label:Jumlah Disetujui|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga Satuan|className:text-center|width:150px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga Disetujui|className:text-center|width:150px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-left|width:150px'),
                    $this->makeColumn('name:checkbox|label:check|class:usulan|width:150px'),
                ],
            ],
        ]);
    
        return $this->render($this->views . '.index');
    }


    public function store(Request $request) //ketika button submit transaksi pengadaan dilakukan
    {
        $record = new PerencanaanDetail;
        // Menghapus semua data di dalam sesi dengan nama 'nama_sesi'
        request()->session()->forget('usulan_id');
       // dd($r);
        return $record->handleStoreListPembelian($request); //handle pertama kali data diambil
        
    }


    public function create(Request $request)
    {
        $pagu = $request->pagu;
        $jumlah_beli= $request->jumlah_beli;
        $data = $request->id;

        $this->prepare([
            'tableStruct' => [
                'url' => route($this->routes . ".grid", compact('data')),
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
        return $this->render($this->views.'.create',compact('data','pagu','jumlah_beli'));
    }

    
    public function update($record){
   
        $data = $data1->id;
        $record = $data1->pembelian_id;

        $this->prepare([
            'tableStruct' => [
                'url' => route($this->routes . ".grid", compact('data')),
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:150px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-left|width:300px'),
                    $this->makeColumn('name:qty_agree|label:Jumlah Disetujui|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga Satuan|className:text-center|width:150px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga Disetujui|className:text-center|width:150px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-left|width:150px'),
                ],
            ],
        ]);
        return $this->render($this->views.'.edit',compact('data','data1'));
    }

    public function storeDetail(TransaksiRequest $request)
    {
        $record = new PembelianTransaksi;
        return $record->handleStoreOrUpdate($request); //handle simpan data
    }



    public function detailDestroy(PerencanaanDetail $detail)
    {
        $pengadaan = $detail->getDetailUsulan($detail->id);
        if($pengadaan == null){
          //  dd($detail);
            return $detail->handleStoreEditListPembelian(0); //perencanaan Detail
        }else{
            return $detail->handleStoreEditListPembelian($pengadaan);
        }
    }



}
