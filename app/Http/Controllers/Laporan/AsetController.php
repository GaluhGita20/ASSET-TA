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

class AsetController extends Controller
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

    public function detail(Aset $record){
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:date|label:Tanggal|className:text-left|width:200px'),
                    $this->makeColumn('name:tindakan|label:Tindakan|className:text-center|width:300px'),
                    $this->makeColumn('name:keterangan|label:Keterangan|className:text-center|width:250px'),
                    $this->makeColumn('name:pegawai|label:Pegawai|className:text-center|width:250px'),
                ],
                'url' => route($this->routes . '.detail', $record->id),
            ],
        ]);
        return $this->render('pelaporan.detail-aset.index',compact('record'));
    }

    public function detailGrid(Aset $record){
        $sortedRecords = $record->logs()->whereModule('inventaris')->where('target_id',$record->id);
        return DataTables::of($sortedRecords)
            ->addColumn('#', function ($sortedRecords) {
                return request()->start;
            })
            ->addColumn('date', function ($sortedRecords) {
                    return $sortedRecords->created_at ? $sortedRecords->created_at : '-';
                }
            )
            ->addColumn(
                'tindakan',
                function ($sortedRecords) {
                    return $sortedRecords->module ? $sortedRecords->module : '-';
                }
            )
            ->addColumn(
                'keterangan',
                function ($sortedRecords) {
                    return $sortedRecords->message ? $sortedRecords->message : '-';
                }
            )
            ->addColumn(
                'pegawai',
                function ($sortedRecords) {
                    return $sortedRecords->created_by ? $sortedRecords->created_by : '-';
                }
            )
        ->rawColumns([
            'date','tindakan','keterangan','pegawai'])
        ->make(true);
    }




}