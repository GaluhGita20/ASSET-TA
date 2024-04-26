<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Aset\AsetRequest;
use App\Models\Globals\Menu;
use App\Models\Globals\Activity;
// use App\Models\Master\Aset\Aset;
use App\Models\inventaris\Aset;
use App\Models\Pemeliharaan\Pemeliharaan;
use App\Models\Pemeliharaan\PemeliharaanDetail;
use App\Models\Pengajuan\Perbaikan;
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
                    $this->makeColumn('name:tgl_register|label:Tanggal Register|className:text-center'),

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
                'tgl_register',
                function ($record) {
                return $record->book_date ? $record->book_date : '-';
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
                    return $record->materials->name ? $record->materials->name: '-';
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
                    return $record->locations ? $record->locations->name : $record->non_room_location;
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
            ->rawColumns(['source_acq','kondisi','status','action','name','jenis_aset','updated_by'])
            ->make(true);
    }

    public function detailsGrid(Aset $record){
        $records_inv = $record->logs()->whereModule('inventaris')->where('target_id',$record->id)->get();
        $perbaikan = Perbaikan::where('kib_id',$record->id)->where('status','approved')->pluck('id')->toArray();
        
        $pemeliharaan = Pemeliharaan::whereHas('details', function($q) use ($record){
            $q->where('kib_id',$record->id);
        })->pluck('id')->toArray();

        $perbaikan1 = Activity::where('module','perbaikan-aset')->whereIn('target_id',$perbaikan)->where('message', 'LIKE', '%membuat%')->get();
        $perbaikan2 = Activity::where('module','pj-perbaikan-aset')->whereIn('target_id',$perbaikan)->where('message', 'LIKE', '%Update Hasil Perbaikan%')->get();
        $pemeliharaan = Activity::where('module','pemeliharaan-aset')->where('target_id',$pemeliharaan)->where('message', 'LIKE', '%Menyetujui Jadwal%')->get();


        $mergedRecords = collect([$perbaikan1,$perbaikan2,$records_inv,$pemeliharaan])->collapse();

        $sortedRecords = $mergedRecords->sortBy('created_at');


        return \DataTables::of($sortedRecords)
            ->addColumn(
                'num',
                function ($sortedRecords) {
                    return request()->start;
                }
            )
            ->addColumn('date', function ($sortedRecords) {
                    return $sortedRecords->created_at ? $sortedRecords->created_at->format('d/m/Y') : '-';
                }
            )
            ->addColumn(
                'tindakan',
                function ($sortedRecords) {
                    if($sortedRecords->module == 'perbaikan-aset'){
                        // '<span class="badge bg-warning text-white">'.ucfirst($record->status).'</span>'
                        return $sortedRecords->module ? '<span class="badge bg-warning text-white"> Mengajukan Perbaikan </span>': '-';
                    }elseif($sortedRecords->module == 'pj-perbaikan-aset'){
                        return $sortedRecords->module ? '<span class="badge bg-warning text-white"> Update Perbaikan </span>': '-';
                    }elseif($sortedRecords->module == 'penghapusan-aset'){
                        return $sortedRecords->module ? '<span class="badge bg-danger text-white"> Mengajukan Penghapusan  </span>': '-';
                    }elseif($sortedRecords->module == 'pemutihan-aset'){
                        return $sortedRecords->module ? '<span class="badge bg-danger text-white"> Mengajukan Pemutihan </span>': '-';
                    }elseif($sortedRecords->module == 'pemeliharaan-aset'){
                        return $sortedRecords->module ? '<span class="badge bg-primary text-white"> Melakukan Pemeliharaan </span>': '-';
                    }
                    else{
                        return $sortedRecords->module ? '<span class="badge bg-success text-white"> Melakukan '.$sortedRecords->module.'</span>' : '-';
                    }
                }
            )
            ->addColumn(
                'keterangan',
                function ($sortedRecords) use ($record) {
                    if($sortedRecords->module == 'perbaikan-aset'){
                        $pesan = Perbaikan::find($sortedRecords->target_id)->problem;
                        return $pesan ? $pesan : '-';
                    }elseif ($sortedRecords->module == 'pj-perbaikan-aset') {
                        $pesan = Perbaikan::find($sortedRecords->target_id)->action_repair;
                        $hasil = Perbaikan::find($sortedRecords->target_id)->repair_results;
                        return $pesan ? ucfirst(strtolower($pesan)).' dengan hasil perbaikan '.strtolower($hasil) : '-';
                    }elseif ($sortedRecords->module == 'pemeliharaan-aset'){
                        $pesan = PemeliharaanDetail::where('pemeliharaan_id',$sortedRecords->target_id)->where('kib_id',$record->id)->value('maintenance_action');
                        return $pesan ? $pesan : '-';
                    }
                    return $sortedRecords->message ? $sortedRecords->message : '-';
                }
            )
            ->addColumn(
                'pegawai',
                function ($sortedRecords) {
                    return $sortedRecords->createdByRaw() ? $sortedRecords->createdByRaw() : '-';
                }
            )
        ->rawColumns([
            'date','tindakan','keterangan','pegawai'])
        ->make(true);
    }

    public function details(Aset $record){

        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:date|label:Tanggal|className:text-center|width:200px'),
                    $this->makeColumn('name:tindakan|label:Tindakan|className:text-center|width:300px'),
                    $this->makeColumn('name:keterangan|label:Keterangan|className:text-center|width:250px'),
                    $this->makeColumn('name:pegawai|label:Pegawai|className:text-center|width:250px'),
                ],
                'url' => route($this->routes . '.detailsGrid', $record->id),
            ],
        ]);

        $perbaikan = Perbaikan::where('kib_id',$record->id)->where('status','approved')->pluck('id')->toArray();
        
        $pemeliharaan = Pemeliharaan::whereHas('details', function($q) use ($record){
            $q->where('kib_id',$record->id);
        })->pluck('id')->toArray();

        $total_per = Activity::where('module','perbaikan-aset')->whereIn('target_id',$perbaikan)->where('message', 'LIKE', '%membuat%')->count();     
        $total_pem = Activity::where('module','pemeliharaan-aset')->whereIn('target_id',$pemeliharaan)->where('message', 'LIKE', '%menyetujui%')->count();

        
        return $this->render('pelaporan.detail-aset.index',compact(['record','total_per','total_pem']));
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
        return $this->render('perbaikan.perbaikan-aset.create',compact('record'));
    }

    public function deletes(Aset $record)
    {
        return $this->render('pengajuan.penghapusan-aset.create',compact('record'));
    }



    public function detailpGrid(Aset $record){

        $asset = Aset::find($record->id);
        $res_val = $asset->residual_value;
        $book_val = $asset->book_value;
        $book_reg = $asset->book_date;
        $useful = $asset->useful;

        $npt = ($book_val - $res_val)/$useful;

        $data  = collect();
        for($i=0; $i<=$useful; $i++){
            if($i == 0){
                $data->push([
                    'date'=> $book_reg ,
                    'nilai_penyusutan'=> 0,
                    'keterangan'=> 'Penyusutan Awal ',
                    'nilai_buku'=> $book_val,
                ]);
            }else{
                $penyusutan = $npt;
                // Hitung nilai buku setelah penyusutan
                $nilai_buku = max(0, $book_val - ($penyusutan * $i));
        
                $data->push([
                    'date' => date('Y-m-d', strtotime($book_reg . ' + ' . $i . ' years')),
                    'nilai_penyusutan' => $penyusutan * $i,
                    'keterangan' => 'Penyusutan Tahun Ke ' . $i,
                    'nilai_buku' => $nilai_buku,
                ]);
            }
        }

        return \DataTables::of($data->toArray())
            ->addColumn(
                'num',
                function ($data) {
                    return request()->start;
                }
            )
            ->addColumn('date', function ($data) {
                return isset($data['date']) ? $data['date'] : '-';
            })
            ->addColumn('nilai_penyusutan', function ($data) {
                return isset($data['nilai_penyusutan']) ? number_format($data['nilai_penyusutan'], 0, ',', ',') : '-';
            })
            ->addColumn('keterangan', function ($data) {
                return isset($data['keterangan']) ? $data['keterangan'] : '-';
            })
            ->addColumn('nilai_buku', function ($data) {
                return isset($data['nilai_buku']) ? number_format($data['nilai_buku'], 0, ',', ',') : '-';
            })
            ->rawColumns(['date','nilai_penyusutan' ,'keterangan', 'nilai_buku'])
            ->make(true);
    }

    public function detailp(Aset $record){

        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:date|label:Tanggal|className:text-center|width:200px'),
                    $this->makeColumn('name:nilai_penyusutan|label:Nilai Penyusutan|className:text-center|width:300px'),
                    $this->makeColumn('name:keterangan|label:Keterangan|className:text-center|width:250px'),
                    $this->makeColumn('name:nilai_buku|label:Nilai Buku|className:text-center|width:250px'),
                ],
                'url' => route($this->routes . '.detailpGrid', $record->id),
            ],
        ]);

        return $this->render('pelaporan.penyusutan.index',compact('record'));
    }

}
