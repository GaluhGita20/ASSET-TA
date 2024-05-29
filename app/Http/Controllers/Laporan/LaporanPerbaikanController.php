<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PerbaikanRequest;
use App\Http\Requests\Pengajuan\PerbaikanVerifyRequest;
use App\Http\Requests\Pengajuan\HasilPerbaikan2Request;
use App\Models\Perbaikan\TransPerbaikanDisposisi;
use App\Models\Perbaikan\UsulanSperpat;
use App\Models\Pengajuan\Perbaikan;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LaporanPerbaikanController extends Controller
{
    protected $module = 'laporan_perbaikan-aset';
    protected $routes = 'laporan.perbaikan-aset';
    protected $views = 'laporan';
    protected $perms = 'report-perbaikan';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Laporan Perbaikan Aset',
            'breadcrumb' => [
                'Home' => route('home'),
                'Laporan Perbaikan Aset' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:no_surat|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:nama_aset|label:Nama Aset|className:text-center|width:250px'),
                    $this->makeColumn('name:type_aset|label:Tipe Aset|className:text-center|width:300px'),
                    $this->makeColumn('name:departemen|label:Departemen|className:text-center|width:300px'),
                    $this->makeColumn('name:is_disposisi|label:Status Diposisi|className:text-center'),
                    $this->makeColumn('name:status|label:Verifikasi Kerusakan'),
                    $this->makeColumn('name:tanggal_panggil|label:Tanggal Panggil|className:text-center|width:300px'),
                    $this->makeColumn('name:hasil_perbaikan|label:Hasil Perbaikan|className:text-center|width:300px'),
                    $this->makeColumn('name:biaya|label:Total Biaya Perbaikan (Rupiah)|className:text-center|width:300px'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);

        $jumlah = Perbaikan::where('repair_results','<>','BELUM')->where('status', 'Approved')->where('check_up_result','<>',null)
        ->whereYear('repair_date',date('Y'))->count('id');

        $value = TransPerbaikanDisposisi::whereHas('codes', function($q){
            $q->where('status', 'Approved')->where('check_up_result','<>',null)
            ->whereYear('repair_date',date('Y'));
        })->sum('total_cost');

        return $this->render($this->views . '.perbaikanAset',compact(['jumlah','value']));
    }

    public function grid()
    {
        $user = auth()->user();
        $records = Perbaikan::where('status', 'Approved')->where('check_up_result','<>',null)->where('repair_results','<>','BELUM') // Atur urutan tambahan jika diperlukan
        ->filters()
        ->dtGet();

        return DataTables::of($records)
            ->addColumn('#', function ($record) {
                return request()->start;
            })
            ->addColumn(
                'nama_aset',
                function ($record) {
                    return $record->asets ? $record->asets->usulans->asetd->name : '-';
                }
            )

            ->addColumn(
                'type_aset',
                function ($record) {
                    return $record->asets ? $record->asets->type : '-';
                }
            )

            ->addColumn('no_surat', function ($record) {
                return $record->code;
            })

            ->addColumn('departemen', function ($record) {
                return $record->deps->name;
            })

            ->addColumn('tanggal_panggil', function ($record) {
                return $record->repair_date ?  $record->repair_date->formatLocalized('%d/%B/%Y') : '-';
            })
            ->addColumn('is_disposisi', function ($record) use ($user) {
                if($record->is_disposisi == 'yes'){
                    return '<span class="badge bg-success text-white"> Yes </span>';
                }else{
                    return '<span class="badge bg-danger text-white"> No </span>';
                }
            })

            ->addColumn('status', function ($record) use ($user) {
                if($record->status == 'approved'){
                    return '<span class="badge bg-success text-white">Verified</span>';
                }else{
                    return $record->labelStatus($record->status ?? 'new');
                }
                //return $record->labelStatus($record->status ?? 'new');
            })

            ->addColumn('tanggal_pengajuan', function ($record) {
                return $record->submission_date ?  $record->submission_date->format('d/m/Y') : '-';
            })
            ->addColumn('biaya', function ($record) use ($user) {
                $biaya = TransPerbaikanDisposisi::where('perbaikan_id',$record->id)->sum('total_cost');
                return number_format($biaya, 0, ',', ',') ? number_format($biaya, 0, ',', ',') : '0';
            })

            ->addColumn('hasil_perbaikan', function ($record) use ($user) {
                if($record->repair_results == 'SELESAI'){
                    return '<span class="badge bg-success text-white">'.ucfirst($record->repair_results).'</span>';
                }elseif($record->repair_results == 'BELUM' || $record->repair_results == null){
                    return '<span class="badge bg-warning text-white">'.'BELUM SELESAI'.'</span>'; 
                }else{
                    return '<span class="badge bg-danger text-white">'.ucfirst($record->repair_results).'</span>';
                }
            })

            ->addColumn('updated_by', function ($record) {
                if ($record->status === 'new') {
                    return "";
                } else {
                    return $record->createdByRaw();
                }
            })

            ->addColumn('action', function ($record) use ($user) {

                $actions[] = [
                    'type' => 'show',
                    'page' => true,
                    'id' => $record->id,
                    'url' => route($this->routes . '.show', $record->id),
                ];
            
                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns([
            'hasil_perbaikan',
            'no_surat',
            'departemen',
            'nama_aset',
            'is_disposisi',
            'tanggal_pengajuan',
            'status','updated_by','action'])
            ->make(true);
    }


    public function detailGrid(Perbaikan $record)
    {        
        $user = auth()->user();
        $records = TransPerbaikanDisposisi::with('codes')->where('perbaikan_id',$record->id)
        ->orderByRaw("CASE WHEN updated_at > created_at THEN updated_at ELSE created_at END DESC")->filters()
        ->dtGet();
    
            return DataTables::of($records)
            ->addColumn('#', function ($record) {
                return request()->start;
            })
            ->addColumn('no_surat', function ($record) {
                    return $record->codes ? $record->codes->code : '-';
                }
            )
            ->addColumn('repair_type', function ($record) {
                if ($record->repair_type == 'sperpat') {
                // return '<span data-short="Completed" class="label label-success label-inline text-nowrap " style="">Completed</span>';
                    return '<span class="badge bg-success text-white">' . $record->repair_type . '</span>';
                } else {
                    return '<span class="badge bg-primary text-white">'.$record->repair_type.'</span>';
                }
            })

            ->addColumn('vendor', function ($record) {
                return $record->vendors->name;
                //return $record->id;
            })

            ->addColumn('spk_start_date', function ($record) {
                return $record->spk_start_date ? Carbon::parse($record->spk_start_date)->format('Y-m-d'):'-';
            })

            ->addColumn('spk_end_date', function ($record) {
                return $record->spk_start_date ? Carbon::parse($record->spk_start_date)->format('Y-m-d'):'-';
            })

            ->addColumn('no_spk', function ($record) {
                return $record->no_spk ? $record->no_spk:'-';
            })

            ->addColumn('total', function ($record) {
                return $record->total_cost ? number_format($record->total_cost, 0, ',', ',') :'-';
            })

            ->addColumn('sper_status', function ($record) use ($user) {
                return $record->labelStatus($record->sper_status ?? 'new');
            })

            ->addColumn('status', function ($record) use ($user) {
                return $record->labelStatus($record->status ?? 'new');
            })

            ->addColumn('updated_by', function ($record) {
                if ($record->status === 'new') {
                    return "";
                } else {
                    return $record->createdByRaw();
                }
            })

            ->addColumn('action', function ($record) use ($user) {
                $actions=[];

                $data = $record->id;
                $actions[] = [
                    'type' => 'show',
                    'page' => true,
                    'label' => 'Detail',
                    'icon' => 'fa fa-plus text-info',
                    'id' => $record->id,
                    'url' => route($this->routes . '.detailShow', 13),
                    // dd($record->id)
                ];
                
                return $this->makeButtonDropdown($actions, $record->id);
            })
            ->rawColumns(['repair_type','sper_status',
            'status','updated_by','action'])
            ->make(true);
    }

    public function detailShow(TransPerbaikanDisposisi $record){
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:sperpat_name|label:Nama Sperpat|className:text-left|width:200px'),
                    $this->makeColumn('name:qty|label:Jumlah|className:text-center|width:300px'),
                    $this->makeColumn('name:unit_cost|label:Harga Satuan|className:text-center|width:250px'),
                    $this->makeColumn('name:total_cost|label:Harga Total|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|label:Diperbarui|width:300px'),
                    $this->makeColumn('name:action_show|label:Aksi'),
                ],
                // $record = TransPerbaikanDisposisi::where()
                'url' => route('perbaikan.usulan-sperpat'.'.detailGrid',13),
            ],
        ]);
        $record = TransPerbaikanDisposisi::find(13)->first();
        // dd($record->id);
        $ts_cost = UsulanSperpat::where('trans_perbaikan_id',13)->sum('total_cost');
        return $this->render('perbaikan.pj-perbaikan.detail.show', compact('record','ts_cost'));
    }

    public function show(Perbaikan $record)
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:no_surat|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:vendor|label:Vendor|className:text-center|width:250px'),
                    $this->makeColumn('name:repair_type|label:Jenis Perbaikan|className:text-center|width:250px'),
                    $this->makeColumn('name:total|label:Total Biaya (Rupiah)|className:text-center|width:250px'),
                    $this->makeColumn('name:sper_status|label:Sperpat Status|className:text-center'),
                    $this->makeColumn('name:status|label:Status Transaksi|className:text-center'),
                    $this->makeColumn('name:updated_by'),
                    // $this->makeColumn('name:action'),
                ],
                'url' => route($this->routes . '.detailGrid', $record->id),
            ],
        ]);

        return $this->render($this->views . '.perbaikanShow', compact('record'));

    }

}


