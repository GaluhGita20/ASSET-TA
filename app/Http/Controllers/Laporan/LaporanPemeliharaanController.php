<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pemeliharaan\PemeliharaanRequest;
use App\Http\Requests\Pemeliharaan\PemeliharaanDetailRequest;
use App\Models\Pemeliharaan\Pemeliharaan;
use App\Models\Pemeliharaan\PemeliharaanDetail;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LaporanPemeliharaanController extends Controller
{
    protected $module = 'laporan_pemeliharaan-aset';
    protected $routes = 'laporan.pemeliharaan-aset';
    protected $views = 'laporan';
    protected $perms = 'report-pemeliharaan';


    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Laporan Pemeliharaan Aset',
            'breadcrumb' => [
                'Home' => route('home'),
                // 'Pengajuan' => '#',
                'Laporan Pemeliharaan Aset' => route($this->routes . '.index'),
            ]
        ]);
    }

    public function index()
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:#|className:text-right'),
                    $this->makeColumn('name:code|label:Nomor Surat|className:text-center|width:300px'),
                    $this->makeColumn('name:departemen|label:Unit Departemen|className:text-center|width:250px'),
                    $this->makeColumn('name:tanggal_pemeliharaan|label:Tanggal Pemeliharaan|className:text-center|width:300px'),
                    $this->makeColumn('name:status'),
                    $this->makeColumn('name:updated_by'),
                    // $this->makeColumn('name:action'),
                ],
            ],
        ]);
        $jumlah = Pemeliharaan::whereYear('maintenance_date',date('Y'))->where('status','completed')
            ->count('id');  // Include the related asets
            
        $value = PemeliharaanDetail::whereHas('pemeliharaan',function($q){
            $q->whereYear('maintenance_date',date('Y'))->where('status','completed');
        })->select('kib_id')->distinct()->count('kib_id');

        return $this->render($this->views . '.pemeliharaan', compact(['jumlah','value']));
        // return $this->render($this->views . '.pemutihan');
        // return $this->render($this->views . '.index');
    }

    public function grid()
    {
        $user = auth()->user();
        $records = Pemeliharaan::grid()->filters()->dtGet();

        return DataTables::of($records)
            ->addColumn('#', function ($record) {
                return request()->start;
            })
            ->addColumn(
                'code',
                function ($record) {
                    return $record->code ? $record->code : '-';
                }
            )

            ->addColumn('departemen', function ($record) {
                return $record->departemen_id ? $record->deps->name : '-';
            })

            ->addColumn('tanggal_pemeliharaan', function ($record) {
                return Carbon::parse($record->maintenance_date)->formatLocalized('%d/%B/%Y');
            })

            ->addColumn('status', function ($record) {
                if($record->status == 'completed'){
                    return '<span class="badge bg-success text-white">Verified</span>';
                }elseif($record->status == 'waiting.approval'){
                    return '<span class="badge bg-primary text-white">Waiting Verify</span>';
                }else{
                    return $record->status ?  $record->labelStatus($record->status) : '-';
                }
            })

            ->addColumn('updated_by', function ($record) {
                if ($record->status === 'new') {
                    return "";
                } else {
                    return $record->createdByRaw();
                }
            })

            ->rawColumns([
            'code',
            'departemen',
            'dates',
            'status','updated_by','action'])
            ->make(true);
    }

}


