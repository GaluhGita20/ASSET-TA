<?php

use App\Http\cityOptionsontrollers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Followup\FollowupMonitor;
use App\Models\Followup\FollowupRegItem;
use App\Models\Master\Org\OrgStruct;
use Facade\FlareClient\Stacktrace\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/home');
Route::get('lang/change', [Controller::class, 'change'])->name('changeLang');
Auth::routes();
Route::get('logout', [LoginController::class, 'logout']);

Route::middleware('auth')->group(function () {
    Route::get(
        'auth/check',
        function () {
            return response()->json(
                [
                    'data'  => auth()->check()
                ]
            );
        }
    );

    Route::namespace('Dashboard')
        ->group(
            function () {
                Route::get('home', 'DashboardController@index')->name('home');
                Route::post('chartAset', 'DashboardController@chartAset')->name('dashboard.chartAset');
                Route::post('chartAsetKIBA', 'DashboardController@chartAsetKIBA')->name('dashboard.chartAsetKIBA');
                Route::post('chartAsetKIBB', 'DashboardController@chartAsetKIBB')->name('dashboard.chartAsetKIBB');
                Route::post('chartAsetKIBC', 'DashboardController@chartAsetKIBC')->name('dashboard.chartAsetKIBC');
                Route::post('chartAsetKIBD', 'DashboardController@chartAsetKIBD')->name('dashboard.chartAsetKIBD');
                Route::post('chartAsetKIBE', 'DashboardController@chartAsetKIBE')->name('dashboard.chartAsetKIBE');
                Route::post('chartAsetKIBF', 'DashboardController@chartAsetKIBF')->name('dashboard.chartAsetKIBF');
                Route::post('progressAset', 'DashboardController@progressAset')->name('dashboard.progressAset');
                Route::post('progress', 'DashboardController@progress')->name('dashboard.progress');
                Route::post('chartFinding', 'DashboardController@chartFinding')->name('dashboard.chartFinding');
                Route::post('chartFollowup', 'DashboardController@chartFollowup')->name('dashboard.chartFollowup');
                Route::post('chartStage', 'DashboardController@chartStage')->name('dashboard.chartStage');
                Route::get('language/{lang}/setLang', 'DashboardController@setLang')->name('setLang');
            }
        );

    Route::namespace('Pengajuan')
    ->prefix('pengajuan')
    ->name('pengajuan.')
    ->group(
            function () {
                Route::name('perencanaan-aset.')
                ->prefix('perencanaan-aset')
                ->group(
                    function(){
                        Route::post('{record}/updateSummary', 'PerencanaanAsetController@updateSummary')->name('updateSummary');
                        Route::post('{record}/detailGrid', 'PerencanaanAsetController@detailGrid')->name('detailGrid');
                        Route::get('detail/{record}', 'PerencanaanAsetController@detail')->name('detail');
                        Route::get('detailCreate/{record}', 'PerencanaanAsetController@detailCreate')->name('detailCreate');
                        Route::get('detailShow/{detail}', 'PerencanaanAsetController@detailShow')->name('detailShow');
                        Route::get('detailEdit/{detail}', 'PerencanaanAsetController@detailEdit')->name('detailEdit');
                        Route::get('detailEditHarga/{detail}', 'PerencanaanAsetController@detailEditHarga')->name('detailEditHarga');
                        Route::get('detailApprove/{detail}', 'PerencanaanAsetController@detailApprove')->name('detailApprove');
                        Route::get('historyDetail/{detail}', 'PerencanaanAsetController@historyDetail')->name('historyDetail');
                        // Route::get('laporan/{record}', 'PerencanaanAsetController@laporan')->name('laporan');
                        // Route::get('laporanDetail/{detail}', 'PerencanaanAsetController@laporanDetail')->name('laporanDetail');
                        Route::delete('detailDestroy/{detail}', 'PerencanaanAsetController@detailDestroy')->name('detailDestroy');
                        Route::post('detailUpHarga/{detail}', 'PerencanaanAsetController@detailUpHarga')->name('detailUpHarga');
                        Route::post('detailUpdate/{detail}', 'PerencanaanAsetController@detailUpdate')->name('detailUpdate');
                        Route::post('detailUpdateApprove/{detail}', 'PerencanaanAsetController@detailUpApprove')->name('detailUpdateApprove');
                        Route::post('detailStore', 'PerencanaanAsetController@detailStore')->name('detailStore');
                        Route::post('reject/{id}','PerencanaanAsetController@reject')->name('reject');

                    }
                );
                Route::grid('perencanaan-aset', 'PerencanaanAsetController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);
            }
        );
    
        Route::namespace('Transaksi')
        ->prefix('transaksi')
        ->name('transaksi.')
        ->group(
            function () {
                Route::name('waiting-purchase.')
                ->prefix('waiting-purchase')
                ->group(
                    function(){
                        Route::post('store', 'ListPembelianController@submitSave')->name('store');
                        Route::post('storeDetail', 'ListPembelianController@storeDetail')->name('storeDetail');
                        Route::post('detailStore', 'ListPembelianController@detailStore')->name('detailStore');
                        Route::get('showDetail/{detail}', 'ListPembelianController@showDetail')->name('showDetail');
                        Route::get('EditDetail/{detail}', 'ListPembelianController@editDetail')->name('editDetail');
                        Route::post('detailUpdate/{detail}', 'ListPembelianController@detailUpdate')->name('detailUpdate');
                        Route::delete('destroyDetail/{detail}', 'ListPembelianController@destroyDetail')->name('destroyDetail');
                        //transaksi.waiting-purchase.detailStore
                      //  Route::get('{id}', 'ListPembelianController@editDetail')->name('editDetail');
                        // Route::get('/create/{data}', 'ListPembelianController@create')->name('create');
                    }
                );
                Route::delete('waiting-purchase/detailDestroy/{detail}', 'ListPembelianController@detailDestroy')->name('waiting-purchase.detailDestroy');
                Route::grid('waiting-purchase', 'ListPembelianController');
                
                //Route::grid('pengadaan-aset', 'PengadaanAsetController');
                Route::grid('pengadaan-aset', 'PengadaanAsetController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);
                Route::get('pengadaan-aset/editUpdate/{id}', 'PengadaanAsetController@editUpdate')->name('pengadaan-aset.editUpdate');
                Route::post('pengadaan-aset/rejected/{id}', 'PengadaanAsetController@rejected')->name('pengadaan-aset.rejected');
                
            }
        );

    //monitoring
    Route::namespace('Monitoring')
        ->group(
            function () {
                Route::grid(
                    'monitoring',
                    'MonitoringController',
                    [
                        'with' => ['excel', 'history', 'tracking', 'submit', 'approval'],
                        'except' => ['create', 'store']
                    ]
                );
            }
        );

    // Ajax
    Route::prefix('ajax')
        ->name('ajax.')
        ->group(
            function () {
                Route::post('saveTempFiles', 'AjaxController@saveTempFiles')->name('saveTempFiles');
                Route::get('testNotification/{emails}', 'AjaxController@testNotification')->name('testNotification');
                Route::post('userNotification', 'AjaxController@userNotification')->name('userNotification');
                Route::get('userNotification/{notification}/read', 'AjaxController@userNotificationRead')->name('userNotificationRead');
                // Ajax Modules

                Route::get('city-options', 'AjaxController@cityOptions')->name('cityOptions');
                Route::post('penilaian-category', 'AjaxController@penilaianCategoryOptions')->name('penilaianCategoryOptions');
                //Route::post('{search}/cityOptionsRoot', 'AjaxController@cityOptionsRoot')->name('cityOptionsRoot');
                Route::get('jabatan-options', 'AjaxController@jabatanOptions')->name('jabatan-options');
                Route::get('jabatan-options-with-nonpkpt', 'AjaxController@jabatanWithNonPKPTOptions')->name('jabatan-options-with-nonpkpt');
                Route::post('{search}/provinceOptions', 'AjaxController@provinceOptionsBySearch')->name('provinceOptionsBySearch');
                Route::post('selectObject', 'AjaxController@selectObject')->name('selectObject');
                Route::post('{search}/selectRole', 'AjaxController@selectRole')->name('selectRole');
                Route::post('{search}/selectStruct', 'AjaxController@selectStruct')->name('selectStruct');
                Route::get('child-struct-options', 'AjaxController@childStructOptions')->name('child-struct-options');
                Route::post('{search}/selectPosition', 'AjaxController@selectPosition')->name('selectPosition');
                Route::post('{search}/selectUser', 'AjaxController@selectUser')->name('selectUser');
                Route::post('{search}/selectCity', 'AjaxController@selectCity')->name('selectCity');
                Route::post('{search}/selectDistrict', 'AjaxController@selectDistrict')->name('selectDistrict');
                Route::post('{search}/selectProvince', 'AjaxController@selectProvince')->name('selectProvince');
                Route::post('{search}/selectCoa', 'AjaxController@selectCoa')->name('selectCoa');
                Route::post('{search}/selectAsetRS','AjaxController@selectAsetRS')->name('selectAsetRS');
                Route::post('{search}/selectJenisUsaha', 'AjaxController@selectJenisUsaha')->name('selectJenisUsaha');
                Route::post('{search}/selectJenisPengadaan', 'AjaxController@selectJenisPengadaan')->name('selectJenisPengadaan');
                Route::post('{search}/selectRoom', 'AjaxController@selectRoom')->name('selectRoom');
                Route::post('{search}/selectVendor', 'AjaxController@selectVendor')->name('selectVendor');
                Route::post('{search}/selectSSBiaya', 'AjaxController@selectSSBiaya')->name('selectSSBiaya');
                Route::post('{search}/selectDetailUsulan', 'AjaxController@selectDetailUsulan')->name('selectDetailUsulan');
                Route::post('{search}/selectAsetBeli', 'AjaxController@selectAsetBeli')->name('selectAsetBeli');
                //select level struct org
                // selectDetailUsulan
                // Route::post('{search}/selectStruct', 'AjaxController@selectStruct')->name('selectStruct');
            }
        );

    Route::namespace('Laporan')
        ->prefix('laporan')
        ->name('laporan.')
        ->group(
            function () {
                Route::grid('perencanaan-aset', 'LaporanPerencanaanController');
                Route::grid('penerimaan-aset', 'LaporanPenerimaanController');
                Route::get('perencanaan-aset/detailShow/{detail}', 'LaporanPerencanaanController@detailShow')->name('perencanaan-aset.detailShow');
                // Route::get('penerimaan-aset/detailShow/{detail}', 'LaporanPenerimaanController@detailShow')->name('penerimaan-aset.detailShow');
        });

    Route::namespace('Inventaris')
        ->prefix('inventaris')
        ->name('inventaris.')
        ->group(
            function () {
                Route::grid('inventaris-aset','InventarisController');
                Route::grid('kib-b','KIBBController');
                Route::grid('kib-a','KIBAController');
                Route::grid('kib-c','KIBCController');
                Route::grid('kib-d','KIBDController');
                Route::grid('kib-e','KIBEController');
                Route::grid('kib-f','KIBFController');
                Route::get('create/kib-b','KIBBController@createKibB')->name('kib-b.createKibB');
                Route::post('storeDetail', 'InventarisController@storeDetail')->name('inventaris-aset.storeDetail');
                Route::post('storeDetailKibA', 'InventarisController@storeDetailKibA')->name('inventaris-aset.storeDetailKibA');
                Route::post('storeDetailKibB', 'InventarisController@storeDetailKibB')->name('inventaris-aset.storeDetailKibB');
                Route::post('storeDetailKibC', 'InventarisController@storeDetailKibC')->name('inventaris-aset.storeDetailKibC');
                Route::post('storeDetailKibD', 'InventarisController@storeDetailKibD')->name('inventaris-aset.storeDetailKibD');
                Route::post('storeDetailKibE', 'InventarisController@storeDetailKibE')->name('inventaris-aset.storeDetailKibE');
                Route::post('storeDetailKibF', 'InventarisController@storeDetailKibF')->name('inventaris-aset.storeDetailKibF');
                // Route::get('showDetail/{detail}', 'ListPembelianController@showDetail')->name('showDetail');
                Route::get('kib-e/detailShow/{detail}', 'KIBEController@showDetail')->name('kib-e.showDetail');
                Route::get('kib-d/detailShow/{detail}', 'KIBDController@showDetail')->name('kib-d.showDetail');

                Route::get('kib-c/detailShow/{detail}', 'KIBCController@showDetail')->name('kib-C.showDetail');
                Route::get('kib-b/detailShow/{detail}', 'KIBBController@showDetail')->name('kib-B.showDetail');

                Route::get('kib-a/detailShow/{detail}', 'KIACController@showDetail')->name('kib-A.showDetail');
                Route::get('kib-f/detailShow/{detail}', 'KIFBController@showDetail')->name('kib-F.showDetail');
                // Route::post('kib-e/detailShow/{detail}','KIBEController@detailShow')->name('kib-e.detailShow');


            });





    // Setting
    Route::namespace('Setting')
        ->prefix('setting')
        ->name('setting.')
        ->group(
            function () {
                Route::namespace('Role')
                    ->group(
                        function () {
                            Route::get('role/import', 'RoleController@import')->name('role.import');
                            Route::post('role/importSave', 'RoleController@importSave')->name('role.importSave');
                            Route::get('role/{record}/permit', 'RoleController@permit')->name('role.permit');
                            Route::patch('role/{record}/grant', 'RoleController@grant')->name('role.grant');
                            Route::grid('role', 'RoleController');
                        }
                    );
                Route::namespace('Flow')
                    ->group(
                        function () {
                            Route::get('flow/import', 'FlowController@import')->name('flow.import');
                            Route::post('flow/importSave', 'FlowController@importSave')->name('flow.importSave');
                            Route::grid('flow', 'FlowController', ['with' => ['history']]);
                        }
                    );
                Route::namespace('User')
                    ->group(
                        function () {
                            Route::get('user/import', 'UserController@import')->name('user.import');
                            Route::post('user/importSave', 'UserController@importSave')->name('user.importSave');
                            Route::post('user/{record}/resetPassword', 'UserController@resetPassword')->name('user.resetPassword');
                            Route::grid('user', 'UserController');
                            Route::get('user/{record}/detail', 'UserController@detail')->name('user.detail');

                            // Pendidikan
                            Route::get('user/{record}/pendidikan', 'UserController@pendidikan')->name('user.pendidikan');
                            Route::get('user/{record}/pendidikanDetailCreate', 'UserController@pendidikanDetailCreate')->name('user.pendidikan.detailCreate');
                            Route::post('user/{id}/pendidikanDetailStore', 'UserController@pendidikanDetailStore')->name('user.pendidikan.detailStore');
                            Route::get('user/{id}/pendidikanDetailShow', 'UserController@pendidikanDetailShow')->name('user.pendidikan.detailShow');
                            Route::get('user/{id}/pendidikanDetailEdit', 'UserController@pendidikanDetailEdit')->name('user.pendidikan.detailEdit');
                            Route::post('user/{id}/pendidikanDetailUpdate', 'UserController@pendidikanDetailUpdate')->name('user.pendidikan.detailUpdate');
                            Route::delete('user/{id}/pendidikanDetailDestroy', 'UserController@pendidikanDetailDestroy')->name('user.pendidikan.detailDestroy');
                            Route::post('user/{record}/pendidikanGrid', 'UserController@pendidikanGrid')->name('user.pendidikan.grid');

                            // Sertifikasi
                            Route::get('user/{record}/sertifikasi', 'UserController@sertifikasi')->name('user.sertifikasi');
                            Route::get('user/{record}/sertifikasiDetailCreate', 'UserController@sertifikasiDetailCreate')->name('user.sertifikasi.detailCreate');
                            Route::post('user/{id}/sertifikasiDetailStore', 'UserController@sertifikasiDetailStore')->name('user.sertifikasi.detailStore');
                            Route::get('user/{id}/sertifikasiDetailShow', 'UserController@sertifikasiDetailShow')->name('user.sertifikasi.detailShow');
                            Route::get('user/{id}/sertifikasiDetailEdit', 'UserController@sertifikasiDetailEdit')->name('user.sertifikasi.detailEdit');
                            Route::post('user/{id}/sertifikasiDetailUpdate', 'UserController@sertifikasiDetailUpdate')->name('user.sertifikasi.detailUpdate');
                            Route::delete('user/{id}/sertifikasiDetailDestroy', 'UserController@sertifikasiDetailDestroy')->name('user.sertifikasi.detailDestroy');
                            Route::post('user/{record}/sertifikasiGrid', 'UserController@sertifikasiGrid')->name('user.sertifikasi.grid');

                            Route::get('profile', 'ProfileController@index')->name('profile.index');
                            Route::post('profile', 'ProfileController@updateProfile')->name('profile.updateProfile');
                            Route::get('profile/notification', 'ProfileController@notification')->name('profile.notification');
                            Route::post('profile/gridNotification', 'ProfileController@gridNotification')->name('profile.gridNotification');
                            Route::get('profile/activity', 'ProfileController@activity')->name('profile.activity');
                            Route::post('profile/gridActivity', 'ProfileController@gridActivity')->name('profile.gridActivity');
                            Route::get('profile/changePassword', 'ProfileController@changePassword')->name('profile.changePassword');
                            Route::post('profile/changePassword', 'ProfileController@updatePassword')->name('profile.updatePassword');
                        }
                    );

                Route::namespace('Activity')
                    ->group(
                        function () {
                            Route::get('activity/export', 'ActivityController@export')->name('activity.export');
                            Route::grid('activity', 'ActivityController');
                        }
                    );
            }
        );

    // Master
    Route::namespace('Master')
        ->prefix('master')
        ->name('master.')
        ->group(
            function () {
                Route::namespace('Org')
                    ->prefix('org')
                    ->name('org.')
                    ->group(
                        function () {
                            Route::grid('root', 'RootController');
                            Route::get('bod/import', 'BodController@import')->name('bod.import');
                            Route::post('bod/importSave', 'BodController@importSave')->name('bod.importSave');
                            Route::grid('bod', 'BodController');

                            Route::get('department/import', 'DepartmentController@import')->name('department.import');
                            Route::post('department/importSave', 'DepartmentController@importSave')->name('department.importSave');
                            Route::grid('department', 'DepartmentController');

                            Route::get('subdepartment/import', 'SubDepartmentController@import')->name('subdepartment.import');
                            Route::post('subdepartment/importSave', 'SubDepartmentController@importSave')->name('subdepartment.importSave');
                            Route::grid('subdepartment', 'SubDepartmentController');

                            Route::get('subsection/import', 'SubSectionController@import')->name('subsection.import');
                            Route::post('subsection/importSave', 'SubSectionController@importSave')->name('subsection.importSave');
                            Route::grid('subsection', 'SubSectionController');

                            Route::get('position/import', 'PositionController@import')->name('position.import');
                            Route::post('position/importSave', 'PositionController@importSave')->name('position.importSave');
                            Route::grid('position', 'PositionController');
                        }
                    );
                Route::namespace('Geografis')
                    ->prefix('geografis')
                    ->name('geografis.')
                    ->group(
                        function () {
                            Route::grid('province', 'ProvinceController');
                            Route::grid('city', 'CityController');
                            Route::grid('district', 'DistrictController');
                        }
                    );
                
                Route::namespace('Coa')
                    ->prefix('coa')
                    ->name('coa.')
                    ->group(
                        function () {
                            Route::grid('tanah', 'CoaTanahController');
                            Route::grid('peralatan-mesin', 'CoaPeralatanController');
                            Route::grid('gedung-bangunan', 'CoaBangunanController');
                            Route::grid('aset-tetap-lainya', 'CoaAsetTetapController');
                            Route::grid('jalan-irigasi-jaringan', 'CoaJalanIrigasiController');
                            Route::grid('kontruksi-pembangunan', 'CoaKontruksiBangunanController');

                            Route::get('getDetailCOA', 'CoaController@getDetailCOA')->name('getDetailCOA');
                        }
                    );

                Route::namespace('Vendor')
                    ->group(
                        function () {
                            Route::grid('vendor', 'VendorController');
                            Route::grid('type-vendor', 'TypeVendorController');
                        }
                    );
                Route::namespace('Dana')
                    ->group(
                        function () {
                            Route::grid('dana', 'SumberDanaController');
                        }
                    );
                Route::namespace('Location')
                    ->group(
                        function () {
                            Route::grid('location', 'LocationController');
                        }
                    );
                Route::namespace('Pemutihan')
                    ->group(
                        function () {
                            Route::grid('data-pemutihan', 'PemutihanController');
                        }
                    );
                Route::namespace('Pengadaan')
                ->group(
                    function () {
                        Route::grid('data-pengadaan', 'PengadaanController');
                    }
                );
                Route::namespace('Aset')
                ->group(
                    function () {
                        Route::grid('data-aset', 'AsetController');
                    }
                );
            }
        );

    // Web Transaction Modules
    foreach (FacadesFile::allFiles(__DIR__ . '/webs') as $file) {
        require $file->getPathname();
    }
});

Route::get(
    'dev/json',
    function () {
        return [
            url('login'),
            yurl('login'),
            route('login'),
            rut('login'),
        ];
    }
);



Route::get(
    'dev/tes-email',
    function (Request $request) {
        \Mail::to(['rusman.pragma@gmail.com'])->send(new \App\Mail\TesMail());
        return $request->all();
    }
);
