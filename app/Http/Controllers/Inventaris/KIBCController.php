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

class KIBCController extends Controller
{
    //
    protected $module = 'inventaris_kib-c';
    protected $routes = 'inventaris.kib-c';
    protected $views  = 'inventaris.kib-c';
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
                'title' => 'Aset Gedung Bangunan',
                'breadcrumb' => [
                    'Invantaris' => rut($this->routes . '.index'),
                    // 'Jenis Aset' => rut($this->routes . '.index'),
                    'Aset Gedung Bangunan' => rut($this->routes . '.index'),
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
                    $this->makeColumn('name:kondisi|label:Kondisi|className:text-center'),
                    // $this->makeColumn('name:nama_akun|label:Nama Akun|className:text-center|width:300px'),
                    $this->makeColumn('name:luas_lantai|label:Luas Lantai (m2)|className:text-center'),
                    $this->makeColumn('name:luas_bangunan|label:Luas Bangunan (m2)|className:text-center'),
                    $this->makeColumn('name:alamat|label:Alamat|className:text-center'),
                    $this->makeColumn('name:asal_usul|label:Asal Usul|className:text-center'),
                    $this->makeColumn('name:status_tanah|label:Hak Tanah|className:text-center'),
                    $this->makeColumn('name:nomor_dokumen|label:Nomor Sertifikat|className:text-center'),
                    $this->makeColumn('name:tgl_dokumen|label:Tanggal Sertifikat|className:text-center'),
                    $this->makeColumn('name:tanah_id|label:Kode Tanah|className:text-center'),
                    $this->makeColumn('name:nilai_beli|label:Biaya Pembangunan|className:text-center'),
                    $this->makeColumn('name:masa_manfaat|label:Masa Manfaat|className:text-center'),
                    $this->makeColumn('name:nilai_residu|label:Nilai Residu|className:text-center'),
                    $this->makeColumn('name:akumulasi|label:Akumulasi Penyusutan|className:text-center'),
                    $this->makeColumn('name:nilai_buku|label:Nilai Buku|className:text-center'),
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
        $records = Aset::with('coad')->where('type','KIB C') ->grid()->filters()->dtGet();

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
                return $record->no_register ? str_pad($record->no_register, 3, '0', STR_PAD_LEFT) : '-';
                // $max_no = $record->where('coa_id',$record->coa_id)->max('no_register');
                // $panjang_target = max(3, ceil(log10($max_no + 1)));
                // return $record->no_register ? str_pad($record->no_register, $panjang_target, '0', STR_PAD_LEFT) : '-';
                }
            )->addColumn(
                'bertingkat',
                function ($record) {
                    return $record->is_graded_bld ? $record->is_graded_bld : '-';
                }
            )->addColumn(
                'berbeton',
                function ($record) {
                   return $record->is_concreate_bld ? $record->is_concreate_bld : '-';
                }
            )->addColumn(
                'luas_lantai',
                function ($record) {
                    return $record->wide ? number_format($record->wide, 0, ',', ','): '-';
                }
            )->addColumn(
                'luas_bangunan',
                function ($record) {
                    return $record->wide_bld ? number_format($record->wide_bld, 0, ',', ',')  : '-';
                }
            )->addColumn(
                'alamat',
                function ($record) {
                    return $record->address ? $record->address : '-';
                }
            )->addColumn(
                'tahun_beli',
                function ($record) {
                    return $record->trans ? $record->trans->spk_start_date->format('Y') : '-';
                }
            )->addColumn(
                'status_tanah',
                function ($record) {
                    return $record->land_status ? $record->land_status : '-';
                }
            )->addColumn(
                'nomor_dokumen',
                function ($record) {
                    return $record->no_sertificate ? $record->no_sertificate : '-';
                }
            )->addColumn(
                'tgl_dokumen',
                function ($record) {
                    return $record->sertificate_date ? date('d/m/Y', strtotime($record->sertificate_date)) : '-';
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
            )->addColumn(
                'status',
                function ($record) {
                    return $record->status ? $record->status : '-';
                }
            )->addColumn(
                'tanah_id',
                function ($record) {
                    return $record->tanah_id ? $record->tanah_id : '-';
                }
            )->addColumn(
                'kondisi',
                function ($record) {
                    return $record->condition ? $record->condition : '-';
                }
            )->addColumn(
                'keterangan',
                function ($record) {
                    return $record->description ? $record->description : '-';
                }
            )->addColumn(
                'updated_by',
                function ($record) {
                    return $record->createdByRaw();
                }
            )
            ->addColumn('action',function ($record) use ($user) {
                    $actions = [
                        'type:show|id:' . $record->id,
                        // 'type:edit|id:' . $record->id,
                    ];
                    return $this->makeButtonDropdown($actions);
                }
            )
            ->rawColumns(['action','name','jenis_aset','updated_by'])
            ->make(true);
    }


    public function create(){
        $baseContentReplace = "base-modal--render";
        return $this->render($this->views . '.create');
    }
   
    public function show(Aset $record){
        $type ='show';
        return $this->render($this->views . '.show',compact('record','type'));
    }

    public function store(AsetRequest $request){
        $record = new Aset;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(Aset $record)
    {
        return $this->render($this->views.'.edit',compact('record'));
    }


    // public function update(Aset $record, AsetRequest $request){
    //     return $record->handleStoreOrUpdate($request);
    // }

    // public function destroy(Aset $record){
    //     return $record->handleDestroy();
    // }

    // public function getDetailAset(AsetRequest $request){
    //     $id_akun = $request->id;
    //     $aset = Aset::where('id', $id)->first();
    //     return response()->json([
    //         'name' => $aset->name,
    //        /// 'jenis_pengadaan' => $aset->jenis_pengadaan,
    //         'description' => $aset->description,
    //     ]);
    // }

}
