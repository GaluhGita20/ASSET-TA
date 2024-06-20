<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Aset\AsetRequest;
use App\Models\Globals\Menu;
// use App\Models\Master\Aset\Aset;
use App\Models\inventaris\Aset;
use App\Models\Master\Location\Location;
use App\Support\Base;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\Setting\KibEExport;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
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
                    $this->makeColumn('name:name|label:Nama Aset|className:text-left'),
                    $this->makeColumn('name:kode_akun|label:Kode Akun|className:text-center'),
                    $this->makeColumn('name:nomor_register|label:Nomor Register|className:text-center'),
                    $this->makeColumn('name:tgl_register|label:Tanggal Register|className:text-center'),
                    $this->makeColumn('name:status|label:Status|className:text-center'),
                    $this->makeColumn('name:kondisi|label:Kondisi|className:text-center'),
                    // $this->makeColumn('name:nama_akun|label:Nama Akun|className:text-center|width:300px'),
                    // $this->makeColumn('name:merek_tipe|label:Merek|className:text-center'),
                    // $this->makeColumn('name:ukuran_cc|label:Ukuran CC|className:text-center'),
                    $this->makeColumn('name:judul|label:Judul|className:text-center'),
                    $this->makeColumn('name:pencipta|label:Pencipta|className:text-center'),
                    $this->makeColumn('name:jenis|label:Jenis|className:text-center'),
                    $this->makeColumn('name:ukuran|label:Ukuran (M)|className:text-center'),
                    $this->makeColumn('name:bahan|label:Bahan|className:text-center'),
                    $this->makeColumn('name:tahun_beli|label:Tahun Perolehan|className:text-center'),
                    // $this->makeColumn('name:no_BPKB|label:Nomor BPKB|className:text-center'),
                    $this->makeColumn('name:source_acq|label:Sumber Perolehan|className:text-center'),
                    $this->makeColumn('name:asal_usul|label:Asal Usul|className:text-center'),
                    // $this->makeColumn('name:nilai_beli|label:Harga|className:text-center'),
                    // $this->makeColumn('name:masa_manfaat|label:Masa Manfaat|className:text-center'),
                    // $this->makeColumn('name:nilai_residu|label:Nilai Penyusutan|className:text-center'),
                    $this->makeColumn('name:nilai_beli|label:Harga Perolehan(Rupiah)|className:text-center'),
                    $this->makeColumn('name:masa_manfaat|label:Masa Manfaat (Tahun)|className:text-center'),
                    $this->makeColumn('name:nilai_residu|label:Nilai Residu (Rupiah)|className:text-center'),
                    $this->makeColumn('name:akumulasi|label:Akumulasi Penyusutan (Rupiah)|className:text-center'),
                    $this->makeColumn('name:nilai_buku|label:Nilai Buku (Rupiah)|className:text-center'),
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
                'tgl_register',
                function ($record) {
                return $record->book_date ? Carbon::parse($record->book_date)->formatLocalized('%d/%B/%Y') : '-';
            })->addColumn(
                'judul',
                function ($record) {
                    return $record->title ? ucwords($record->title) : '-';
                }
            )->addColumn(
                'pencipta',
                function ($record) {
                    return $record->creators ? ucwords($record->creators) : '-';
                }
            )->addColumn(
                'spesifikasi',
                function ($record) {
                    return $record->spesifikasi ? $record->spesifikasi : '-';
                }
            )->addColumn(
                'bahan',
                function ($record) {
                    return $record->materials->name ? $record->materials->name : '-';
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
                    return $record->usulans->trans->spk_start_date ? $record->usulans->trans->spk_start_date->format('Y') : $record->usulans->trans->receipt_date->format('Y');
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
                    return $record->usulans->trans->unit_cost ? number_format($record->usulans->trans->unit_cost, 0, ',', ',') : number_format($record->usulans->HPS_unit_cost, 0, ',', ',');
                }
            )->addColumn(
                'nilai_buku',
                function ($record) {
                    return $record->book_value ? number_format($record->book_value, 0, ',', ',') : '-';
                }
            )->addColumn(
                'masa_manfaat',
                function ($record) {
                    return $record->useful ? $record->useful : '-';
                }
            )->addColumn(
                'akumulasi',
                function ($record) {
                    return $record->accumulated_depreciation ? number_format($record->accumulated_depreciation, 0, ',', ',') : '0';
                }
            )
            ->addColumn(
                'source_acq',
                function ($record) {
                    if ($record->usulans->trans->source_acq == 'Hibah' || $record->usulans->trans->source_acq == 'Sumbangan' ) {
                        return $record->usulans ? '<span class="badge bg-primary text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' : '-';
                    } else {
                        return $record->usulans ? '<span class="badge bg-success text-white">'.ucfirst($record->usulans->trans->source_acq).'</span>' : '-';
                    }
                    //return $record->usulans ? ucwords($record->usulans->trans->source_acq) : '-';
                }
            )->addColumn(
                'kondisi',
                function ($record) {
                  //  return $record->condition ? ucfirst($record->condition) : '-';
                if ($record->condition == 'baik') {
                    return $record->condition ? '<span class="badge bg-success text-white">'.ucwords($record->condition).'</span>' : '-';
                } elseif ($record->condition == 'rusak berat') {
                    return $record->condition ? '<span class="badge bg-danger text-white">'.ucwords($record->condition).'</span>' : '-';
                } else {
                    return $record->condition ? '<span class="badge bg-warning text-white">'.ucwords($record->condition).'</span>' : '-';
                }
                }
            )->addColumn(
                'status',
                function ($record) {
                    if ($record->status == 'actives') {
                        return $record->status ? '<span class="badge bg-success text-white">'.ucfirst('active').'</span>' : '-';
                    }elseif ($record->status == 'maintenance') {
                        return $record->status ? '<span class="badge bg-warning text-white">'.ucfirst($record->status).'</span>' : '-';
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
                'masa_manfaat',
                function ($record) {
                    return $record->useful ? $record->useful : '-';
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
    
                // }
                    return $this->makeButtonDropdown($actions);
                }
            )
            ->rawColumns(['source_acq','status','kondisi','action','name','jenis_aset','updated_by'])
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
        $filters = [
            'jenis_aset' => $request->jenis_aset,
            'room_location' => $request->room_location,
            'location_id' => $request->location_id,
            'condition' => $request->condition,
        ];
        return Excel::download(new KibEExport($filters), date('Y-m-d') . ' KIBB.xlsx');
    }

    public function print(Request $request)
    {
        $title ='Laporan Aset KIB E';
        $query = Aset::with('coad')
        ->where('type', 'KIB E')
        ->whereIn('status', ['actives', 'in repair', 'in deletion', 'maintenance']);

        if ($request->jenis_aset !== null) {
            $query->where('jenis_aset', $request->jenis_aset);
        }

        if ($request->room_location !== null) {
            $query->where('room_location', $request->room_location);
        }

        if ($request->location_id !== null) {
            $query->where(function ($query) use ($request) {
                $query->whereHas('usulans', function ($q) use ($request) {
                    $q->whereHas('perencanaan', function ($qq) use ($request) {
                        $qq->where('struct_id', $request->location_id);
                    });
                })->orWhere('location_hibah_aset', $request->location_id);
            });
        }

        if ($request->condition !== null) {
            $query->where('condition', $request->condition);
        }

        $records = $query->filters()->get();
        //$records = Aset::with('coad')->where('type','KIB E')->whereIn('status',['actives','in repair','in deletion','maintenance'])->filters()->get();
        $view1 = view($this->views.'.cetak',compact('records','title'))->render();
        // $view2 = view($this->views.'.cetakDetail',compact('detail','record','title','gambar_logo_1','gambar_logo_2'))->render();
        $html = $view1;
        $pdf = PDF::loadHTML($html)->setPaper('a3', 'landscape');
        //$pdf = PDF::loadView($this->views.'.cetakDetail', compact('detail','record'))->setPaper('a4', 'portrait');;
        
        // Mengatur response untuk menampilkan PDF di browser
        return $pdf->stream('document.pdf');
        //return $this->render($this->views.'.cetak',compact('records','title'));
    }

    public function printKIR(Request $request)
    {
        $title ='Laporan Aset KIB E';
        if($request->room_location == null){
            return $this->rollback(
                [
                    'message' => 'Silahkan Pilih Ruang Lokasi Aset'
                ]
            );
            // $unit = Auth()->user()->position->location_id;
            // $ruang = '-';
            // // $records = null;
            // $records = Aset::with('coad')->where('type','KIB B')->whereIn('status',['actives','in repair','in deletion','maintenance'])->whereHas('usulans',
            // function ($q) use ($unit){
            //     $q->whereHas('perencanaan', function($qq) use ($unit){
            //         $qq->where('struct_id',$unit);
            //     });
            // })->filters()->get();
            // return $this->render($this->views.'.cetakKIR',compact('records','title','ruang','unit'));
        }else{
            $records = Aset::with('coad')->where('type','KIB E')->where('room_location',$request->room_location)
            ->whereIn('status',['actives','in repair','in deletion','maintenance'])->grid()->filters()->dtGet()->get();
            $ruang = $request->room_location;
            $ruang = Location::where('id',$ruang)->value('name');
            $unit = Location::where('id',$request->room_location)->first();
            // return $this->render($this->views.'.cetakKIR',compact('records','title','ruang','unit'));

            $view1 = view($this->views.'.cetakKIR',compact('records','title','ruang','unit'))->render();
            // $view2 = view($this->views.'.cetakDetail',compact('detail','record','title','gambar_logo_1','gambar_logo_2'))->render();
            $html = $view1;
            $pdf = PDF::loadHTML($html)->setPaper('a3', 'landscape');
            //$pdf = PDF::loadView($this->views.'.cetakDetail', compact('detail','record'))->setPaper('a4', 'portrait');;
            
            // Mengatur response untuk menampilkan PDF di browser
            return $pdf->stream('document.pdf');
        }
    }

}
