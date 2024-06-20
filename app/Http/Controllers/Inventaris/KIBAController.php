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
use App\Exports\Setting\KibAExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
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
                    $this->makeColumn('name:name|label:Nama Aset|className:text-left|width:400px'),
                    $this->makeColumn('name:status|label:Status|className:text-center'),
                    $this->makeColumn('name:kode_akun|label:Kode Akun|className:text-center'),

                    // $this->makeColumn('name:nama_akun|label:Nama Akun|className:text-center|width:300px'),
                    $this->makeColumn('name:nomor_register|label:Nomor Register|className:text-center'),
                    $this->makeColumn('name:tgl_register|label:Tanggal Register|className:text-center'),
                    $this->makeColumn('name:luas_tanah|label:Luas (m2)|className:text-center'),
                    $this->makeColumn('name:provinsi|label:Provinsi|className:text-center'),
                    $this->makeColumn('name:kota|label:Kota|className:text-center'),
                    $this->makeColumn('name:daerah|label:Daerah|className:text-center'),
                    $this->makeColumn('name:alamat|label:Alamat|className:text-center'),
                    $this->makeColumn('name:source_acq|label:Sumber Perolehan|className:text-center'),
                    $this->makeColumn('name:asal_usul|label:Asal Usul|className:text-center'),
                    $this->makeColumn('name:hak_tanah|label:Hak Tanah|className:text-center'),
                    $this->makeColumn('name:nomor_sertifikat|label:Nomor Sertifikat|className:text-center'),
                    $this->makeColumn('name:tgl_sertifikat|label:Tanggal Sertifikat|className:text-center'),
                    $this->makeColumn('name:kegunaan_tanah|label:Kegunaan Tanah|className:text-center'),
                    $this->makeColumn('name:nilai_beli|label:Harga Perolehan (Rupiah)|className:text-center'),
                    $this->makeColumn('name:nilai_buku|label:Nilai Buku (Rupiah)|className:text-center'),
                    $this->makeColumn('name:nilai_kenaikan|label:Nilai Kenaikan (Rupiah)|className:text-center'),
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
        $records = Aset::with('coad')->where('type','KIB A')->filters()->dtGet();

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
                'tgl_register',
                function ($record) {
                return $record->book_date ? Carbon::parse($record->book_date)->formatLocalized('%d/%B/%Y') : '-';
            })
            ->addColumn(
                'luas_tanah',
                function ($record) {
                    return $record->wide ? number_format($record->wide, 0, ',', ',') : '-';
                }
            )->addColumn(
                'provinsi',
                function ($record) {
                    return $record->province_id ? $record->provinsi->name : '-';
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
                    return $record->address ? ucwords($record->address) : '-';
                }
            )->addColumn(
                'source_acq',
                function ($record) {
                    if ($record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ) {
                        return $record->usulans ? '<span class="badge bg-primary text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' : '-';
                    } else {
                        return $record->usulans ? '<span class="badge bg-success text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' : '-';
                    }
                   // return $record->usulans ? ucwords($record->usulans->trans->source_acq) : '-';
                }
            )->addColumn(
                'tahun_beli',
                function ($record) {
                    return $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $record->usulans->trans->receipt_date->format('Y');
                }
            )->addColumn(
                'hak_tanah',
                function ($record) {
                    return $record->hakTanah->name ? $record->hakTanah->name : '-';
                }
            )->addColumn(
                'kegunaan_tanah',
                function ($record) {
                    return $record->land_use ? ucwords($record->land_use) : '-';
                }
            )->addColumn(
                'nomor_sertifikat',
                function ($record) {
                    return $record->no_sertificate? $record->no_sertificate : '-';
                }
            )->addColumn(
                'tgl_sertifikat',
                function ($record) {
                    return $record->sertificate_date ? Carbon::parse($record->sertificate_date)->formatLocalized('%d/%B/%Y') : '-';
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
                'nilai_kenaikan',
                function ($record) {
                    return $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0';
                }
            )
            ->addColumn(
                'nilai_buku',
                function ($record) {
                    return $record->book_value ? number_format($record->book_value, 0, ',', ',') : '-';
                }
            )
            ->addColumn(
                'status',
                function ($record) {
                    if ($record->status == 'actives') {
                        return $record->status ? '<span class="badge bg-success text-white">'.ucfirst('active').'</span>' : '-';
                    } elseif ($record->status == 'notactive') {
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
                $actions[] = [
                    'type' => 'show',
                    'page' => true,
                    'id' => $record->id,
                    'url' => route($this->routes . '.show', $record->id),
                ];

                // if($record->condition =='rusak berat' && $record->status =='actives'){
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
                // }

                    return $this->makeButtonDropdown($actions);
                }
            )
            ->rawColumns(['source_acq','status','action','name','jenis_aset','updated_by'])
            ->make(true);
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

    public function deletes(Aset $record)
    {
        return $this->render('pengajuan.penghapusan-aset.create',compact('record'));
    }

    public function export(Request $request){
        $filters = [
            'jenis_aset' => $request->jenis_aset,
            'room_location' => $request->room_location,
            'location_id' => $request->location_id,
            'condition' => $request->condition,
        ];
        return Excel::download(new KibAExport($filters), date('Y-m-d') . ' KIBB.xlsx');
    }

    public function print()
    {
        $title ='Laporan Aset KIB A';
        $records = Aset::with('coad')->where('type','KIB A')->where('status','actives')->filters()->get();

        // $records = $query->filters()->get();
        // dd($records);

        // $gambar_logo_1 = public_path('assets/images/KLU_logo.png');
        // $gambar_logo_2 = public_path(config('base.logo.auth'));
        // $detail = PerencanaanDetail::where('perencanaan_id',$record->id)->get();
        $view1 = view($this->views.'.cetak',compact('records','title'))->render();
        // $view2 = view($this->views.'.cetakDetail',compact('detail','record','title','gambar_logo_1','gambar_logo_2'))->render();
        $html = $view1;
        $pdf = PDF::loadHTML($html)->setPaper('a3', 'landscape');
        return $pdf->stream('document.pdf');
        // return $this->render($this->views.'.cetak',compact('records','title'));
    }

}
