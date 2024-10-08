<?php

namespace App\Http\Controllers\Laporan\Inventaris;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Aset\AsetRequest;
use App\Models\Globals\Menu;
use App\Models\Globals\Activity;
// use App\Models\Master\Aset\Aset;
use App\Models\inventaris\Aset;
use App\Models\Pemeliharaan\Pemeliharaan;
use App\Models\Pemeliharaan\PemeliharaanDetail;
use App\Models\Pengajuan\Perbaikan;
use App\Models\Pengajuan\Pemutihans;
use App\Models\Pengajuan\Penghapusan;
use App\Support\Base;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KIBBController extends Controller
{
    //
    protected $module = 'laporan-inventaris_kib-b';
    protected $routes = 'laporan.inventaris.kib-b';
    protected $views  = 'pelaporan.kib-b';
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
                'title' => 'Laporan Aset Peralatan Mesin',
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
                    // $this->makeColumn('name:source_acq|label:Sumber Perolehan|className:text-center'),
                    // $this->makeColumn('name:asal_usul|label:Asal Usul|className:text-center'),
                    $this->makeColumn('name:nilai_beli|label:Harga Perolehan (Rupiah)|className:text-center'),
                    $this->makeColumn('name:masa_manfaat|label:Masa Manfaat (Tahun)|className:text-center'),
                    $this->makeColumn('name:nilai_residu|label:Nilai Penyusutan (Rupiah)|className:text-center'),
                    $this->makeColumn('name:akumulasi|label:Akumulasi Penyusutan (Rupiah)|className:text-center'),
                    $this->makeColumn('name:nilai_buku|label:Nilai Buku (Rupiah)|className:text-center'),
                    $this->makeColumn('name:unit|label:Unit|className:text-center'),
                    $this->makeColumn('name:location|label:Lokasi|className:text-center'),
                    // $this->makeColumn('name:keterangan|label:Keterangan|className:text-center'),
                    $this->makeColumn('name:updated_by'),
                    // $this->makeColumn('name:created_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);
        $jumlah = Aset::where('type','KIB B')->whereIn('status',['actives','in repair','in deletion','maintenance'])->count('id');
        $value = Aset::where('type','KIB B')->whereIn('status',['actives','in repair','in deletion','maintenance'])->sum('book_value');
        return $this->render($this->views . '.index', compact(['jumlah','value']));
        // return $this->render($this->views . '.index');
    }
    
    public function grid()
    {
        $user = auth()->user();
        $records = Aset::with('coad')->where('type','KIB B')->filters()->dtGet();

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
                })
            ->addColumn(
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
                    'label' => 'Laporan Detail',
                    'icon' => 'fa fa-book text-success',
                    'id' => $record->id,
                    // 'method' =>'post',
                    'url' => route($this->routes . '.details', $record->id),
                ];

                $actions[] = [
                    'type' => 'show',
                    'page' => true,
                    'label' => 'Laporan Penyusutan',
                    'icon' => 'fas fa-coins text-primary',
                    'id' => $record->id,
                    'url' => route($this->routes . '.detailp', $record->id),
                ];

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

        // if($record->status=='clean'){
        $pemutihan = Pemutihans::where('kib_id',$record->id)->where('status','completed')->pluck('id')->toArray();
        if($pemutihan == null){
            $updated = Aset::where('id',$record->id)->where('status','clean')->value('updated_at');
            //$updated = Aset::where('id',$record->id)->where('status','clean')->get();
           // dd($updated);

            //temukan temannya
            // dd($updated->format('d-m-Y H-i-s'));
            if($updated){
                $pemutihanAset = Aset::where('updated_at', $updated->format('Y-m-d H:i:s'))
                        ->where('status', 'clean')
                        ->where('id', '<>', $record->id)
                        ->first();
                
                if ($pemutihanAset) {
                    $pemutihan = Pemutihans::where('kib_id', $pemutihanAset->id)
                        ->where('status', 'completed')
                        ->pluck('id')
                        ->toArray();
                }else{
                    $pemutihan==null;
                }
            }
            // dd($pemutihan);

        }

        $pemutihan1 = Activity::where('module','pemutihan-aset')->where('target_id',$pemutihan)->where('message','LIKE', '%Membuat Pengajuan%')->get();
        $pemutihan2 = Activity::where('module', 'pemutihan-aset')
        ->where('target_id', $pemutihan)
        ->where('message', 'LIKE', '%Menyetujui Pengajuan%')
        ->latest('created_at')->first();

        if($pemutihan2 != null){
            $pemutihan2 = Activity::where('id',$pemutihan2->id)->get();
        }

        //$pemutihan2 = Activity::where('module','pemutihan-aset')->where('target_id',$pemutihan)->where('message','LIKE', '%Menyetujui Pengajuan%')->get();
        // // }

        $perbaikan1 = Activity::where('module','perbaikan-aset')->whereIn('target_id',$perbaikan)->where('message', 'LIKE', '%membuat%')->get();
        $perbaikan2 = Activity::where('module','perbaikan-aset')->whereIn('target_id',$perbaikan)->where('message', 'LIKE', '%Update Hasil Perbaikan%')->get();
        $pemeliharaan = Activity::where('module','pemeliharaan-aset')->whereIn('target_id',$pemeliharaan)->where('message', 'LIKE', '%menyetujui%')->get();
        
        $penghapusan = Penghapusan::where('kib_id',$record->id)->where('status','completed')->pluck('id')->toArray();
        
        $penghapusan1 = Activity::where('module','penghapusan-aset')->where('target_id',$penghapusan)->where('message','LIKE', '%Membuat Pengajuan%')->get();
        $penghapusan2 = Activity::where('module', 'penghapusan-aset')
        ->where('target_id', $penghapusan)
        ->where('message', 'LIKE', '%Menyetujui Pengajuan%')
        ->latest('created_at')->first();

        if($penghapusan2 != null){
            $penghapusan2 = Activity::where('id',$penghapusan2->id)->get();
        }

        $mergedRecords = collect([$perbaikan1,$perbaikan2,$records_inv,$pemeliharaan,$penghapusan1,$penghapusan2,$pemutihan1,$pemutihan2])->collapse();

        $sortedRecords = $mergedRecords->sortBy('created_at');

        // $mergedRecords = collect([$penghapusan1,$penghapusan2])->collapse();
        // $sortedRecords = $mergedRecords->sortBy('created_at');
        // dd($pemutihan2->first()->message);
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
                function ($sortedRecords) use ($penghapusan2,$pemutihan2,$perbaikan2) {
                    // dd($penghapusan2->first()->id);
                    if($sortedRecords->module == 'perbaikan-aset'){
                        if($sortedRecords->message != $perbaikan2->first()->message){
                            return '<span class="badge bg-warning text-white"> Mengajukan Perbaikan Aset</span>';
                        }else{
                            return '<span class="badge bg-warning text-white"> Melakukan Perbaikan Aset</span>';
                        }
                        // return $sortedRecords->module ? '<span class="badge bg-warning text-white"> Mengajukan Perbaikan </span>': '-';
                    }elseif($sortedRecords->module == 'pemeliharaan-aset'){
                        return $sortedRecords->module ? '<span class="badge bg-primary text-white"> Melakukan Pemeliharaan </span>': '-';
                    }elseif ($sortedRecords->module=='penghapusan-aset') {
                        if($sortedRecords->message != $penghapusan2->first()->message){
                            return '<span class="badge bg-danger text-white"> Mengajukan Penghapusan Aset</span>';
                        }else{
                            return '<span class="badge bg-danger text-white"> Melakukan Penghapusan Aset</span>';
                        }
                    }elseif ($sortedRecords->module =='pemutihan-aset' && $pemutihan2->first()->message != null) {
                        if($sortedRecords->message != $pemutihan2->first()->message){
                            return '<span class="badge bg-danger text-white"> Mengajukan Pemutihan Aset</span>';
                        }else{
                            return '<span class="badge bg-danger text-white"> Melakukan Pemutihan Aset</span>';
                        }
                    }
                    else{
                        return $sortedRecords->module ? '<span class="badge bg-success text-white"> Melakukan '.$sortedRecords->module.'</span>' : '-';
                    }
                }
            )
            ->addColumn(
                'keterangan',
                function ($sortedRecords) use ($record,$perbaikan2) {
                    if($sortedRecords->module == 'perbaikan-aset'){
                        if($sortedRecords->message == $perbaikan2->first()->message){
                            $pesan1 = Perbaikan::find($sortedRecords->target_id)->action_repair;
                            $pesan2 = Perbaikan::find($sortedRecords->target_id)->repair_results;
                            return $pesan1 ? $pesan1.' dengan hasil '.ucfirst(strtolower($pesan2)) : '-';
                            // return '<span class="badge bg-warning text-white"> Mengajukan Perbaikan Aset</span>';
                        }else{
                            // return '<span class="badge bg-warning text-white"> Melakukan Perbaikan Aset</span>';
                            $pesan = Perbaikan::find($sortedRecords->target_id)->problem;
                            return $pesan ? $pesan : '-';
                        }
                    // }
                    //     return $pesan ? ucfirst(strtolower($pesan)).' dengan hasil perbaikan '.strtolower($hasil) : '-';
                    }elseif ($sortedRecords->module == 'pemeliharaan-aset'){
                        $pesan = PemeliharaanDetail::where('pemeliharaan_id',$sortedRecords->target_id)->where('kib_id',$record->id)->value('maintenance_action');
                        return $pesan ? $pesan : '-';
                    }elseif ($sortedRecords->module == 'penghapusan-aset' && strpos($sortedRecords->message, 'Membuat Pengajuan') !== false){
                        $pesan = Penghapusan::find($sortedRecords->target_id)->value('desc_del');
                        return $pesan ? 'Mengajukan Penghapusan Karena '.$pesan : '-';
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


    public function detailpGrid(Aset $record){

        $asset = Aset::find($record->id);
        $res_val = $asset->residual_value;
        $book_val = $asset->acq_value;
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
