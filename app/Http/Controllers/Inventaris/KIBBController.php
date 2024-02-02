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

class KIBBController extends Controller
{
    //
    protected $module = 'inventaris_kib-b';
    protected $routes = 'inventaris.kib-b';
    protected $views  = 'inventaris.kib-b';
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
                'title' => 'Aset Peralatan Mesin',
                'breadcrumb' => [
                    'Invantaris' => rut($this->routes . '.index'),
                    // 'Jenis Aset' => rut($this->routes . '.index'),
                    'Aset Peralatan Mesin' => rut($this->routes . '.index'),
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
                    $this->makeColumn('name:name|label:Nama Aset|className:text-left'),
                    $this->makeColumn('name:kode_akun|label:Kode Akun|className:text-center'),
                    $this->makeColumn('name:nomor_register|label:Nomor Register|className:text-center'),
                    $this->makeColumn('name:status|label:Status|className:text-center'),
                    $this->makeColumn('name:kondisi|label:Kondisi|className:text-center'),
                    // $this->makeColumn('name:nama_akun|label:Nama Akun|className:text-center|width:300px'),
                    $this->makeColumn('name:merek_tipe|label:Merek|className:text-center'),
                    $this->makeColumn('name:ukuran_cc|label:Ukuran CC|className:text-center'),
                    $this->makeColumn('name:bahan|label:Bahan|className:text-center'),
                    $this->makeColumn('name:tahun_beli|label:Tahun Pembelian|className:text-center'),
                    $this->makeColumn('name:no_pabrik|label:Nomor Pabrik|className:text-center'),
                    $this->makeColumn('name:no_rangka|label:Nomor Rangka|className:text-center'),
                    $this->makeColumn('name:no_mesin|label:Nomor Mesin|className:text-center'),
                    $this->makeColumn('name:no_polisi|label:Nomor Polisi|className:text-center'),
                    $this->makeColumn('name:no_BPKB|label:Nomor BPKB|className:text-center'),
                    $this->makeColumn('name:source_acq|label:Sumber Perolehan|className:text-center'),
                    $this->makeColumn('name:asal_usul|label:Asal Usul|className:text-center'),
                    $this->makeColumn('name:nilai_beli|label:Harga (Rupiah)|className:text-center'),
                    $this->makeColumn('name:masa_manfaat|label:Masa Manfaat (Tahun)|className:text-center'),
                    $this->makeColumn('name:nilai_residu|label:Nilai Penyusutan (Rupiah)|className:text-center'),
                    $this->makeColumn('name:akumulasi|label:Akumulasi Penyusutan (Rupiah)|className:text-center'),
                    $this->makeColumn('name:nilai_buku|label:Harga (Rupiah)|className:text-center'),
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
        $records = Aset::with('coad')->where('type','KIB B')->grid()->filters()->dtGet();

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
                }
            )->addColumn(
                'merek_tipe',
                function ($record) {
                    return $record->merek_type_item ? ucwords($record->merek_type_item) : '-';
                }
            )->addColumn(
                'masa_manfaat',
                function ($record) {
                   return $record->useful ? $record->useful : '-';
                }
            )->addColumn(
                'ukuran_cc',
                function ($record) {
                    return $record->cc_size_item ? $record->cc_size_item : '-';
                }
            )->addColumn(
                'bahan',
                function ($record) {
                    return $record->material ? ucwords($record->material) : '-';
                }
            )->addColumn(
                'source_acq',
                function ($record){
                    if ($record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ) {
                        return $record->usulans ? '<span class="badge bg-primary text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' : '-';
                    } else {
                        return $record->usulans ? '<span class="badge bg-success text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' : '-';
                    }
                    //return $record->usulans ? ucwords($record->usulans->trans->source_acq) : '-';
                }
            )->addColumn(
                'tahun_beli',
                function ($record) {
                    return $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : '-';
                }
            )->addColumn(
                'no_pabrik',
                function ($record) {
                    return $record->no_factory_item ? $record->no_factory_item: '-';
                }
            )->addColumn(
                'no_rangka',
                function ($record) {
                    return $record->no_frame ? $record->no_frame : '-';
                }
            )->addColumn(
                'no_mesin',
                function ($record) {
                    return $record->no_machine_item ? $record->no_machine_item : '-';
            })->addColumn(
                'no_polisi',
                function ($record) {
                    return $record->no_police_item ? $record->no_police_item : '-';
                }
            )->addColumn(
                'no_BPKB',
                function ($record) {
                    return $record->no_BPKB_item ? $record->no_BPKB_item : '-';
                }
            )->addColumn(
                'asal_usul',
                function ($record) {
                    return $record->usulans->danad ? $record->usulans->danad->name : '-';
                }
            )->addColumn(
                'nilai_beli',
                function ($record) {
                   return $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') : number_format($record->usulans->HPS_unit_cost, 0, ',', ',');
                }
            )->addColumn(
                'nilai_residu',
                function ($record) {
                   return $record->residual_value ? number_format($record->residual_value, 0, ',', ',')  : '-';
                }
            )
            ->addColumn(
                'nilai_buku',
                function ($record) {
                   return $record->book_value ? number_format($record->book_value, 0, ',', ',') : '-';
                }
            )
            ->addColumn(
                'akumulasi',
                function ($record) {
                   return $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0';
                }
            )
            ->addColumn(
                'kondisi',
                function ($record) {
                    if ($record->condition == 'baik') {
                        return $record->condition ? '<span class="badge bg-success text-white">'.ucfirst($record->condition).'</span>' : '-';
                    } elseif ($record->condition == 'rusak berat') {
                        return $record->condition ? '<span class="badge bg-danger text-white">'.ucfirst($record->condition).'</span>' : '-';
                    } else {
                        return $record->condition ? '<span class="badge bg-warning text-white">'.ucfirst($record->condition).'</span>' : '-';
                    }
                    // return $record->condition ? ucfirst($record->condition) : '-';
                }
            )->addColumn(
                'status',
                function ($record) {
                    if ($record->status == 'actives') {
                        return $record->status ? '<span class="badge bg-success text-white">'.ucfirst('active').'</span>' : '-';
                    } elseif ($record->status == 'notactive') {
                        return $record->status ? '<span class="badge bg-danger text-white">'.ucfirst($record->status).'</span>' : '-';
                    } else {
                        return $record->status ? '<span class="badge bg-light">'.ucfirst($record->status).'</span>' : '-';
                    }
                }
            )->addColumn(
            'unit',
                function ($record) {
                    if(!empty($record->usulans->perencanaan->struct)){
                        return $record->usulans->perencanaan->struct->name ? $record->usulans->perencanaan->struct->name : '-';
                    }else{
                        return $record->location_hibah_aset ? $record->deps->name : '-';
                    }
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
                'updated_by',
                function ($record) {
                    return $record->createdByRaw();
                }
            )
            ->addColumn('action',function ($record) use ($user) {
                $actions[] = [
                    'type' => 'show',
                    'page' => true,
                    'id' => $record->id,
                    'url' => route($this->routes . '.show', $record->id),
                ];

                if($record->condition =='baik'){
                    if (auth()->user()->checkPerms('perbaikan-aset.create')) {
                        $actions[] = [
                            'type' => 'edit',
                            'page' => true,
                            'label' => 'Perbaikan',
                            'icon' => 'fa fa-wrench text-success',
                            'id' => $record->id,
                            'url' => route($this->routes . '.repair', $record->id),
                        ];
                    }
                }

                if($record->condition =='rusak berat'){
                    if (auth()->user()->checkPerms('penghapusan-aset.create')) {
                        $actions[] = [
                            'type' => 'edit',
                            'page' => true,
                            'label' => 'Penghapusan',
                            'icon' => 'fas fa-trash text-danger',
                            'id' => $record->id,
                            'url' => route($this->routes . '.deletes', $record->id),
                        ];
                    }
                }
                return $this->makeButtonDropdown($actions);
            }
            )
            ->rawColumns(['source_acq','kondisi','status','action','name','jenis_aset','updated_by'])
            ->make(true);
    }

    public function storeDetail(TransaksiRequest $request)
    {
        $record = new PembelianTransaksi;
        return $record->handleStoreOrUpdate($request); //handle simpan data
    }


    public function createKibB(Request $request){
        return $this->render($this->views.'.create');
    }
   
    public function show(Aset $record){
        $type ='show';
        return $this->render($this->views . '.detailShow',compact('record','type'));
    }

    public function store(AsetRequest $request){
        $record = new Aset;
        return $record->handleStoreOrUpdate($request);
    }

    public function edit(Aset $record)
    {
        return $this->render($this->views.'.edit',compact('record'));
    }


    public function repair(Aset $record)
    {
        // return $this->render('pengajuan.penghapusan-aset.create',compact('record'));
        return $this->render('pengajuan.perbaikan-aset.create',compact('record'));
    }

    public function deletes(Aset $record)
    {
        return $this->render('pengajuan.penghapusan-aset.create',compact('record'));
    }

   
}
