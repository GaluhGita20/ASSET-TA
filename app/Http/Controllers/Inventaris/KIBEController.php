<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Aset\AsetRequest;
use App\Models\Globals\Menu;
// use App\Models\Master\Aset\Aset;
use App\Models\inventaris\Aset;
use App\Support\Base;
use Carbon\Carbon;
use Illuminate\Http\Request;
//use Yajra\DataTables\Facades\DataTables;

class KIBEController extends Controller
{
    //
    protected $module = 'inventaris_kib-e';
    protected $routes = 'inventaris.kib-e';
    protected $views  = 'inventaris.kib-e';
    protected $perms = 'registrasi.inventaris-aset';
    //private $datas;

    public function __construct()
    {
        $this->prepare(
            [
                'module' => $this->module,
                'routes' => $this->routes,
                'views' => $this->views,
                'perms' => $this->perms,
                'permission' => $this->perms . '.view',
                'title' => 'Aset Tetap Lainya',
                'breadcrumb' => [
                    'Invantaris' => rut($this->routes . '.index'),
                    // 'Jenis Aset' => rut($this->routes . '.index'),
                    'Aset Tetap Lainya' => rut($this->routes . '.index'),
                ]
            ]
        );
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num'),
                    $this->makeColumn('name:name|label:Nama Aset|className:text-center'),
                    $this->makeColumn('name:kode_akun|label:Kode Akun|className:text-center'),
                    $this->makeColumn('name:nomor_register|label:Nomor Register|className:text-center'),
                    $this->makeColumn('name:status|label:Status|className:text-center'),
                    // $this->makeColumn('name:nama_akun|label:Nama Akun|className:text-center|width:300px'),
                    // $this->makeColumn('name:merek_tipe|label:Merek|className:text-center'),
                    // $this->makeColumn('name:ukuran_cc|label:Ukuran CC|className:text-center'),
                    $this->makeColumn('name:judul|label:Judul|className:text-center'),
                    $this->makeColumn('name:pencipta|label:Pencipta|className:text-center'),
                    $this->makeColumn('name:jenis|label:Jenis|className:text-center'),
                    $this->makeColumn('name:ukuran|label:Ukuran (M)|className:text-center'),
                    $this->makeColumn('name:bahan|label:Bahan|className:text-center'),
                    $this->makeColumn('name:tahun_beli|label:Tahun Pembelian|className:text-center'),
                    // $this->makeColumn('name:no_BPKB|label:Nomor BPKB|className:text-center'),
                    $this->makeColumn('name:asal_usul|label:Asal Usul|className:text-center'),
                    // $this->makeColumn('name:nilai_beli|label:Harga|className:text-center'),
                    $this->makeColumn('name:kondisi|label:Kondisi|className:text-center'),
                    // $this->makeColumn('name:masa_manfaat|label:Masa Manfaat|className:text-center'),
                    // $this->makeColumn('name:nilai_residu|label:Nilai Penyusutan|className:text-center'),
                    $this->makeColumn('name:nilai_beli|label:Harga|className:text-center'),
                    $this->makeColumn('name:masa_manfaat|label:Masa Manfaat|className:text-center'),
                    $this->makeColumn('name:nilai_residu|label:Nilai Residu|className:text-center'),
                    $this->makeColumn('name:akumulasi|label:Akumulasi Penyusutan|className:text-center'),
                    $this->makeColumn('name:nilai_buku|label:Nilai Buku|className:text-center'),
                    $this->makeColumn('name:unit|label:Unit|className:text-center'),
                    $this->makeColumn('name:location|label:Lokasi|className:text-center'),
                    $this->makeColumn('name:keterangan|label:Keterangan|className:text-center'),
                    $this->makeColumn('name:updated_by'),
                    // $this->makeColumn('name:created_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);
        return $this->render($this->views . '.index');
    }
    
