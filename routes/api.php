<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\KIBAController;
use App\Http\Controllers\API\KIBBController;
use App\Http\Controllers\API\KIBCController;
use App\Http\Controllers\API\KIBDController;
use App\Http\Controllers\API\KIBEController;
use App\Http\Controllers\API\KIBFController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// foreach (\File::allFiles(__DIR__ . '/api') as $file) {
//     require $file->getPathname();
// }

Route::middleware(['throttle:60,1'])->group(function() {
    Route::middleware([App\Http\Middleware\api_key::class])->group(function () {
        Route::get('/aset-kiba',[KIBAController::class,'index']);
        Route::get('/aset-kibb',[KIBBController::class,'index']);
        Route::get('/aset-kibc',[KIBCController::class,'index']);
        Route::get('/aset-kibd',[KIBDController::class,'index']);
        Route::get('/aset-kibe',[KIBEController::class,'index']);
        Route::get('/aset-kibf',[KIBFController::class,'index']);

        Route::get('/aset-kiba-detail/{id}',[KIBAController::class,'detail']);
        Route::get('/aset-kibb-detail/{id}',[KIBBController::class,'detail']);
        Route::get('/aset-kibc-detail/{id}',[KIBCController::class,'detail']);
        Route::get('/aset-kibd-detail/{id}',[KIBDController::class,'detail']);
        Route::get('/aset-kibe-detail/{id}',[KIBEController::class,'detail']);
        Route::get('/aset-kibf-detail/{id}',[KIBFController::class,'detail']);
        
    });
});
