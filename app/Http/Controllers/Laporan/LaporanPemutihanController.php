<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pemutihan\PemutihanRequest;
use App\Models\Pengajuan\Pemutihans;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LaporanPemutihanController extends Controller
{
    protected $module = 'laporan_pemutihan-aset';
    protected $routes = 'laporan.pemutihan-aset';
    protected $views = 'laporan';
    protected $perms = 'report-pemutihan';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Laporan Pemutihan',
            'breadcrumb' => [
                'Home' => route('home'),
                'Laporan Pemutihan' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function grid()
    {
        $user = auth()->user();
        $records = Pemutihans::where('status','completed')->grid()->filters()->dtGet();

        return DataTables::of($records)
            ->addColumn('#', function ($record) {
                return request()->start;
            })
            ->addColumn('nama_aset', function ($record) {
                    return $record->asets ? $record->asets->asetData->name : '-';
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

            ->addColumn('clean_type', function ($record) {
                return $record->pemutihanType->name;
            })

            ->addColumn('pic', function ($record) {
                return $record->picd->name;
            })

            ->addColumn('qty', function ($record) {
                return $record->qty;
            })

            ->addColumn('target', function ($record) {
                return $record->target;
            })

            ->addColumn('value', function ($record) {
                return number_format($record->valued, 0, ',', ',');
            })

            ->addColumn('status', function ($record) use ($user) {
                if($record->status =='completed'){
                    return '<span class="badge bg-success text-white">Verified</span>';
                }elseif($record->status == 'waiting.approval'){
                    return '<span class="badge bg-primary text-white">Waiting Verify</span>';
                }else{
                    return $record->labelStatus($record->status ?? 'new');
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
                $actions = [];

                if ($record->checkAction('show', $this->perms)) {
                    $actions[] = [
                        'type' => 'show',
                        'page' => true,
                        'id' => $record->id,
                        'url' => route($this->routes . '.show', $record->id),
                    ];
                }

                return $this->makeButtonDropdown($actions, $record->id);

            })

            ->rawColumns([
            'status','updated_by','action'])
            ->make(true);
    }


    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:no_surat|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:nama_aset|label:Nama Aset|className:text-center|width:250px'),
                    $this->makeColumn('name:type_aset|label:Tipe Aset|className:text-center|width:250px'),
                    $this->makeColumn('name:clean_type|label:Jenis Pemutihan|className:text-center|width:250px'),
                    $this->makeColumn('name:target|label:Target Pemutihan|className:text-center|width:250px'),
                    $this->makeColumn('name:qty|label:Jumlah|className:text-center|width:250px'),
                    $this->makeColumn('name:pic|label:PIC|className:text-center|width:250px'),
                    $this->makeColumn('name:value|label:Pendapatan Pemutihan (Rupiah)|className:text-center|width:250px'),
                    $this->makeColumn('name:location|label:Lokasi Pemutihan|className:text-center|width:250px'),
                    $this->makeColumn('name:status'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);
        $jumlah = Pemutihans::whereYear('submission_date',date('Y'))->where('status','completed')
        ->count('id');  // Include the related asets

        $value = Pemutihans::whereYear('submission_date',date('Y'))->where('status','completed')
        ->sum('valued'); 

        return $this->render($this->views . '.pemutihan', compact(['jumlah','value']));
        // return $this->render($this->views . '.pemutihan');
    }

    public function show(Pemutihans $record)
    {
        
        return $this->render($this->views.'.pemutihanShow', compact('record'));
    }



}


