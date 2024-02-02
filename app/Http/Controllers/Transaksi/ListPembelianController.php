<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PerencanaanRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailRequest;
use App\Http\Requests\Pengajuan\PerencanaanDisposisiRequest;
use App\Http\Requests\Transaksi\TransaksiRequest;
use App\Http\Requests\Transaksi\UsulanNonPembelianRequest;
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
    protected $perms = 'transaksi.pengadaan-aset';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Transaksi Aset',
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
      
        if($request->data != null){ //data yang sudah di checklist
            $allIds = collect($request->data)->flatten()->toArray();
            $records = PerencanaanDetail::with('perencanaan')
            ->whereIn('id', $allIds)
            ->get();
        }else{
            $records = PerencanaanDetail::where(function ($query) {
                $query->whereHas('perencanaan', function ($q) {
                    $q->whereYear('procurement_year', now()->year)->where('status', 'completed');
                });
            })->whereHas('asetd', function ($q) {
                $q->orderBy('id', 'asc');
            })->where(function ($query) {
                $query->where('qty_agree', '>', 0)->where('status', 'waiting purchase');
            })->filters()->dtGet();
            
        }
 
        return DataTables::of($records)
            ->addColumn('num', function ($detail) {
                return request()->start;
            })
            ->addColumn('struct', function ($detail) {
                return $detail->perencanaan ? $detail->perencanaan->struct->name : '-';
            })->addColumn('tahun_usulan', function ($detail) {
                return $detail->perencanaan? $detail->perencanaan->procurement_year : '-';
            })
            ->addColumn('ref_aset_id', function ($detail) {
                return $detail->asetd->name ? $detail->asetd->name : '';
            })
            ->addColumn('jenis_usulan', function ($detail) {
                if($detail->is_purchase == 'yes'){
                    return 'Pembelian';
                }else{
                    return 'Non Pembelian';
                }
            })
            ->editColumn('qty_agree', function ($detail) {
                if ($detail->qty_agree > 0) {
                   // return '<span data-short="Completed" class="label label-success label-inline text-nowrap " style="">Completed</span>';
                    return `<label><span class="label label-success">` . $detail->qty_agree . `</span><label>`;
                } else {
                    return `<span class="label label-danger">0</span>`;
                }
            })
            ->addColumn('HPS_unit_cost', function ($detail) {
               // return $detail->HPS_unit_cost ? $detail->HPS_unit_cost : '';
                return number_format($detail->HPS_unit_cost, 0, ',', ',');
            })
            ->addColumn('HPS_total_agree', function ($detail) {
                //return $detail->HPS_total_cost ? $detail->HPS_total_cost : '';
                return number_format($detail->HPS_total_agree, 0, ',', ',');
            })
            ->editColumn(
                'checkbox',
                function ($detail) {
                    return '<label class="checkbox" style="text-align:center; display: flex; align-items: center;">
                        <input  type="checkbox" ' . '' . ' class="usulan" name="usulan_id[' . $detail->id . ']" value="' . $detail->id . '">
                        <span></span>
                    </label>';
                }
            ) ->addColumn('action', function ($detail){
                
                $actions[] = [
                    'type' => 'delete',
                    'url' => route($this->routes . '.detailDestroy', $detail->id),
                ];
                return $this->makeButtonDropdown($actions, $detail->id);
                
            })
            ->addColumn('actionDetail', function ($detail){
                $perms = 'transaksi.pengadaan-aset';
                if (auth()->user()->checkPerms($perms.'.create')){
                    if($detail->is_purchase =='no' && $detail->status =='waiting purchase'){
                        $actions = [];
                        // dd($detail->id);
                        $actions[] = [
                            'type' => 'show',
                            'url' => route($this->routes . '.showDetail', $detail->id),
                        ];

                        $actions[] = [
                            'type' => 'edit',
                            'url' => route($this->routes . '.editDetail', $detail->id),
                        ];
        
                        $actions[] = [
                            'type' => 'delete',
                            'url' => route($this->routes . '.destroyDetail', $detail->id),
                        ];
                    return $this->makeButtonDropdown($actions, $detail->id);
                    }
                }
            })
            ->rawColumns(['tahun_usulan','checkbox','action','actionDetail'])
            ->make(true);
    }
    
    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'url' => route($this->routes . ".grid"),
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:200px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-left|width:200px'),
                    $this->makeColumn('name:tahun_usulan|label:Tahun Usulan|className:text-center|width:200px'),
                    $this->makeColumn('name:qty_agree|label:Jumlah (Unit)|className:text-center|width:300px'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga (Rupiah)|className:text-center|width:200px'),
                    $this->makeColumn('name:jenis_usulan|label:Jenis Usulan (Unit)|className:text-center|width:200px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga (Rupiah)|className:text-center|width:200px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-center|width:200px'),
                    $this->makeColumn('name:checkbox|label:check|className:text-center|width:50px'),
                    // $this->makeColumn('name:actionDetail|label:action|className:text-center|width:50px'),
                ],
            ],
        ]);
    
        return $this->render($this->views . '.index');
    }


    public function store(Request $request) //ketika button submit transaksi pengadaan dilakukan
    {
        
        if($request->is_submit == null){
            $this->prepare([
                'tableStruct' => [
                    'url' => route($this->routes . ".grid"),
                    'datatable_1' => [
                        $this->makeColumn('name:num|label:#'),
                        $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:200px'),
                        $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-left|width:200px'),
                        $this->makeColumn('name:tahun_usulan|label:Tahun Usulan|className:text-center|width:200px'),
                        $this->makeColumn('name:qty_agree|label:Jumlah|className:text-center|width:300px'),
                        $this->makeColumn('name:HPS_unit_cost|label:Standar Harga|className:text-center|width:200px'),
                        $this->makeColumn('name:HPS_total_agree|label:Total Harga|className:text-center|width:200px'),
                        $this->makeColumn('name:struct|label:Unit Pengusul|className:text-center|width:200px'),
                        $this->makeColumn('name:checkbox|label:check|class:usulan|className:text-center|width:50px'),
                    ],
                ],
            ]);
            return $this->render($this->views . '.index');
        }else{{ 
            //dd($request->all());
            $record = new PerencanaanDetail;
            // Menghapus semua data di dalam sesi dengan nama 'nama_sesi'
            request()->session()->forget('usulan_id');
    
            return $record->handleStoreListPembelian($request); //handle pertama kali data diambil
         }}
    }


    public function create(Request $request)
    {
        if($request->pagu == null){
            $baseContentReplace = "base-modal--render";
            return $this->render($this->views . '.create-lainya');
        }
        $pagu = $request->pagu;
        $jumlah_beli= $request->jumlah_beli;
        $data = $request->id;

        $this->prepare([
            'tableStruct' => [
                'url' => route($this->routes . ".grid", compact('data')),
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:200px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi Aset|className:text-left|width:300px'),
                    $this->makeColumn('name:tahun_usulan|label:Tahun Usulan|className:text-center|width:300px'),
                    $this->makeColumn('name:qty_agree|label:Jumlah|className:text-center|width:300px'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga |className:text-center|width:200px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga|className:text-center|width:200px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-center|width:200px'),
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
                    $this->makeColumn('name:qty_agree|label:Jumlah|className:text-center,label-info'),
                    $this->makeColumn('name:HPS_unit_cost|label:Standar Harga|className:text-center|width:150px'),
                    $this->makeColumn('name:HPS_total_agree|label:Total Harga|className:text-center|width:150px'),
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
        $pengadaan = $detail->getDetailUsulan($detail->id); ////ambil data pembelian
        if($pengadaan == null){ //jika data pembelian kosong maka masih proses create
            return $detail->handleStoreEditListPembelian(0); //perencanaan Detail
        }else{
            return $detail->handleStoreEditListPembelian($pengadaan);
        }
    }









    /// store data detail usulan non pembelian

    public function detailStore(UsulanNonPembelianRequest $request)
    {
        // dd($request);
        $detail = new PerencanaanDetail;
        return $detail->handleStoreNewData($request);
    }

    public function showDetail(PerencanaanDetail $detail)
    {
        ///$record = $detail->perencanaan;
        $type='show';
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.edit-lainya', compact('detail','baseContentReplace','type'));
    }


    public function editDetail(PerencanaanDetail $detail)
    {
        ///$record = $detail->perencanaan;
        $type='edit';
        $baseContentReplace = 'base-modal--render';
        return $this->render($this->views . '.edit-lainya', compact('detail','baseContentReplace','type'));
    }

    public function detailUpdate(UsulanNonPembelianRequest $request, PerencanaanDetail $detail)
    {
        return $detail->handleStoreNewData($request);
    }

    public function destroyDetail(PerencanaanDetail $detail)
    {
        // dd($detail);
        $pengadaan = $detail->handleDestroy();
    }


    // public function store(TransaksiRequest $request)
    // {
    //     $record = new PembelianTransaksi;
    //     return $record->handleStoreOrUpdate($request); //handle simpan data
    // }

}


            // ->whereHas('perencanaan', function ($query) {
            //     $query->where('status', 'completed');
            //     $query->orderBy('procurement_year', 'asc');
            // })->whereHas('asetd', function ($query) {
            //     $query->orderBy('name', 'asc');
            // })
            //     ->orderBy("trans_usulan.procurement_year") // Urutkan berdasarkan tahun pengadaan
              //   ->orderBy("asetd.ref_aset_id")  
                 // ->orderBy("ref_aset_id")