<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\PenghapusanRequest;
use App\Models\Pengajuan\Penghapusan;
use App\Models\Pengajuan\Pemutihans;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LaporanPenghapusanController extends Controller
{
    protected $module = 'laporan_penghapusan-aset';
    protected $routes = 'laporan.penghapusan-aset';
    protected $views = 'laporan';
    protected $perms = 'report-penghapusan';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Laporan Penghapusan',
            'breadcrumb' => [
                'Home' => route('home'),
                // 'Pengajuan' => '#',
                'Laporan Penghapusan' => route($this->routes . '.index'),
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
                    $this->makeColumn('name:type_aset|label:Tipe Aset|className:text-center|width:250px'),
                    $this->makeColumn('name:nama_aset|label:Nama Aset|className:text-center|width:250px'),
                    $this->makeColumn('name:departemen|label:Departemen|className:text-center|width:300px'),
                    $this->makeColumn('name:nilai_hapus|label:Nilai Aset Dihapus (Rupiah)|className:text-center|width:300px'),
                    $this->makeColumn('name:status'),
                    $this->makeColumn('name:updated_by'),
                    $this->makeColumn('name:action'),
                ],
            ],
        ]);
        
        $jumlah = Penghapusan::where('status', 'completed')
            ->whereYear('submission_date', date('Y'))
            ->count('id');

        // Mengambil semua kib_id dari Penghapusan dengan status 'completed' pada tahun ini
        $kib_ids = Penghapusan::where('status', 'completed')
            ->whereYear('submission_date', date('Y'))
            ->pluck('kib_id');

        // Menghitung total book_value dari asets yang terkait dengan kib_ids
        $value = Penghapusan::where('status', 'completed')
            ->whereYear('submission_date', date('Y'))
            ->with('asets')  // Include the related asets
            ->get()
            ->sum(function($q) use($kib_ids) {
                return $q->asets->whereIn('id',$kib_ids)->sum('book_value');
            });

        return $this->render($this->views . '.penghapusan', compact(['jumlah','value']));
    }

    public function grid()
    {
        $user = auth()->user();
        $records = Penghapusan::where('status','completed')->grid()->filters()->dtGet();

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

            ->addColumn('status', function ($record) use ($user) {
                return $record->labelStatus($record->status ?? 'new');
            })

            ->addColumn('nilai_hapus', function ($record) use ($user) {
                return number_format($record->asets->book_value, 0, ',', ',') ?? '0' ;
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
            'no_surat',
            'departemen',
            'nama_aset',
            'status','updated_by','action'])
            ->make(true);
    }

    public function show(Penghapusan $record)
    {
        return $this->render($this->views.'.penghapusanShow', compact('record'));
    }

}


