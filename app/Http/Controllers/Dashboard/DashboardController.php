<?php

namespace App\Http\Controllers\Dashboard;

use App;
use App\Http\Controllers\Controller;
use App\Models\Master\Org\OrgStruct;
use App\Models\Master\Org\Position;
use Illuminate\Http\Request;
use App\Models\Inventaris\Aset;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $module =  'dashboard';
    protected $routes =  'dashboard';
    protected $views =  'dashboard';

    public function __construct()
    {
        $this->prepare([
            'module' => $this->module,
            'routes' => $this->routes,
            'views' => $this->views,
            'title' => 'Dashboard',
        ]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->status != 'active') {
            return $this->render($this->views.'.nonactive');
        }
        if (!$user->checkPerms('dashboard.view') || !$user->roles()->exists()) {
            return abort(403);
        }

        $progress = [
            [
                'name' => 'reporting',
                'title' => 'Pelaporan Audit',
                'color' => 'success',
                'icon' => 'fas fa-bookmark',
            ],
            [
                'name' => 'followup',
                'title' => 'Tindak Lanjut Audit',
                'color' => 'warning',
                'icon' => 'fas fa-id-card',
            ],
        ];

        $is_auditor = false;

        if(isset($user->position->id) && $user->position->imAuditor()) {
            $is_auditor = true;

            array_unshift($progress, [
                'name' => 'conducting',
                'title' => 'Pelaksanaan Audit',
                'color' => 'danger',
                'icon' => 'fa fa-tags',
            ]);

            array_unshift($progress, [
                'name' => 'preparation',
                'title' => 'Persiapan Audit',
                'color' => 'primary',
                'icon' => 'fas fa-paper-plane',
            ]);
        }

        return $this->render($this->views.'.index', ['progress' => $progress, 'is_auditor' => $is_auditor]);
    }

    public function setLang($lang)
    {
        App::setLocale($lang);
        session()->put('locale', $lang);

        return redirect()->back();
    }

    public function progress(Request $request)
    {
        // Preparation
        $total = Summary::gridAssignment()
                        ->filters()
                        ->count();
        $compl = Summary::gridAssignment()
                        // ->hasCompleted('assignment')
                        // ->hasCompleted('document')
                        ->whereHas('document', function ($q) {
                            $q->whereHas('docFull', function ($qq) {
                                $qq->where('status', 'completed');
                            });
                        })
                        // ->hasCompleted('apm')
                        // ->hasCompleted('fee')
                        ->filters()
                        ->count();

        $percent = ($compl > 0 && $total > 0) ? round(($compl/$total*100), 0) : 0;
        $cards[] = [
            'name' => 'preparation',
            'total' => $total,
            'completed' => $compl,
            'percent' => $percent,
        ];

        // Conducting
        $total = Summary::gridOpening()
                        ->filters()
                        ->count();
        $compl = Summary::gridOpening()
                        // ->hasCompleted('opening')
                        // ->hasCompleted('sample')
                        // ->hasCompleted('feedback')
                        // ->hasCompleted('worksheet')
                        ->hasCompleted('closing')
                        ->filters()
                        ->count();

        $percent = ($compl && $total) ? round(($compl/$total*100), 0) : 0;
        $cards[] = [
            'name' => 'conducting',
            'total' => $total,
            'completed' => $compl,
            'percent' => $percent,
        ];

        // Reporting
        $total = Summary::gridExiting()
                        ->filters()
                        ->count();
        $compl = Summary::gridExiting()
                        // ->hasCompleted('lha')
                        ->hasCompleted('exiting')
                        ->filters()
                        ->count();

        $percent = ($compl && $total) ? round(($compl/$total*100), 0) : 0;
        $cards[] = [
            'name' => 'reporting',
            'total' => $total,
            'completed' => $compl,
            'percent' => $percent,
        ];

        // Followup
        $total = Summary::gridFollowupReg()
                        ->filters()
                        ->count();
        $compl = Summary::gridFollowupReg()
                        // ->hasCompleted('followupReg')
                        ->hasCompleted('followupMonitor')
                        ->filters()
                        ->count();

        $percent = ($compl && $total) ? round(($compl/$total*100), 0) : 0;
        $cards[] = [
            'name' => 'followup',
            'total' => $total,
            'completed' => $compl,
            'percent' => $percent,
        ];

        return response()->json([
            'data' => $cards
        ]);
    }



    public function progressAset(Request $request)
    {

        $not_active = Aset::where('status','notactive')->where('type','KIB A')->count();
        $active = Aset::where('status', 'active')->where('type','KIB A')->count();              //dibulatkan dan mengambil 2 angka di belakang koma

        $cards[] = [
            'name' => 'Aset Tanah',
            'not_active' => $not_active,
            'active' => $active,
            // 'percent' => $pembelian_percent,
        ];

        $not_active = Aset::where('status','notactive')->where('type','KIB B')->count();
        $active = Aset::where('status', 'active')->where('type','KIB B')->count();              //dibulatkan dan mengambil 2 angka di belakang koma

        $cards[] = [
            'name' => 'Aset Peralatan Mesin',
            'not_active' => $not_active,
            'active' => $active,
            // 'percent' => $pembelian_percent,
        ];

        $not_active = Aset::where('status','notactive')->where('type','KIB C')->count();
        $active = Aset::where('status', 'active')->where('type','KIB C')->count();              //dibulatkan dan mengambil 2 angka di belakang koma

        $cards[] = [
            'name' => 'Aset Gedung Bangunan',
            'not_active' => $not_active,
            'active' => $active,
            // 'percent' => $pembelian_percent,
        ];

        $not_active = Aset::where('status', 'notactive')->where('type','KIB D')->count();
        $active = Aset::where('status', 'active')->where('type','KIB D')->count();              //dibulatkan dan mengambil 2 angka di belakang koma

        $cards[] = [
            'name' => 'Aset Jalan Irigasi Jaringan',
            'not_active' => $not_active,
            'active' => $active,
            // 'percent' => $pembelian_percent,
        ];

        $not_active = Aset::where('status', 'notactive')->where('type','KIB E')->count();
        $active = Aset::where('status', 'active')->where('type','KIB E')->count();              //dibulatkan dan mengambil 2 angka di belakang koma

        $cards[] = [
            'name' => 'Aset Tetap Lainya',
            'not_active' => $not_active,
            'active' => $active,
            // 'percent' => $pembelian_percent,
        ];

        $not_active = Aset::where('status', 'notactive')->where('type','KIB F')->count();
        $active = Aset::where('status', 'active')->where('type','KIB F')->count();              //dibulatkan dan mengambil 2 angka di belakang koma

        $cards[] = [
            'name' => 'Aset Kontruksi Pembangunan',
            'not_active' => $not_active,
            'active' => $active,
            // 'percent' => $pembelian_percent,
        ];

        // data dijadikan bentuk json 
        return response()->json(
            [
                'data' => $cards
            ]
        );
    }



    public function chartFinding(Request $request)
    {
        $request->merge(['year_start' => $request->finding_start ?? date('Y') - 10]);
        $request->merge(['year_end' => $request->finding_end ?? date('Y')]);

        $years = range($request->year_start, $request->year_end);
        $object = $this->getObject($request->finding_object);
        $data = KkaSampleDetail::countFindingForDashboard($years, $object);
        // $title = 'Temuan '.$object['object_name'].' '.$request->year_start.'/'.$request->year_end;
        $title = '  ';

        $series = [];
        foreach ($data as $key => $value) {
            $series[] = [
                'name'  => $key,
                'type'  => $key === 'total' ? 'area': 'column',
                'data'  => $value,
            ];
        }
        // dd(
        //     189,
        //     $request->all(),
        //     $data
        // );

        return [
            'title' => ['text' => $title],
            'series' => $series,
            'xaxis' => ['categories' => $years]
        ];
    }

    public function chartFollowup(Request $request)
    {
        $request->merge(['year_start' => $request->followup_start ?? date('Y') - 10]);
        $request->merge(['year_end' => $request->followup_end ?? date('Y')]);

        $years = range($request->year_start, $request->year_end);
        $object = $this->getObject($request->followup_object);
        $data = KkaSampleDetail::countFollowupForDashboard($years, $object);
        // $title = 'Tindak Lanjut Temuan '.$object['object_name'].' '.$request->year_start.'/'.$request->year_end;
        $title = '  ';

        return [
            'title' => ['text' => $title],
            'series' => [
                [
                    'name' => 'Total',
                    'type' => 'area',
                    'data' => $data['total']
                ],[
                    'name' => 'Open',
                    'type' => 'column',
                    'data' => $data['open']
                ],[
                    'name' => 'Close',
                    'type' => 'column',
                    'data' => $data['close']
                ],
            ],
            'xaxis' => ['categories' => $years]
        ];
    }

    public function chartStage(Request $request)
    {
        $request->merge(['year' => $request->stage_year ?? date('Y')]);
        $object = $this->getObject($request->stage_object);

        $categories = [];
        $total = [];
        $completed = [];
        $progress = [];

        // Surat Penugasan
        $categories[] = 'Surat Penugasan';
        $total[] = Summary::gridAssignment()->chartStage($object)->count();
        $completed[] = Summary::gridAssignment()->chartStage($object, 'assignment')->count();
        $progress[] = (end($total) - end($completed));

        // Permintaan Dokumen
        $categories[] = 'Permintaan Dokumen';
        $total[] = Summary::gridDocReq()->chartStage($object)->count();
        $completed[] = Summary::gridDocReq()->chartStage($object, 'document')->count();
        $progress[] = (end($total) - end($completed));

        // Pemenuhan Dokumen
        $categories[] = 'Pemenuhan Dokumen';
        $total[] = Summary::gridDocFull()->chartStage($object)->count();
        $completed[] = Summary::gridDocFull()
                                ->chartStage($object)
                                ->whereHas('document', function ($q) {
                                    $q->whereHas('docFull', function ($qq) {
                                        $qq->where('status', 'completed');
                                    });
                                })
                                ->count();
        $progress[] = (end($total) - end($completed));

        // APM
        $categories[] = 'APM';
        $total[] = Summary::gridApm()->chartStage($object)->count();
        $completed[] = Summary::gridApm()->chartStage($object, 'apm')->count();
        $progress[] = (end($total) - end($completed));

        // Biaya Penugasan
        $categories[] = 'Biaya Penugasan';
        $total[] = Summary::gridFee()->chartStage($object)->count();
        $completed[] = Summary::gridFee()->chartStage($object, 'fee')->count();
        $progress[] = (end($total) - end($completed));

        // Opening Meeting
        $categories[] = 'Opening Meeting';
        $total[] = Summary::gridOpening()->chartStage($object)->count();
        $completed[] = Summary::gridOpening()->chartStage($object, 'opening')->count();
        $progress[] = (end($total) - end($completed));

        // Kertas Kerja
        $categories[] = 'Kertas Kerja';
        $total[] = Summary::gridSample()->chartStage($object)->count();
        $completed[] = Summary::gridSample()->chartStage($object, 'sample')->count();
        $progress[] = (end($total) - end($completed));

        // Feedback
        $categories[] = 'Feedback';
        $total[] = Summary::gridFeedback()->chartStage($object)->count();
        $completed[] = Summary::gridFeedback()->chartStage($object, 'feedback')->count();
        $progress[] = (end($total) - end($completed));

        // Audit Worksheet
        $categories[] = 'Audit Worksheet';
        $total[] = Summary::gridWorksheet()->chartStage($object)->count();
        $completed[] = Summary::gridWorksheet()->chartStage($object, 'worksheet')->count();
        $progress[] = (end($total) - end($completed));

        // Closing Meeting
        $categories[] = 'Closing Meeting';
        $total[] = Summary::gridClosing()->chartStage($object)->count();
        $completed[] = Summary::gridClosing()->chartStage($object, 'closing')->count();
        $progress[] = (end($total) - end($completed));

        // Exit Meeting
        $categories[] = 'Exit Meeting';
        $total[] = Summary::gridExiting()->chartStage($object)->count();
        $completed[] = Summary::gridExiting()->chartStage($object, 'exiting')->count();
        $progress[] = (end($total) - end($completed));

        // LHA
        $categories[] = 'LHA';
        $total[] = Summary::gridLha()->chartStage($object)->count();
        $completed[] = Summary::gridLha()->chartStage($object, 'lha')->count();
        $progress[] = (end($total) - end($completed));

        // Register Tindak Lanjut
        $categories[] = 'Register Tindak Lanjut';
        $total[] = Summary::gridFollowupReg()->chartStage($object)->count();
        $completed[] = Summary::gridFollowupReg()->chartStage($object, 'followupReg')->count();
        $progress[] = (end($total) - end($completed));

        // Monitoring Tindak Lanjut
        $categories[] = 'Monitoring Tindak Lanjut';
        $total[] = Summary::gridFollowupMonitor()->chartStage($object)->count();
        $completed[] = Summary::gridFollowupMonitor()->chartStage($object, 'followupMonitor')->count();
        $progress[] = (end($total) - end($completed));

        // Survey Kepuasan Audit
        $categories[] = 'Survey Kepuasan Audit';
        $total[] = Summary::gridSurveyRecap()->chartStage($object)->count();
        $completed[] = Summary::gridSurveyRecap()->chartStage($object, 'surveyReg')->count();
        $progress[] = (end($total) - end($completed));

        return [
            'title' => ['text' => 'Tahap Audit '.$object['object_name'].' '.$request->year],
            'series' => [
                [
                    'name' => 'Total',
                    'type' => 'area',
                    'data' => $total
                ],[
                    'name' => 'On Progress',
                    'type' => 'column',
                    'data' => $progress
                ],[
                    'name' => 'Completed',
                    'type' => 'column',
                    'data' => $completed
                ],
            ],
            'xaxis' => ['categories' => $categories]
        ];
    }

    public function getObject($object = null)
    {
        $object = ($object != 'all') ? $object : null;
        $obj = [
            'categories' => [],
            'object_id' => 0,
            'ict_object_id' => 0,
            'object_name' => '',
        ];
        if ($object) {
            // format object = struct--id_of_org_structs || ict--id_of_ref_ict_objects
            $object = explode('--', $object);
            if ($object[0] == 'struct') {
                $obj['categories'] = ['operation','special'];
                $obj['object_id'] = (int) $object[1];
                $obj['object_name'] = OrgStruct::find($obj['object_id'])->name ?? '';
            }
            if ($object[0] == 'ict') {
                $obj['categories'] = ['ict'];
                $obj['ict_object_id'] = (int) $object[1];
                $obj['object_name'] = IctObject::find($obj['ict_object_id'])->name ?? '';
            }
        }

        return $obj;
    }


    public function chartAset(Request $request)
    {
        $request->merge(['year' => $request->stage_year ?? date('Y')]);

        // ['year' => $request->stage_year ?? date('Y')]: Ini adalah array yang berisi elemen 'year' yang akan digabungkan (merge) dengan data dalam permintaan ($request). 'year' adalah kunci (key) dalam array yang akan digunakan untuk menyimpan nilai.

        // $request->stage_year: Merupakan cara untuk mengakses nilai dari elemen 'stage_year' dalam objek permintaan ($request). Jadi, ini mencoba untuk mendapatkan nilai 'stage_year' dari permintaan.

        // ??: Ini adalah operator null coalescing. Jika nilai sebelumnya ($request->stage_year) adalah null atau tidak terdefinisi, maka nilai setelah operator ?? (date('Y')) akan digunakan sebagai nilai alternatif.

        // date('Y'): Ini adalah fungsi PHP yang mengembalikan tahun saat ini dalam format 4 digit. Misalnya, jika sekarang tahun 2023, maka fungsi ini akan mengembalikan string '2023'.

        $year = $request->stage_year;
        $title = '';

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $KIBA = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB A')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $KIBB = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB B')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $KIBC = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB C')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $KIBD = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB D')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $KIBE = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB E')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $KIBF = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB F')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $temp_data['KIBA'] = array_fill(0, 12, 0);
        $temp_data['KIBB'] = array_fill(0, 12, 0);
        $temp_data['KIBC'] = array_fill(0, 12, 0);
        $temp_data['KIBD'] = array_fill(0, 12, 0);
        $temp_data['KIBE'] = array_fill(0, 12, 0);
        $temp_data['KIBF'] = array_fill(0, 12, 0);

        foreach ($KIBA as $row) {
            $temp_data['KIBA'][$row->month-1] = $row->total_completed;
        }


        foreach ($KIBB as $row) {
            $temp_data['KIBB'][$row->month-1] = $row->total_not_completed;
        }

        foreach ($KIBC as $row) {
            $temp_data['KIBC'][$row->month-1] = $row->total_completed;
        }


        foreach ($KIBD as $row) {
            $temp_data['KIBD'][$row->month-1] = $row->total_not_completed;
        }

        foreach ($KIBE as $row) {
            $temp_data['KIBE'][$row->month-1] = $row->total_completed;
        }


        foreach ($KIBF as $row) {
            $temp_data['KIBF'][$row->month-1] = $row->total_not_completed;
        }


        return [
            'title' => ['text' => $title],
            'series' => [
                [
                    'name' => 'Aset KIB A',
                    'type' => 'column',
                    'data' => $temp_data['KIBA'],
                ],
                [
                    'name' => 'Aset KIB B',
                    'type' => 'column',
                    'data' => $temp_data['KIBB'],
                ],
                [
                    'name' => 'Aset KIB C',
                    'type' => 'column',
                    'data' => $temp_data['KIBC'],
                ],
                [
                    'name' => 'Aset KIB D',
                    'type' => 'column',
                    'data' => $temp_data['KIBD'],
                ],
                [
                    'name' => 'Aset KIB E',
                    'type' => 'column',
                    'data' => $temp_data['KIBE'],
                ],
                [
                    'name' => 'Aset KIB F',
                    'type' => 'column',
                    'data' => $temp_data['KIBF'],
                ],
            ],
            'xaxis' => ['categories' => $months],
            'colors' => ['#28C76F', '#F64E60','#28A58F','#91176F','#12789F','#A8C720'],
        ];
    }


    public function chartAsetKIBA(Request $request)
    {
        $request->merge(['year' => $request->stage_year ?? date('Y')]);
        $year = $request->stage_year;
        $title = '';

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $KIBA1 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB B')->where('status','actives')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $KIBA2 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_not_completed')
        ->where('type', 'KIB B')
        ->where('status', 'notactive')
        ->whereHas('asets', function ($q) use ($year) {
            $q->whereYear('updated_at', $year);
        })
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // $KIBA2 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_not_completed')
        // ->where('type', 'KIB B')->where('status','notactive')
        // ->whereYear('book_date', $year)
        // ->groupBy('month')
        // ->orderBy('month')
        // ->get();

        $temp_data['KIBA1'] = array_fill(0, 12, 0);
        $temp_data['KIBA2'] = array_fill(0, 12, 0);

        foreach ($KIBA1 as $row) {
            $temp_data['KIBA1'][$row->month-1] = $row->total_completed;
        }

        foreach ($KIBA2 as $row) {
            $temp_data['KIBA2'][$row->month-1] = $row->total_not_completed;
        }

        return [
            'title' => ['text' => $title],
            'series' => [
                [
                    'name' => 'Aset KIB A Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBA1'],
                ],
                [
                    'name' => 'Aset KIB A Not Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBA2'],
                ],

            ],
            'xaxis' => ['categories' => $months],
            'colors' => ['#28C76F', '#F64E60'],
        ];
    }


    public function chartAsetKIBB(Request $request)
    {
        $request->merge(['year' => $request->stage_year ?? date('Y')]);
        $year = $request->stage_year;
        $title = '';

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $KIBB1 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB B')->where('status','actives')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        $KIBB2 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_not_completed')
        ->where('type', 'KIB B')
        ->where('status', 'notactive')
        ->whereHas('asets', function ($q) use ($year) {
            $q->whereYear('updated_at', $year);
        })
        ->groupBy('month')
        ->orderBy('month')
        ->get();


        $temp_data['KIBB1'] = array_fill(0, 12, 0);
        $temp_data['KIBB2'] = array_fill(0, 12, 0);

        foreach ($KIBB1 as $row) {
            $temp_data['KIBB1'][$row->month-1] = $row->total_completed;
        }


        foreach ($KIBB2 as $row) {
            $temp_data['KIBB2'][$row->month-1] = $row->total_not_completed;
        }

        return [
            'title' => ['text' => $title],
            'series' => [
                [
                    'name' => 'Aset KIB B Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBB1'],
                ],
                [
                    'name' => 'Aset KIB B Not Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBB2'],
                ],

            ],
            'xaxis' => ['categories' => $months],
            'colors' => ['#28C76F', '#F64E60'],
        ];
    }

    public function chartAsetKIBC(Request $request)
    {
        $request->merge(['year' => $request->stage_year ?? date('Y')]);
        $year = $request->stage_year;
        $title = '';

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $KIBA1 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB C')->where('status','active')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $KIBA2 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_not_completed')
        ->where('type', 'KIB C')->where('status','notactive')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $temp_data['KIBA1'] = array_fill(0, 12, 0);
        $temp_data['KIBA2'] = array_fill(0, 12, 0);

        foreach ($KIBA1 as $row) {
            $temp_data['KIBA1'][$row->month-1] = $row->total_completed;
        }


        foreach ($KIBA2 as $row) {
            $temp_data['KIBA2'][$row->month-1] = $row->total_not_completed;
        }

        return [
            'title' => ['text' => $title],
            'series' => [
                [
                    'name' => 'Aset KIB C Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBA1'],
                ],
                [
                    'name' => 'Aset KIB C Not Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBA2'],
                ],

            ],
            'xaxis' => ['categories' => $months],
            'colors' => ['#28C76F', '#F64E60'],
        ];
    }


    public function chartAsetKIBD(Request $request)
    {
        $request->merge(['year' => $request->stage_year ?? date('Y')]);
        $year = $request->stage_year;
        $title = '';

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $KIBA1 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB D')->where('status','active')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $KIBA2 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_not_completed')
        ->where('type', 'KIB D')->where('status','notactive')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $temp_data['KIBA1'] = array_fill(0, 12, 0);
        $temp_data['KIBA2'] = array_fill(0, 12, 0);

        foreach ($KIBA1 as $row) {
            $temp_data['KIBA1'][$row->month-1] = $row->total_completed;
        }


        foreach ($KIBA2 as $row) {
            $temp_data['KIBA2'][$row->month-1] = $row->total_not_completed;
        }

        return [
            'title' => ['text' => $title],
            'series' => [
                [
                    'name' => 'Aset KIB D Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBA1'],
                ],
                [
                    'name' => 'Aset KIB D Not Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBA2'],
                ],

            ],
            'xaxis' => ['categories' => $months],
            'colors' => ['#28C76F', '#F64E60'],
        ];
    }


    public function chartAsetKIBE(Request $request)
    {
        $request->merge(['year' => $request->stage_year ?? date('Y')]);
        $year = $request->stage_year;
        $title = '';

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $KIBA1 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB E')->where('status','active')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $KIBA2 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_not_completed')
        ->where('type', 'KIB E')->where('status','notactive')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $temp_data['KIBA1'] = array_fill(0, 12, 0);
        $temp_data['KIBA2'] = array_fill(0, 12, 0);

        foreach ($KIBA1 as $row) {
            $temp_data['KIBA1'][$row->month-1] = $row->total_completed;
        }


        foreach ($KIBA2 as $row) {
            $temp_data['KIBA2'][$row->month-1] = $row->total_not_completed;
        }

        return [
            'title' => ['text' => $title],
            'series' => [
                [
                    'name' => 'Aset KIB E Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBA1'],
                ],
                [
                    'name' => 'Aset KIB E Not Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBA2'],
                ],

            ],
            'xaxis' => ['categories' => $months],
            'colors' => ['#28C76F', '#F64E60'],
        ];
    }

    public function chartAsetKIBF(Request $request)
    {
        $request->merge(['year' => $request->stage_year ?? date('Y')]);
        $year = $request->stage_year;
        $title = '';

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $KIBA1 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_completed')
        ->where('type', 'KIB F')->where('status','active')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $KIBA2 = Aset::selectRaw('MONTH(book_date) as month, COUNT(*) as total_not_completed')
        ->where('type', 'KIB F')->where('status','notactive')
        ->whereYear('book_date', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $temp_data['KIBA1'] = array_fill(0, 12, 0);
        $temp_data['KIBA2'] = array_fill(0, 12, 0);

        foreach ($KIBA1 as $row) {
            $temp_data['KIBA1'][$row->month-1] = $row->total_completed;
        }


        foreach ($KIBA2 as $row) {
            $temp_data['KIBA2'][$row->month-1] = $row->total_not_completed;
        }

        return [
            'title' => ['text' => $title],
            'series' => [
                [
                    'name' => 'Aset KIB F Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBA1'],
                ],
                [
                    'name' => 'Aset KIB F Not Active',
                    'type' => 'column',
                    'data' => $temp_data['KIBA2'],
                ],

            ],
            'xaxis' => ['categories' => $months],
            'colors' => ['#28C76F', '#F64E60'],
        ];
    }
}
