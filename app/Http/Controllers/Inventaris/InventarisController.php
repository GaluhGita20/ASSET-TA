<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PerencanaanRequest;
use App\Http\Requests\Pengajuan\PerencanaanDetailRequest;
use App\Http\Requests\Pengajuan\PerencanaanDisposisiRequest;
use App\Http\Requests\Transaksi\TransaksiRequest;
use App\Http\Requests\Inventaris\KibARequest;
use App\Http\Requests\Inventaris\KibBRequest;
use App\Http\Requests\Inventaris\KibCRequest;
use App\Http\Requests\Inventaris\KibDRequest;
use App\Http\Requests\Inventaris\KibERequest;
use App\Http\Requests\Inventaris\KibFRequest;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Inventaris\Aset;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Transaksi\PembelianTransaksi;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class InventarisController extends Controller
{
    protected $module ='inventaris';
    protected $routes ='inventaris.inventaris-aset';
    protected $views = 'inventaris.inventaris-aset';
    protected $perms = 'registrasi.inventaris-aset';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Iventaris Aset',
            'breadcrumb' => [
                'Home' => route('home'),
                // 'Pengajuan' => '#',
                'List Iventaris Aset' => route($this->routes . '.index'),
            ]
        ]);
    }


    public function grid(Request $request)
    {
        $user = auth()->user();
        $records = PerencanaanDetail::where('status','waiting register')->filters()->dtGet();
        return DataTables::of($records)
            ->addColumn('num', function ($detail) {
                return request()->start;
            })
            ->addColumn('ref_aset_id', function ($detail) {
                return $detail->asetd->name ? $detail->asetd->name : '';
            })
            ->addColumn('struct', function ($detail) {
                return $detail->perencanaan ? $detail->perencanaan->struct->name : '';
            })
            ->addColumn('qty_agree', function ($detail) {
                $flag= Aset::where('usulan_id',$detail->id)->count();
                if($flag > 0){
                    $sisa = abs($flag - $detail->qty_agree);
                    return $sisa;
                }else{
                    return $detail->qty_agree ?  $detail->qty_agree  : '';
                }
            })
            ->addColumn('tanggal_terima', function ($detail){
                $data = $detail->perencanaanPembelian()->where('detail_usulan_id', $detail->id)->get(['pembelian_id']);
                $pembelianIds = $data->pluck('pembelian_id')->toArray();
                $receiptDates = PembelianTransaksi::where('id', $pembelianIds)->get('receipt_date');
               return Carbon::parse($receiptDates[0]['receipt_date'])->format('Y/m/d');
            })
            ->addColumn('HPS_unit_cost', function ($detail) {
                $data = $detail->perencanaanPembelian()->where('detail_usulan_id', $detail->id)->get(['pembelian_id']);
                $pembelianIds = $data->pluck('pembelian_id')->toArray();
                $receiptDates = PembelianTransaksi::where('id', $pembelianIds)->get('unit_cost');
                return number_format($receiptDates[0]['unit_cost'], 0, ',', ',');
            })
            ->editColumn(
                'checkbox',
                function ($detail) {
                    return '<label class="checkbox" style="text-align:center; display: flex; align-items: center;">
                        <input  type="checkbox" ' . '' . ' class="usulan" name="usulan_id[' . $detail->id . ']" value="' . $detail->id . '">
                        <span></span>
                    </label>';
                }
            )
            ->rawColumns(['tahun_usulan','checkbox','action'])
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
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi|className:text-left|width:200px'),
                    // $this->makeColumn('name:kategori_aset|label:Kategor Aset|className:text-center|width:200px'),
                    $this->makeColumn('name:tanggal_terima|label:Tanggal Terima|className:text-center|width:200px'),
                    $this->makeColumn('name:qty_agree|label:Jumlah Belum Dicatat|className:text-center|width:300px'),
                    $this->makeColumn('name:HPS_unit_cost|label:Harga (Rupiah)|className:text-center|width:200px'),
                   // $this->makeColumn('name:HPS_total_agree|label:Total Harga Disetujui (Rupiah)|className:text-center|width:200px'),
                    $this->makeColumn('name:struct|label:Unit Pengusul|className:text-center|width:200px'),
                    $this->makeColumn('name:checkbox|label:check|class:usulan|className:text-center|width:50px'),
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
                        $this->makeColumn('name:kategori_aset|label:Kategor Aset|className:text-center|width:200px'),
                        $this->makeColumn('name:tanggal_terima|label:Tanggal Terima|className:text-center|width:200px'),
                        $this->makeColumn('name:qty_agree|label:Jumlah|className:text-center|width:300px'),
                        $this->makeColumn('name:HPS_unit_cost|label:Harga (Rupiah)|className:text-center|width:200px'),
                        $this->makeColumn('name:struct|label:Unit Pengusul|className:text-left|width:200px'),
                        $this->makeColumn('name:checkbox|label:check|class:usulan|className:text-center|width:50px'),
                    ],
                ],
            ]);
        
            return $this->render($this->views . '.index');
        }else{
            $record = new Aset;
            // dd($request->all());
            if( count($request->usulan_id) > 1 ){
                return $this->rollback(__('Pilih Satu Data Untuk Di Inventarisasikan'));
            }
            return $record->handleSubmitKib($request); //handle pertama kali data diambil
        }
    }


    public function create(Request $request)
    {
        // dd($request->all());
     
       
        $usulan = PerencanaanDetail::where('id',$request->usulan_id)->first();
        $trans = PembelianTransaksi::where('id',$request->trans_id)->first();
        
        $jumlah = $request->jumlah;
        if($request->customValue == 'B'){
            return $this->render($this->views.'.create-kib-b',compact('trans','usulan','jumlah'));
        }elseif($request->customValue == 'A'){
            return $this->render($this->views.'.create-kib-a',compact('trans','usulan','jumlah'));
        }elseif($request->customValue == 'C'){
            return $this->render($this->views.'.create-kib-c',compact('trans','usulan','jumlah'));
        }elseif($request->customValue == 'D'){
            return $this->render($this->views.'.create-kib-d',compact('trans','usulan','jumlah'));
        }elseif($request->customValue == 'E'){
            return $this->render($this->views.'.create-kib-e',compact('trans','usulan','jumlah'));
        }else{
            return $this->render($this->views.'.create-kib-f',compact('trans','usulan','jumlah'));
        }
    }

    public function storeDetailKibA(KibARequest $request)
    {
       // dd($request);
        $record = new Aset;
        return $record->handleStoreOrUpdateKibA($request); //handle simpan data
    }

    public function storeDetailKibB(KibBRequest $request)
    {
       // dd($request);
       
        $record = new Aset;
        return $record->handleStoreOrUpdateKibB($request); //handle simpan data
    }

    public function storeDetailKibC(KibCRequest $request)
    {
       // dd($request);
        $record = new Aset;
        return $record->handleStoreOrUpdateKibC($request); //handle simpan data
    }

    public function storeDetailKibD(KibDRequest $request)
    {
       // dd($request);
        $record = new Aset;
        return $record->handleStoreOrUpdateKibD($request); //handle simpan data
    }

    public function storeDetailKibE(KibERequest $request)
    {
       // dd($request);
        $record = new Aset;
        return $record->handleStoreOrUpdateKibE($request); //handle simpan data
    }

    public function storeDetailKibF(KibFRequest $request)
    {
       // dd($request);
        $record = new Aset;
        return $record->handleStoreOrUpdateKibC($request); //handle simpan data
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
        $pengadaan = $detail->getDetailUsulan($detail->id); ////ambil data pembelian
        if($pengadaan == null){ //jika data pembelian kosong maka masih proses create
            return $detail->handleStoreEditListPembelian(0); //perencanaan Detail
        }else{
            return $detail->handleStoreEditListPembelian($pengadaan);
        }
    }

}
