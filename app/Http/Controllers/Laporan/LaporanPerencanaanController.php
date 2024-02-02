<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan\Perencanaan;
use App\Models\Pengajuan\PerencanaanDetail;
use App\Models\Globals\Approval;
use App\Models\Master\Org\Position;
use App\Support\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LaporanPerencanaanController extends Controller
{
    protected $module = 'laporan_perencanaan-aset';
    protected $routes = 'laporan.perencanaan-aset';
    protected $views = 'laporan';
    protected $perms = 'perencanaan-aset';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'perms' => $this->perms,
            'permission' => $this->perms . '.view',
            'title' => 'Laporan',
            'breadcrumb' => [
                'Home' => route('home'),
            //    'Laporan' => '#',
                'Usulan Aset' => route($this->routes . '.index'),
            ]
        ]);
    }


    public function grid()
    {        
        $user = auth()->user();
        $record = Perencanaan::all();
        $records = PerencanaanDetail::with(['perencanaan'])->with('users')
            ->whereHas(
                'perencanaan',
                function ($q){
                    $q->where('status','completed');
                }
            )->orderByRaw("CASE WHEN updated_at > created_at THEN updated_at ELSE created_at END DESC")->laporan()
            ->dtGet();
        return DataTables::of($records)
            ->addColumn(
                'num',
                function ($detail) {
                    return request()->start;
                }
            )
            ->addColumn(
                'ref_aset_id',
                function ($detail) {
                    return $detail->asetd->name ? $detail->asetd->name : '';
                }
            )
            ->addColumn(
                'desc_spesification',
                function ($detail) {
                    return $detail->desc_spesification ? $detail->desc_spesification : '';
                }
            )
            ->addColumn(
                'existing_amount',
                function ($detail) {
                    return $detail->existing_amount;
                }
            )
            ->addColumn(
                'requirement_standard',
                function ($detail) {
                    return $detail->requirement_standard;
                }
            )
            ->addColumn(
                'qty_req',
                function ($detail) {
                    return $detail->qty_req;
                }
            )
            ->addColumn(
                'HPS_unit_cost',
                function ($detail) {
                    return number_format($detail->HPS_unit_cost, 0, ',', ',');
                }
            )
            ->addColumn(
                'status',
                function ($detail) {
                    // return $detail->labelStatus();
                    if ($detail->qty_agree > 0) {
                        return '<span class="badge bg-success text-white">'.ucfirst('Disetujui').'</span>';
                    }  else {
                        return '<span class="badge bg-danger text-white">'.ucfirst('Ditolak').'</span>';
                    }
                }
            )
            ->addColumn(
                'HPS_total_cost',
                function ($detail) {
                    return number_format($detail->HPS_total_cost, 0, ',', ',');
                }
            )
            ->addColumn(
                'qty_agree',
                function ($detail) {
                    return number_format($detail->qty_agree, 0, ',', ',') ?? '0';
                }
            )
            ->addColumn(
                'HPS_total_agree',
                function ($detail) {
                    return number_format($detail->HPS_total_agree, 0, ',', ',');
                }
            )
            ->addColumn(
                'sumber_dana',
                function ($detail) {
                    return $detail->danad ? $detail->danad->name : '-';
                }
            )->addColumn(
                'procurement_year',
                function ($detail){
                    return $detail->perencanaan->procurement_year ? $detail->perencanaan->procurement_year : '-';
                }
            )->addColumn(
                'departement',
                function ($detail){
                    return $detail->perencanaan->struct->name ? $detail->perencanaan->struct->name : '-';
                }
            )
            ->addColumn(
                'status',
                function ($detail){
                    return $detail->labelStatus($detail->status);
                    // return $detail->status ? '<span class="label label-light-primary">'.$detail->status.'<span>' : '-';
                    // return '<span class="label label-light-primary font-weight-bold label-inline text-nowrap" data-toggle="tooltip">'.$detail->status.'</span>';
                }
            )
            // '<span class="label label-light-'
            ->addColumn(
                'updated_by',
                function ($detail){
                    return $detail->perencanaan->createdByRaw();
                }
            )
            ->addColumn(
                'action_show',
                function ($detail){
                    $actions = [];
                    $actions[] = [
                        'type' => 'show',
                        'url' => route($this->routes . '.detailShow', $detail->id),
                    ];                    
                    return $this->makeButtonDropdown($actions, $detail->id);
                }
            )
            
            ->rawColumns(['status','action_show','updated_by','created_by','status'])
            ->make(true);
    }

    public function index(PerencanaanDetail $record)
    {
        $this->prepare([
            'tableStruct' => [
                'datatable_1' => [
                    $this->makeColumn('name:num|label:#'),
                    $this->makeColumn('name:ref_aset_id|label:Nama Aset|className:text-left|width:200px'),
                    $this->makeColumn('name:desc_spesification|label:Spesifikasi|className:text-left|width:300px'),
                    $this->makeColumn('name:qty_req|label:Pengajuan|className:text-center|width:250px'),
                    $this->makeColumn('name:qty_agree|label:Disetujui|className:text-center|width:250px'),
                    $this->makeColumn('name:status|label:Status|className:text-center|width:150px'),
                    $this->makeColumn('name:procurement_year|label:Tahun Pengadaaan|className:text-center|width:250px'),
                    $this->makeColumn('name:departement|label:Unit Kerja|className:text-center|width:250px'),
                    $this->makeColumn('name:updated_by|width:300px'),
                  //  $this->makeColumn('name:created_by|width:300px'),
                    $this->makeColumn('name:action_show|label:Aksi'),
                ],
            ],
        ]);
        return $this->render($this->views . '.perencanaan', compact('record'));
    }

    public function detailShow(PerencanaanDetail $detail)
    {
        $type ='show';
        $baseContentReplace = 'base-modal--render';
        return $this->render('pengajuan.perencanaan-aset.detail.show', compact('type','detail', 'baseContentReplace'));
    }

    
}





