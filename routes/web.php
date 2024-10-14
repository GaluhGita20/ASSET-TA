<?php

// use App\Http\cityOptionsontrollers\Auth\LoginController;
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
// Route::get('logout', [LoginController::class, 'logout']);

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

        // namespace ==> mengacu pada lokasi atau direktori kelas controller yang akan digunakan di dalam grup route ini. Pada contoh ini, Pengajuan berarti semua controller yang digunakan di dalam grup route ini berada di dalam namespace App\Http\Controllers\Pengajuan.

        // prefix ==> menambahkan awalan pada setiap URL di dalam grup route ini. Pada contoh ini, semua route di dalam grup ini akan diawali dengan pengajuan pada URL-nya.

        // name ==> memberikan nama dasar atau awalan untuk setiap nama route di dalam grup ini. Ini mempermudah penggunaan route dalam kode dengan memberikan nama yang konsisten.

        // cnth
        // pengajuan.perencanaan-aset.
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


                // pelayanan medik ===============================================================================================

                Route::name('perencanaan-aset-pelayanan.')
                ->prefix('perencanaan-aset-pelayanan')
                ->group(
                    function(){
                        Route::post('{record}/updateSummary', 'PerencanaanAsetPelayananController@updateSummary')->name('updateSummary');
                        Route::post('{record}/detailGrid', 'PerencanaanAsetPelayananController@detailGrid')->name('detailGrid');
                        Route::get('detail/{record}', 'PerencanaanAsetPelayananController@detail')->name('detail');
                        Route::get('detailCreate/{record}', 'PerencanaanAsetPelayananController@detailCreate')->name('detailCreate');
                        Route::get('detailShow/{detail}', 'PerencanaanAsetPelayananController@detailShow')->name('detailShow');
                        Route::get('detailEdit/{detail}', 'PerencanaanAsetPelayananController@detailEdit')->name('detailEdit');
                        Route::get('detailEditHarga/{detail}', 'PerencanaanAsetPelayananController@detailEditHarga')->name('detailEditHarga');
                        Route::get('detailApprove/{detail}', 'PerencanaanAsetPelayananController@detailApprove')->name('detailApprove');
                        Route::get('historyDetail/{detail}', 'PerencanaanAsetPelayananController@historyDetail')->name('historyDetail');
                        Route::delete('detailDestroy/{detail}', 'PerencanaanAsetPelayananController@detailDestroy')->name('detailDestroy');
                        Route::post('detailUpHarga/{detail}', 'PerencanaanAsetPelayananController@detailUpHarga')->name('detailUpHarga');
                        Route::post('detailUpdate/{detail}', 'PerencanaanAsetPelayananController@detailUpdate')->name('detailUpdate');
                        Route::post('detailUpdateApprove/{detail}', 'PerencanaanAsetPelayananController@detailUpApprove')->name('detailUpdateApprove');
                        Route::post('detailStore', 'PerencanaanAsetPelayananController@detailStore')->name('detailStore');
                        Route::post('reject/{id}','PerencanaanAsetPelayananController@reject')->name('reject');

                    }
                );
                
                Route::grid('perencanaan-aset-pelayanan', 'PerencanaanAsetPelayananController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);
                // ===============================================================================================================


                Route::name('perubahan-perencanaan-aset.')
                ->prefix('perubahan-perencanaan-aset')
                ->group(
                    function(){
                        Route::get('{record}/editHarga', 'PerubahanPerencanaanAsetController@editHarga')->name('editHarga');
                        Route::post('{record}/updateSummary', 'PerubahanPerencanaanAsetController@updateSummary')->name('updateSummary');
                        Route::post('{record}/updateHarga', 'PerubahanPerencanaanAsetController@updateHarga')->name('updateHarga');
                        Route::post('reject/{id}','PerubahanPerencanaanAsetController@reject')->name('reject');
                        Route::get('updateSpesifikasi/{record}', 'PerubahanPerencanaanAsetController@updateSpesifikasi')->name('updateSpesifikasi');
                        Route::post('{record}/saveSpesifikasi', 'PerubahanPerencanaanAsetController@saveSpesifikasi')->name('saveSpesifikasi');
                    }
                );

                Route::grid('perubahan-perencanaan-aset', 'PerubahanPerencanaanAsetController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);

                Route::get('perubahan-perencanaan-aset/detail/{record}', 'PerubahanPerencanaanAsetController@detail')->name('perubahan-perencanaan-aset.detail');

                // ====================================
                Route::get('perubahan-usulan-umum/{record}/editHarga', 'PerubahanPerencanaanAsetUmumController@editHarga')->name('perubahan-usulan-umum.editHarga');
                Route::post('perubahan-usulan-umum/{record}/updateSummary', 'PerubahanPerencanaanAsetUmumController@updateSummary')->name('perubahan-usulan-umum.updateSummary');
                Route::post('perubahan-usulan-umum/reject/{id}','PerubahanPerencanaanAsetUmumController@reject')->name('perubahan-usulan-umum.reject');
                Route::get('perubahan-usulan-umum/updateSpesifikasi/{record}', 'PerubahanPerencanaanAsetUmumController@updateSpesifikasi')->name('perubahan-usulan-umum.updateSpesifikasi');
                Route::post('perubahan-usulan-umum/{record}/saveSpesifikasi', 'PerubahanPerencanaanAsetUmumController@saveSpesifikasi')->name('perubahan-usulan-umum.saveSpesifikasi');
                Route::post('perubahan-usulan-umum/{record}/updateHarga', 'PerubahanPerencanaanAsetUmumController@updateHarga')->name('perubahan-usulan-umum.updateHarga');
                Route::grid('perubahan-usulan-umum', 'PerubahanPerencanaanAsetUmumController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);

                Route::get('perubahan-usulan-umum/detail/{record}', 'PerubahanPerencanaanAsetUmumController@detail')->name('perubahan-usulan-umum.detail');

                // ====================================

                Route::post('penghapusan-aset/{record}/updateSummary', 'PenghapusanAsetController@updateSummary')->name('penghapusan-aset.updateSummary');
                Route::get('penghapusan-aset/detail/{record}', 'PenghapusanAsetController@detail')->name('penghapusan-aset.detail');
                Route::grid('penghapusan-aset', 'PenghapusanAsetController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);

                //pemutihan
                // Route::post('pemutihan-aset/{record}/updateSummary', 'PemutihanAsetController@updateSummary')->name('pemutihan-aset.updateSummary');
                Route::post('pemutihan-aset/{record}/updateSummary', 'PemutihanAsetController@updateSummary')->name('pemutihan-aset.updateSummary');
                Route::get('pemutihan-aset/detail/{record}', 'PemutihanAsetController@detail')->name('pemutihan-aset.detail');
                Route::get('pemutihan-aset/{record}/show', 'PemutihanAsetController@show')->name('pemutihan-aset.show');
                Route::post('store', 'PemutihanAsetController@submitSave')->name('pemutihan-aset.store');
                Route::post('pemutihan-aset/rejected/{id}', 'PemutihanAsetController@rejected')->name('pemutihan-aset.rejected');
                Route::grid('pemutihan-aset', 'PemutihanAsetController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);


            }
        );

        Route::namespace('Perbaikan')
        ->prefix('perbaikan')
        ->name('perbaikan.')
        ->group(
            function () {

                Route::get('perbaikan-aset/{record}/detail', 'PerbaikanAsetController@detail')->name('perbaikan-aset.detail');
                Route::post('perbaikan-aset/{record}/updateSummary', 'PerbaikanAsetController@updateSummary')->name('perbaikan-aset.updateSummary');
                Route::grid('perbaikan-aset', 'PerbaikanAsetController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);

                Route::grid('pj-perbaikan', 'PjPerbaikanAsetController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);
                Route::post('pj-perbaikan/{record}/detailGrid', 'PjPerbaikanAsetController@detailGrid')->name('pj-perbaikan.detailGrid');
                Route::get('pj-perbaikan/detail/{record}', 'PjPerbaikanAsetController@detail')->name('pj-perbaikan.detail');
                Route::get('pj-perbaikan/detailShow/{detail}', 'PjPerbaikanAsetController@detailShow')->name('pj-perbaikan.detailShow');

                //trans
                Route::grid('trans-sperpat', 'TransPerbaikanDisposisiController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);

                Route::get('trans-sperpat/detail/{record}', 'TransPerbaikanDisposisiController@detail')->name('trans-sperpat.detail');
                Route::post('trans-sperpat/{record}/detailGrid', 'TransPerbaikanDisposisiController@detailGrid')->name('trans-sperpat.detailGrid');
                Route::get('trans-sperpat/detailShow/{detail}', 'TransPerbaikanDisposisiController@detailShow')->name('trans-sperpat.detailShow');
                // =======================================


                Route::post('detailStore', 'PerbaikanDisposisiController@detailStore')->name('detailStore');
                Route::post('perbaikan-aset/{record}/updateSummary', 'PerbaikanAsetController@updateSummary')->name('perbaikan-aset.updateSummary');
                Route::grid('perbaikan-aset', 'PerbaikanAsetController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);

                Route::get('usulan-sperpat/editHarga/{record}', 'PerbaikanDisposisiController@editHarga')->name('usulan-sperpat.editHarga');
                
                Route::post('usulan-sperpat/{record}/updateHarga', 'PerbaikanDisposisiController@updateHarga')->name('usulan-sperpat.updateHarga');
                
                Route::get('usulan-sperpat/detail/{record}', 'PerbaikanDisposisiController@detail')->name('usulan-sperpat.detail');


                Route::post('usulan-sperpat/detailStore', 'PerbaikanDisposisiController@detailStore')->name('usulan-sperpat.detailStore');
                
                Route::post('usulan-sperpat/detailUpdate/{detail}', 'PerbaikanDisposisiController@detailUpdate')->name('usulan-sperpat.detailUpdate');
                Route::get('usulan-sperpat/detailEdit/{detail}', 'PerbaikanDisposisiController@detailEdit')->name('usulan-sperpat.detailEdit');

                Route::delete('usulan-sperpat/detailDestroy/{detail}', 'PerbaikanDisposisiController@detailDestroy')->name('usulan-sperpat.detailDestroy');
                // Route::post('usulan-sperpat/detailStore', 'PerbaikanDisposisiController@detailStore')->name('usulan-sperpat.detailStore');
                
                Route::get('usulan-sperpat/detailCreate/{record}', 'PerbaikanDisposisiController@detailCreate')->name('usulan-sperpat.detailCreate');
                Route::get('usulan-sperpat/detailShow/{detail}', 'PerbaikanDisposisiController@detailShow')->name('usulan-sperpat.detailShow');
                // Route::get('usulan-sperpat/detailEdit/{detail}', 'PerbaikanDisposisiController@detailEdit')->name('usulan-sperpat.detailEdit');

                Route::delete('usulan-sperpat/detailDestroy/{detail}', 'PerbaikanDisposisiController@detailDestroy')->name('usulan-sperpat.detailDestroy');
                
                Route::post('usulan-sperpat/{record}/detailGrid', 'PerbaikanDisposisiController@detailGrid')->name('usulan-sperpat.detailGrid');
                Route::post('usulan-sperpat/{record}/updateSummary', 'PerbaikanDisposisiController@updateSummary')->name('usulan-sperpat.updateSummary');
                Route::grid('usulan-sperpat', 'PerbaikanDisposisiController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);

                Route::post('usulan-sperpat/detailUpdateHarga/{detail}', 'PerbaikanDisposisiController@detailUpdateHarga')->name('usulan-sperpat.detailUpdateHarga');
                Route::get('usulan-sperpat/{detail}/detailEditHarga', 'PerbaikanDisposisiController@detailEditHarga')->name('usulan-sperpat.detailEditHarga');
        });

        Route::namespace('Pemeliharaan')
        ->prefix('pemeliharaan')
        ->name('pemeliharaan.')
        ->group(
            function () {
                Route::grid('pemeliharaan-aset', 'PemeliharaanAsetController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);
                Route::post('pemeliharaan-aset/rejected/{id}', 'PemeliharaanAsetController@rejected')->name('pemeliharaan-aset.rejected');
                Route::post('pemeliharaan-aset/{record}/updateSummary', 'PemeliharaanAsetController@updateSummary')->name('pemeliharaan-aset.updateSummary');
                Route::post('pemeliharaan-aset/detailUpdate/{detail}', 'PemeliharaanAsetController@detailUpdate')->name('pemeliharaan-aset.detailUpdate');
                // Route::post('pemeliharaan/{record}/updateSummary', 'PemeliharaanAsetController@updateSummary')->name('updateSummary');
                Route::post('pemeliharaan/{record}/detailGrid', 'PemeliharaanAsetController@detailGrid')->name('pemeliharaan-aset.detailGrid');
                Route::get('pemeliharaan-aset/detail/{record}', 'PemeliharaanAsetController@detail')->name('pemeliharaan-aset.detail');
                Route::get('pemeliharaan-aset/detailCreate/{record}', 'PemeliharaanAsetController@detailCreate')->name('pemeliharaan-aset.detailCreate');
                Route::get('pemeliharaan-aset/detailShow/{detail}', 'PemeliharaanAsetController@detailShow')->name('pemeliharaan-aset.detailShow');
                Route::get('pemeliharaan-aset/detailEdit/{detail}', 'PemeliharaanAsetController@detailEdit')->name('pemeliharaan-aset.detailEdit');

                Route::delete('pemeliharaan-aset/detailDestroy/{detail}', 'PemeliharaanAsetController@detailDestroy')->name('pemeliharaan-aset.detailDestroy');
                Route::post('pemeliharaan-aset/detailStore', 'PemeliharaanAsetController@detailStore')->name('pemeliharaan-aset.detailStore');
                
        });
    
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
                
                // /transaksi/non-pengadaan-aset
                Route::post('non-pengadaan-aset/{record}/updateSummary', 'HibahAsetController@updateSummary')->name('non-pengadaan-aset.updateSummary');
                Route::post('non-pengadaan-aset/{record}/detailGrid', 'HibahAsetController@detailGrid')->name('non-pengadaan-aset.detailGrid');
                Route::post('non-pengadaan-aset/detailStore', 'HibahAsetController@detailStore')->name('non-pengadaan-aset.detailStore');
                Route::get('non-pengadaan-aset/detail/{record}', 'HibahAsetController@detail')->name('non-pengadaan-aset.detail');
                Route::get('non-pengadaan-aset/detailCreate/{record}', 'HibahAsetController@detailCreate')->name('non-pengadaan-aset.detailCreate');
                Route::get('non-pengadaan-aset/detailShow/{detail}', 'HibahAsetController@detailShow')->name('non-pengadaan-aset.detailShow');
                Route::get('non-pengadaan-aset/detailEdit/{detail}', 'HibahAsetController@detailEdit')->name('non-pengadaan-aset.detailEdit');
                Route::delete('non-pengadaan-aset/detailDestroy/{detail}', 'HibahAsetController@detailDestroy')->name('non-pengadaan-aset.detailDestroy');
                // Route::post('non-pengadaan-aset/{id}/DetailUpdate', 'HibahAsetController@DetailUpdate')->name('non-pengadaan-aset.detailUpdate');
                Route::post('non-pengadaan-aset/detailUpdate/{detail}', 'HibahAsetController@DetailUpdate')->name('non-pengadaan-aset.detailUpdate');
                Route::grid('non-pengadaan-aset', 'HibahAsetController', [
                    'with' => ['submit', 'reject', 'approval', 'tracking', 'history', 'print'],
                ]);
            }
        );

    //monitoring
    // Route::namespace('Monitoring')
    //     ->group(
    //         function () {
    //             Route::grid(
    //                 'monitoring',
    //                 'MonitoringController',
    //                 [
    //                     'with' => ['excel', 'history', 'tracking', 'submit', 'approval'],
    //                     'except' => ['create', 'store']
    //                 ]
    //             );
    //         }
    //     );

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
                Route::post('selectKib', 'AjaxController@selectKib')->name('selectKib');
                Route::post('selectPerbaikan', 'AjaxController@selectPerbaikan')->name('selectPerbaikan');
                Route::post('getKibById', 'AjaxController@getKibById')->name('getKibById');
                Route::post('getLapRencana2', 'AjaxController@getLapRencana2')->name('getLapRencana2');
                Route::post('getLapRencana1', 'AjaxController@getLapRencana1')->name('getLapRencana1');
                Route::post('getLapPengadaan1', 'AjaxController@getLapPengadaan1')->name('getLapPengadaan1');
                Route::post('getLapAsetKIBB', 'AjaxController@getLapAsetKIBB')->name('getLapAsetKIBB');
                Route::post('getLapPenghapusan', 'AjaxController@getLapPenghapusan')->name('getLapPenghapusan');
                Route::post('getLapPemutihan', 'AjaxController@getLapPemutihan')->name('getLapPemutihan');
                Route::post('getLapPemeliharaan', 'AjaxController@getLapPemeliharaan')->name('getLapPemeliharaan');
                Route::post('getLapHibah', 'AjaxController@getLapHibah')->name('getLapHibah');
                Route::post('getLapPerbaikan', 'AjaxController@getLapPerbaikan')->name('getLapPerbaikan');

                Route::post('getLapSperpat', 'AjaxController@getLapSperpat')->name('getLapSperpat');
                Route::post('getLapPerbaikan', 'AjaxController@getLapPerbaikan')->name('getLapPerbaikan');
                Route::post('getUsulanAsetById', 'AjaxController@getUsulanAsetById')->name('getUsulanAsetById');

                Route::post('select-umum', 'AjaxController@selectUmum')->name('selectUmum');
                Route::post('select-penunjang', 'AjaxController@selectPenunjang')->name('selectPenunjang');
                //Route::get('selectStructPenunjang', 'AjaxController@selectStructPenunjang')->name('selectStructPenunjang');


                Route::post('cekSperpat', 'AjaxController@cekSperpat')->name('cekSperpat');
                Route::post('checkAset', 'AjaxController@checkAset')->name('checkAset');

                //Route::post('{search}/cityOptionsRoot', 'AjaxController@cityOptionsRoot')->name('cityOptionsRoot');
                Route::get('jabatan-options', 'AjaxController@jabatanOptions')->name('jabatan-options');
                Route::get('jabatan-options-with-nonpkpt', 'AjaxController@jabatanWithNonPKPTOptions')->name('jabatan-options-with-nonpkpt');
                Route::post('{search}/provinceOptions', 'AjaxController@provinceOptionsBySearch')->name('provinceOptionsBySearch');
                Route::post('selectObject', 'AjaxController@selectObject')->name('selectObject');
                Route::post('{search}/selectRole', 'AjaxController@selectRole')->name('selectRole');
                Route::post('{search}/selectStruct', 'AjaxController@selectStruct')->name('selectStruct');
                Route::post('selectPemeliharaan', 'AjaxController@selectPemeliharaan')->name('selectPemeliharaan');

                //selectCodePerencanaan
                Route::post('selectCodePerencanaan', 'AjaxController@selectCodePerencanaan')->name('selectCodePerencanaan');
                Route::post('selectCodePerencanaanUmum', 'AjaxController@selectCodePerencanaanUmum')->name('selectCodePerencanaanUmum');
                Route::post('selectUsulanDetail', 'AjaxController@selectUsulanDetail')->name('selectUsulanDetail');

                Route::post('selectPemeliharaan', 'AjaxController@selectPemeliharaan')->name('selectPemeliharaan');


                Route::get('child-struct-options', 'AjaxController@childStructOptions')->name('child-struct-options');
                Route::post('{search}/selectPosition', 'AjaxController@selectPosition')->name('selectPosition');
                Route::post('{search}/selectUser', 'AjaxController@selectUser')->name('selectUser');
                Route::post('{search}/selectDeps', 'AjaxController@selectDeps')->name('selectDeps');
                Route::post('selectDepsRSUD', 'AjaxController@selectDepsRSUD')->name('selectDepsRSUD');
                Route::post('selectDepsBPKAD', 'AjaxController@selectDepsBPKAD')->name('selectDepsBPKAD');
                Route::post('{search}/selectCity', 'AjaxController@selectCity')->name('selectCity');
                Route::post('{search}/selectDistrict', 'AjaxController@selectDistrict')->name('selectDistrict');
                Route::post('{search}/selectProvince', 'AjaxController@selectProvince')->name('selectProvince');
                Route::post('{search}/selectCoa', 'AjaxController@selectCoa')->name('selectCoa');
                Route::post('{search}/selectAsetRS','AjaxController@selectAsetRS')->name('selectAsetRS');
                Route::post('{search}/selectRooms', 'AjaxController@selectRooms')->name('selectRooms');

                Route::post('{search}/selectAsetKib','AjaxController@selectAsetKib')->name('selectAsetKib');

                Route::post('{search}/selectJenisUsaha', 'AjaxController@selectJenisUsaha')->name('selectJenisUsaha');
                Route::post('{search}/selectStatusTanah', 'AjaxController@selectStatusTanah')->name('selectStatusTanah');
                Route::post('{search}/selectHakTanah', 'AjaxController@selectHakTanah')->name('selectHakTanah');
                Route::post('{search}/selectBahanAset', 'AjaxController@selectBahanAset')->name('selectBahanAset');


                Route::post('{search}/selectJenisPengadaan', 'AjaxController@selectJenisPengadaan')->name('selectJenisPengadaan');
                Route::post('{search}/selectJenisPemutihan', 'AjaxController@selectJenisPemutihan')->name('selectJenisPemutihan');
                Route::post('{search}/selectRoom', 'AjaxController@selectRoom')->name('selectRoom');
                Route::post('{search}/selectVendor', 'AjaxController@selectVendor')->name('selectVendor');
                Route::post('{search}/selectSSBiaya', 'AjaxController@selectSSBiaya')->name('selectSSBiaya');
                Route::post('{search}/selectDetailUsulan', 'AjaxController@selectDetailUsulan')->name('selectDetailUsulan');
                Route::post('{search}/selectAsetBeli', 'AjaxController@selectAsetBeli')->name('selectAsetBeli');
                Route::post('{search}/','AjaxController@selectAsetKib')->name('selectAsetKib');
                Route::post('{search}/selectAsetItem','AjaxController@selectAsetItem')->name('selectAsetItem');

                // selectAsetItem
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
                Route::post('{record}/detailGrid','AsetController@detailGrid')->name('detailGrid');
                Route::get('detail/{record}', 'AsetController@detail')->name('detail');
                // Route::grid('AsetController');
                Route::grid('perencanaan-aset', 'LaporanPerencanaanController');
                Route::grid('penghapusan-aset', 'LaporanPenghapusanController');
                Route::grid('penerimaan-aset', 'LaporanPenerimaanController');
                Route::grid('penerimaan-hibah-aset', 'LaporanPenerimaanHibahController');
                Route::grid('pemutihan-aset', 'LaporanPemutihanController');
                Route::grid('pemeliharaan-aset', 'LaporanPemeliharaanController');
                Route::grid('hibah-aset', 'LaporanHibahAsetController');

                Route::grid('perbaikan-sperpat-aset', 'LaporanSperpatController');
                Route::grid('perbaikan-aset', 'LaporanPerbaikanController');
                Route::post('perbaikan-aset/{record}/detailGrid', 'LaporanPerbaikanController@detailGrid')->name('perbaikan-aset.detailGrid');
                Route::get('perbaikan-aset/detailShow/{detail}', 'LaporanPerbaikanController@detailShow')->name('perbaikan-aset.detailShow');
                Route::post('perbaikan-sperpat-aset/{record}/detailGrid', 'LaporanSperpatController@detailGrid')->name('perbaikan-sperpat-aset.detailGrid');
                Route::get('perbaikan-sperpat-aset/detailShow/{detail}', 'LaporanSperpatController@detailShow')->name('perbaikan-sperpat-aset.detailShow');
                Route::get('perencanaan-aset/detailShow/{detail}', 'LaporanPerencanaanController@detailShow')->name('perencanaan-aset.detailShow');
                Route::get('penerimaan-aset/detailShow/{detail}', 'LaporanPenerimaanController@detailShow')->name('penerimaan-aset.detailShow');
                Route::post('penerimaan-aset/{record}/detailGrid', 'LaporanPenerimaanController@detailGrid')->name('penerimaan-aset.detailGrid');
                Route::get('penerimaan-hibah-aset/detailShow/{detail}', 'LaporanPenerimaanHibahController@detailShow')->name('penerimaan-hibah-aset.detailShow');
                Route::post('penerimaan-hibah-aset/{record}/detailGrid', 'LaporanPenerimaanHibahController@detailGrid')->name('penerimaan-hibah-aset.detailGrid');
                Route::get('pemeliharaan-aset/detailShow/{detail}', 'LaporanPemeliharaanController@detailShow')->name('pemeliharaan-aset.detailShow');
                Route::post('pemeliharaan-aset/{record}/detailGrid', 'LaporanPemeliharaanController@detailGrid')->name('pemeliharaan-aset.detailGrid');
                Route::get('penghapusan-aset/detailShow/{detail}', 'LaporanPenghapusanController@detailShow')->name('penghapusan-aset.detailShow');
                Route::get('pemutihan-aset/detailShow/{detail}', 'LaporanPemutihanController@detailShow')->name('pemutihan-aset.detailShow');


                Route::namespace('Inventaris')
                ->prefix('inventaris')
                ->name('inventaris.')
                ->group( function() {
                    Route::name('kib-a.')
                    ->prefix('kib-a')
                    ->group(function() {  
                        Route::post('{record}/detailsGrid','KIBAController@detailsGrid')->name('detailsGrid');
                        Route::get('details/{record}', 'KIBAController@details')->name('details');
                        Route::post('{record}/detailpGrid','KIBAController@detailpGrid')->name('detailpGrid');
                        Route::get('detailp/{record}', 'KIBAController@detailp')->name('detailp');
                    });
                    Route::name('kib-b.')
                    ->prefix('kib-b')
                    ->group(function() { 
                        Route::post('{record}/detailsGrid','KIBBController@detailsGrid')->name('detailsGrid');
                        Route::get('details/{record}', 'KIBBController@details')->name('details');
                        Route::post('{record}/detailpGrid','KIBBController@detailpGrid')->name('detailpGrid');
                        Route::get('detailp/{record}', 'KIBBController@detailp')->name('detailp');
                    });
                    Route::name('kib-c.')
                    ->prefix('kib-c')
                    ->group(function() { 
                        Route::post('{record}/detailsGrid','KIBCController@detailsGrid')->name('detailsGrid');
                        Route::get('details/{record}', 'KIBCController@details')->name('details');
                        Route::post('{record}/detailpGrid','KIBCController@detailpGrid')->name('detailpGrid');
                        Route::get('detailp/{record}', 'KIBCController@detailp')->name('detailp');
                    });
                    Route::name('kib-d.')
                    ->prefix('kib-d')
                    ->group(function() { 
                        Route::post('{record}/detailsGrid','KIBDController@detailsGrid')->name('detailsGrid');
                        Route::get('details/{record}', 'KIBDController@details')->name('details');
                        Route::post('{record}/detailpGrid','KIBDController@detailpGrid')->name('detailpGrid');
                        Route::get('detailp/{record}', 'KIBDController@detailp')->name('detailp');
                    });
                    Route::name('kib-e.')
                    ->prefix('kib-e')
                    ->group(function() { 
                        Route::post('{record}/detailsGrid','KIBEController@detailsGrid')->name('detailsGrid');
                        Route::get('details/{record}', 'KIBEController@details')->name('details');
                        Route::post('{record}/detailpGrid','KIBEController@detailpGrid')->name('detailpGrid');
                        Route::get('detailp/{record}', 'KIBEController@detailp')->name('detailp');
                    });

                    Route::name('kib-f.')
                    ->prefix('kib-f')
                    ->group(function() { 
                        Route::post('{record}/detailsGrid','KIBFController@detailsGrid')->name('detailsGrid');
                        Route::get('details/{record}', 'KIBFController@details')->name('details');
                        Route::post('{record}/detailpGrid','KIBFController@detailpGrid')->name('detailpGrid');
                        Route::get('detailp/{record}', 'KIBFController@detailp')->name('detailp'); 
                    });
                    Route::grid('kib-a','KIBAController');
                    Route::grid('kib-b','KIBBController');
                    Route::grid('kib-c','KIBCController');
                    Route::grid('kib-d','KIBDController');
                    Route::grid('kib-e','KIBEController');
                    Route::grid('kib-f','KIBFController');
                });
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

                Route::get('kib-a/detailShow/{detail}', 'KIBAController@showDetail')->name('kib-A.showDetail');
                Route::get('kib-f/detailShow/{detail}', 'KIBFController@showDetail')->name('kib-F.showDetail');

                //repair
                Route::get('kib-b/repair/{record}', 'KIBBController@repair')->name('kib-b.repair');
                Route::get('kib-c/repair/{record}', 'KIBCController@repair')->name('kib-c.repair');
                Route::get('kib-d/repair/{record}', 'KIBDController@repair')->name('kib-d.repair');
                Route::get('kib-e/repair/{record}', 'KIBEController@repair')->name('kib-e.repair');
                Route::get('kib-f/repair/{record}', 'KIBFController@repair')->name('kib-f.repair');

                //deletes 
                Route::get('kib-a/deletes/{record}', 'KIBAController@deletes')->name('kib-a.deletes');
                Route::get('kib-b/deletes/{record}', 'KIBBController@deletes')->name('kib-b.deletes');
                Route::get('kib-c/deletes/{record}', 'KIBCController@deletes')->name('kib-c.deletes');
                Route::get('kib-d/deletes/{record}', 'KIBDController@deletes')->name('kib-d.deletes');
                Route::get('kib-e/deletes/{record}', 'KIBEController@deletes')->name('kib-e.deletes');
                Route::get('kib-f/deletes/{record}', 'KIBFController@deletes')->name('kib-f.deletes');

                //pemutihan
                // Route::post('trans-sperpat/{record}/detailGrid', 'TransPerbaikanDisposisiController@detailGrid')->name('trans-sperpat.detailGrid');
                // Route::get('trans-sperpat/detailShow/{detail}', 'TransPerbaikanDisposisiController@detailShow')->name('trans-sperpat.detailShow');
                Route::post('kib-b/{record}/detailsGrid','KIBBController@detailsGrid')->name('kib-b.detailsGrid');
                Route::get('kib-b/details/{record}', 'KIBBController@details')->name('kib-b.details');

                Route::post('kib-b/{record}/detailpGrid','KIBBController@detailpGrid')->name('kib-b.detailpGrid');
                Route::get('kib-b/detailp/{record}', 'KIBBController@detailp')->name('kib-b.detailp');

                Route::get('inventaris/kib-a/export','KIBAController@export')->name('kib-a.export');
                Route::get('inventaris/kib-b/export','KIBBController@export')->name('kib-b.export');
                Route::get('inventaris/kib-c/export','KIBCController@export')->name('kib-c.export');
                Route::get('inventaris/kib-d/export','KIBDController@export')->name('kib-d.export');
                Route::get('inventaris/kib-e/export','KIBEController@export')->name('kib-e.export');
                Route::get('inventaris/kib-f/export','KIBFController@export')->name('kib-f.export');

                Route::get('inventaris/kib-a/kib-pdf','KIBAController@print')->name('kib-a.kib-pdf');
                Route::get('inventaris/kib-b/kib-pdf','KIBBController@print')->name('kib-b.kib-pdf');
                Route::get('inventaris/kib-c/kib-pdf','KIBCController@print')->name('kib-c.kib-pdf');
                Route::get('inventaris/kib-d/kib-pdf','KIBDController@print')->name('kib-d.kib-pdf');
                Route::get('inventaris/kib-e/kib-pdf','KIBEController@print')->name('kib-e.kib-pdf');
                Route::get('inventaris/kib-f/kib-pdf','KIBFController@print')->name('kib-f.kib-pdf');

                Route::get('inventaris/kib-b/kir-pdf','KIBBController@printKIR')->name('kib-b.kir-pdf');
                Route::get('inventaris/kib-e/kir-pdf','KIBEController@printKIR')->name('kib-e.kir-pdf');

                // Route::get('kib-a/pemutihan/{record}', 'KIBAController@pemutihan')->name('kib-a.pemutihan');
                // Route::get('kib-b/pemutihan/{record}', 'KIBBController@pemutihan')->name('kib-b.pemutihan');
                // Route::get('kib-c/pemutihan/{record}', 'KIBCController@pemutihan')->name('kib-c.pemutihan');
                // Route::get('kib-d/pemutihan/{record}', 'KIBDController@pemutihan')->name('kib-d.pemutihan');
                // Route::get('kib-e/pemutihan/{record}', 'KIBEController@pemutihan')->name('kib-e.pemutihan');
                // Route::get('kib-f/pemutihan/{record}', 'KIBFController@pemutihan')->name('kib-f.pemutihan');
            }
        );

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
                            // Route::get('/coa', 'CoaTanahController@index')->name('index');

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
                Route::namespace('BahanAset')
                ->group(
                    function () {
                        Route::grid('bahanAset', 'BahanAsetController');
                    }
                );
                Route::namespace('HakTanah')
                ->group(
                    function () {
                        Route::grid('hakTanah', 'HakTanahController');
                    }
                );
                Route::namespace('StatusTanah')
                ->group(
                    function () {
                        Route::grid('statusTanah', 'StatusTanahController');
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
    // foreach (\File::allFiles(__DIR__ . '/webs') as $file) {
    //     require $file->getPathname();
    // }
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
        \Mail::to(['teguharthana@gmail.com'])->send(new \App\Mail\TesMail());
        return $request->all();
    }
);

//test user sistem
// sri (staf)
// umam (kepala) IGD
// encu (kepala) pelayanan medik
// hengki (program perencanaan dan pelaporan)
// sumardi (keuangan)
// suasa (direktur)

// master data
// hak tanah
// status tanah
// bahan


//aset yang ditaruh di tempat halaman parkir
// data double Kepala Seksi Sarana dan Prasarana Logistik belum ada

// jabatan kepala badan
// jabatan filter direksi muncul dua kali di data master jabatan


//sumber pendanaan usulan sperpat aset

