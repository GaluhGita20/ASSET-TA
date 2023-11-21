<?php

// Example
Route::namespace('Pengajuan')->prefix('pengajuan')->name('pengajuan.')->group(function () {
    
    // pembelian aktiva
    Route::grid('pembelian-aktiva', 'PembelianAktivaController', [
        'with' => ['submit','approval','tracking','print','history'],
    ]);
});