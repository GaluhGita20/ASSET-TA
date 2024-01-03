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

class KIBAController extends Controller
{
    //
    protected $module = 'inventaris_kib-a';
    protected $routes = 'inventaris.kib-a';
    protected $views  = 'inventaris.kib-a';
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
                'title' => 'Aset Tanah',
                'breadcrumb' => [
                    'Invantaris' => rut($this->routes . '.index'),
                    // 'Jenis Aset' => rut($this->routes . '.index'),
                    'Aset Tanah' => rut($this->routes . '.index'),
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
                    $this->makeColumn('name:name|label:Nama Aset|className:text-center|width:400px'),
                    $this->makeColumn('name:status|label:Status|className:text-center'),
                    $this->makeColumn('name:kode_akun|label:Kode Akun|className:text-center'),
                    // $this->makeColumn('name:nama_akun|label:Nama Akun|className:text-center|width:300px'),
                    $this->makeColumn('name:nomor_register|label:Nomor Register|className:text-center'),
                    $this->makeColumn('name:luas_tanah|label:Luas (m2)|className:text-center'),
                    $this->makeColumn('name:provinsi|label:Provinsi|className:text-center'),
                    $this->makeColumn('name:kota|label:Kota|className:text-center'),
                    $this->makeColumn('name:daerah|label:Daerah|className:text-center'),
                    $this->makeColumn('name:alamat|label:Alamat|className:text-center'),
                    $this->makeColumn('name:asal_usul|label:Asal Usul|className:text-center'),
                    $this->makeColumn('name:hak_tanah|label:Hak Tanah|className:text-center'),
                    $this->makeColumn('name:nomor_sertifikat|label:Nomor Sertifikat|className:text-center'),
                    $this->makeColumn('name:tgl_sertifikat|label:Tanggal Sertifikat|className:text-center'),
                    $this->makeColumn('name:kegunaan_tanah|label:Kegunaan Tanah|className:text-center'),
                    $this->makeColumn('name:nilai_beli|label:Harga|className:text-center'),
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
        $records = Aset::with('coad')->where('type','KIB A')->grid()->filters()->dtGet();

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
                'luas_tanah',
                function ($record) {
                    return $record->wide ? number_format($record->wide, 0, ',', ',') : '-';
                }
            )->addColumn(
                'provinsi',
                function ($record) {
                   return $record->province_id ? $record->province->name : '-';
                }
            )->addColumn(
                'kota',
                function ($record) {
                    return $record->city_id ? $record->city->name : '-';
                }
            )->addColumn(
                'daerah',
                function ($record) {
                    return $record->district_id ? $record->district->name : '-';
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
                'hak_tanah',
                function ($record) {
                    return $record->land_rights ? $record->land_rights : '-';
                }
            )->addColumn(
                'kegunaan_tanah',
                function ($record) {
                    return $record->land_use ? $record->land_use : '-';
                }
            )->addColumn(
                'nomor_sertifikat',
                function ($record) {
                    return $record->no_sertificate? $record->no_sertificate : '-';
                }
            )->addColumn(
                'tgl_sertifikat',
                function ($record) {
                    return $record->sertificate_date ? date('d/m/Y', strtotime($record->sertificate_date)) : '-';
                }
            )->addColumn(
                'asal_usul',
                function ($record) {
                    return $record->usulans ? $record->usulans->danad->name : '-';
                }
            )->addColumn(
                'nilai_beli',
                function ($record) {
                   return $record->trans ? number_format($record->trans->unit_cost, 0, ',', ',') : '-';
                }
            )->addColumn(
                'status',
                function ($record) {
                    return $record->status ? $record->status : '-';
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