    public function grid()
    {
        $user = auth()->user();
        $records = Aset::with('coad')->where('type','KIB E')->grid()->filters()->dtGet();

        return \DataTables::of($records)
            ->addColumn(
                'num',
                function ($record) {
                    return request()->start;
                }
            )
            ->addColumn(
                'name',
                function ($record) {
                   return $record->usulans ? $record->usulans->asetd->name : '-';
                }
            
            )->addColumn(
                'kode_akun',
                function ($record) {
                    return $record->coad ? $record->coad->kode_akun : '-';
                }
            )->addColumn(
                'nama_akun',
                function ($record) {
                    return $record->coad ? $record->coad->nama_akun : '-';
                }
            )->addColumn(
                'nomor_register',
                function ($record) {
                // $max_no = $record->where('coa_id',$record->coa_id)->max('no_register');
                // $panjang_target = max(3, ceil(log10($max_no + 1)));
                // return $record->no_register ? str_pad($record->no_register, $panjang_target, '0', STR_PAD_LEFT) : '-';
                return $record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-';
                }
            )->addColumn(
                'judul',
                function ($record) {
                    return $record->title ? $record->title : '-';
                }
            )->addColumn(
                'pencipta',
                function ($record) {
                   return $record->creators ? $record->creators : '-';
                }
            )->addColumn(
                'spesifikasi',
                function ($record) {
                    return $record->spesifikasi ? $record->spesifikasi : '-';
                }
            )->addColumn(
                'bahan',
                function ($record) {
                    return $record->material ? $record->material : '-';
                }
            )->addColumn(
                'jenis',
                function ($record) {
                    return $record->tipe_animal ? $record->tipe_animal : '-';
                }
            )->addColumn(
                'ukuran',
                function ($record) {
                    return $record->size_animal ? $record->size_animal : '-';
                }
            )->addColumn(
                'tahun_beli',
                function ($record) {
                    return $record->trans ? $record->trans->spk_start_date->format('Y') : '-';
                }
            )->addColumn(
                'asal_usul',
                function ($record) {
                    return $record->usulans ? $record->usulans->danad->name : '-';
                }
            )->addColumn(
                'nilai_residu',
                function ($record) {
                    return $record->residual_value ? number_format($record->residual_value, 0, ',', ',') : '-';
                }
            )->addColumn(
                'nilai_beli',
                function ($record) {
                    return $record->trans ? number_format($record->trans->unit_cost, 0, ',', ',') : '-';
                }
            )->addColumn(
                'nilai_buku',
                function ($record) {
                    return $record->book_value ? number_format($record->book_value, 0, ',', ',') : '-';
                }
            )->addColumn(
                'masa_manfaat',
                function ($record) {
                    return $record->useful ? $record->useful.' Tahun' : '-';
                }
            )->addColumn(
                'akumulasi',
                function ($record) {
                    return $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0';
                }
            )
            ->addColumn(
                'kondisi',
                function ($record) {
                    return $record->condition ? $record->condition : '-';
                }
            )->addColumn(
                'status',
                function ($record) {
                    return $record->status ? $record->status : '-';
                }
            )->addColumn(
                'unit',
                function ($record) {
                    return $record->usulans ? $record->usulans->perencanaan->struct->name : '-';
                }
            )->addColumn(
                'keterangan',
                function ($record) {
                    return $record->description ? $record->description : '-';
                }
            )->addColumn(
                'location',
                function ($record) {
                    return $record->locations ? $record->locations->name : '-';
                }
            )
            ->addColumn(
                'masa_manfaat',
                function ($record) {
                   return $record->useful ? $record->useful.' Tahun' : '-';
            }
            )->addColumn(
                'updated_by',
                function ($record) {
                    return $record->createsByRaw();
                }
            )
            ->addColumn('action',function ($record) use ($user) {
              //  if ($record->checkAction('show', $this->perms)) {
                    $actions[] = [
                        'type' => 'show',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes . '.show', $record->id),
                    ];
                // }
                    return $this->makeButtonDropdown($actions);
                }
            )
            ->rawColumns(['action','name','jenis_aset','updated_by'])
            ->make(true);
    }


    public function createKibB(Request $request){
        return $this->render($this->views.'.create');
    }
   
    public function show(Aset $record){
        // $type ='show';
        return $this->render($this->views . '.detailShow',compact('record'));
    }

    public function store(AsetRequest $request){
        $record = new Aset;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(Aset $record)
    {
        return $this->render($this->views.'.edit',compact('record'));
    }

    public function storeDetail(TransaksiRequest $request)
    {
        $record = new PembelianTransaksi;
        return $record->handleStoreOrUpdate($request); //handle simpan data
    }

}
