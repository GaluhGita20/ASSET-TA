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
use App\Exports\Setting\KibCExport;
use Maatwebsite\Excel\Facades\Excel;
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
                    $this->makeColumn('name:name|label:Nama Aset|className:text-left'),
                    $this->makeColumn('name:kode_akun|label:Kode Akun|className:text-center'),
                    $this->makeColumn('name:nomor_register|label:Nomor Register|className:text-center'),
                    $this->makeColumn('name:tgl_register|label:Tanggal Register|className:text-center'),
                    $this->makeColumn('name:status|label:Status|className:text-center'),
                    $this->makeColumn('name:kondisi|label:Kondisi|className:text-center'),
                    $this->makeColumn('name:luas_lantai|label:Luas Lantai (m2)|className:text-center'),
                    $this->makeColumn('name:luas_bangunan|label:Luas Bangunan (m2)|className:text-center'),
                    $this->makeColumn('name:alamat|label:Alamat|className:text-center'),
                    $this->makeColumn('name:source_acq|label:Sumber Perolehan|className:text-center'),
                    $this->makeColumn('name:asal_usul|label:Asal Usul|className:text-center'),
                    $this->makeColumn('name:status_tanah|label:Status Tanah|className:text-center'),
                    $this->makeColumn('name:nomor_dokumen|label:Nomor Sertifikat|className:text-center'),
                    $this->makeColumn('name:tgl_dokumen|label:Tanggal Sertifikat|className:text-center'),
                    $this->makeColumn('name:tanah_id|label:Kode Tanah|className:text-center'),
                    $this->makeColumn('name:nilai_beli|label:Harga Perolehan (Rupiah)|className:text-center'),
                    // $this->makeColumn('name:nilai_residu|label:Harga Perolehan (Rupiah)|className:text-center'),
                    $this->makeColumn('name:masa_manfaat|label:Masa Manfaat (Tahun)|className:text-center'),
                    $this->makeColumn('name:nilai_residu|label:Nilai Residu (Rupiah)|className:text-center'),
                    $this->makeColumn('name:akumulasi|label:Akumulasi Penyusutan (Rupiah)|className:text-center'),
                    $this->makeColumn('name:nilai_buku|label:Nilai Buku (Rupiah)|className:text-center'),
                    $this->makeColumn('name:pengusul|label:Unit Pengusul|className:text-center'),
                    $this->makeColumn('name:keterangan|label:Keterangan|className:text-center'),
                    $this->makeColumn('name:updated_by'),
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
                }
            )->addColumn(
                'tgl_register',
                function ($record) {
                return $record->book_date ? Carbon::parse($record->book_date)->formatLocalized('%d/%B/%Y') : '-';
            })->addColumn(
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
                    return $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $record->usulans->trans->receipt_date->format('Y');
                }
            )->addColumn(
                'status_tanah',
                function ($record) {
                    return $record->statusTanah->name ? $record->statusTanah->name : '-';
                }
            )->addColumn(
                'nomor_dokumen',
                function ($record) {
                    return $record->no_sertificate ? $record->no_sertificate : '-';
                }
            )->addColumn(
                'tgl_dokumen',
                function ($record) {
                    return $record->sertificate_date ? Carbon::parse($record->sertificate_date)->formatLocalized('%d/%B/%Y') : '-';
                }
            )->addColumn(
                'asal_usul',
                function ($record) {
                    return $record->usulans->danad ? $record->usulans->danad->name : '-';
                }
            )->addColumn(
                'nilai_residu',
                function ($record) {
                    return $record->residual_value ? number_format($record->residual_value, 0, ',', ',') : '-';
                }
            )->addColumn(
                'nilai_beli',
                function ($record) {
                    return $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') : number_format($record->usulans->HPS_unit_cost, 0, ',', ',') ;
                }
            )->addColumn(
                'nilai_buku',
                function ($record) {
                    return $record->book_value ? number_format($record->book_value, 0, ',', ',') : '-';
                }
            )->addColumn(
                'masa_manfaat',
                function ($record) {
                    return $record->useful ? $record->useful: '-';
                }
            )->addColumn(
                'akumulasi',
                function ($record) {
                    return $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0';
                }
            )->addColumn(
                'status',
                function ($record) {
                    if ($record->status == 'actives') {
                        return $record->status ? '<span class="badge bg-success text-white">'.ucfirst('active').'</span>' : '-';
                    } elseif ($record->status == 'maintenance') {
                        return $record->status ? '<span class="badge bg-warning text-white">'.ucfirst($record->status).'</span>' : '-';
                    }elseif ($record->status == 'notactive') {
                        return $record->status ? '<span class="badge bg-danger text-white">'.ucfirst($record->status).'</span>' : '-';
                    } elseif ($record->status == 'in repair') {
                        return $record->status ? '<span class="badge bg-warning text-white">'.ucfirst($record->status).'</span>' : '-';
                    } elseif ($record->status == 'in deletion') {
                        return $record->status ? '<span class="badge bg-warning text-white">'.ucfirst($record->status).'</span>' : '-';
                    } 
                    else {
                        return $record->status ? '<span class="badge bg-light">'.ucfirst($record->status).'</span>' : '-';
                    }
                }
            )->addColumn(
                'tanah_id',
                function ($record) {
                    return $record->tanahs->nama_akun ? $record->tanahs->kode_akun.'/'.$record->tanahs->nama_akun : '-';
                }
            )->addColumn(
                'kondisi',
                function ($record) {
                    if ($record->condition == 'baik') {
                        return $record->condition ? '<span class="badge bg-success text-white">'.ucwords($record->condition).'</span>' : '-';
                    } elseif ($record->condition == 'rusak berat') {
                        return $record->condition ? '<span class="badge bg-danger text-white">'.ucwords($record->condition).'</span>' : '-';
                    } else {
                        return $record->condition ? '<span class="badge bg-warning text-white">'.ucwords($record->condition).'</span>' : '-';
                    }
                    // return $record->condition ? ucfirst($record->condition) : '-';
                }
            )->addColumn(
                'keterangan',
                function ($record) {
                    return $record->description ? $record->description : '-';
                }
            )->addColumn(
                'source_acq',
                function ($record) {
                    if ($record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ) {
                        return $record->usulans ? '<span class="badge bg-primary text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' : '-';
                    } else {
                        return $record->usulans ? '<span class="badge bg-success text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' : '-';
                    }
                  //  return $record->usulans ? ucwords($record->usulans->trans->source_acq) : '-';
                }
            )
            ->addColumn(
                'pengusul',
                    function ($record) {
                        if(!empty($record->usulans->perencanaan->struct)){
                            return $record->usulans->perencanaan->struct->name ? $record->usulans->perencanaan->struct->name : '-';
                        }else{
                            return $record->location_hibah_aset ? $record->deps->name : '-';
                        }
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

                if($record->condition =='baik' && $record->status =='actives'){
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

                if($record->condition =='rusak berat' && $record->status =='actives'){
                    if (auth()->user()->checkPerms('penghapusan-aset.create')) {
                        $actions[] = [
                            'type' => 'edit',
                            'page' => true,
                            'label' => 'Hapus',
                            'icon' => 'fas fa-trash text-danger',
                            'id' => $record->id,
                            'url' => route($this->routes . '.deletes', $record->id),
                        ];
                    }
                }


                    return $this->makeButtonDropdown($actions);
                }
            )
            ->rawColumns(['source_acq','status','kondisi','action','name','jenis_aset','updated_by'])
            ->make(true);
    }

    public function storeDetail(TransaksiRequest $request)
    {
        $record = new PembelianTransaksi;
        return $record->handleStoreOrUpdate($request); //handle simpan data
    }


    public function create(){
        $baseContentReplace = "base-modal--render";
        return $this->render($this->views . '.create');
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
        // dd($record);
        return $this->render('perbaikan.perbaikan-aset.create',compact('record'));
    }

    public function deletes(Aset $record)
    {
        return $this->render('pengajuan.penghapusan-aset.create',compact('record'));
    }

    public function export(Request $request){
        return Excel::download(new KibCExport, date('Y-m-d') . ' KIBB.xlsx');
    }

    public function print()
    {
        $title ='Laporan Aset KIB C';
        $records = Aset::with('coad')->where('type','KIB C')->whereIn('status',['actives','in repair','in deletion','maintenance'])->filters()->get();

        return $this->render($this->views.'.cetak',compact('records','title'));
    }

}
