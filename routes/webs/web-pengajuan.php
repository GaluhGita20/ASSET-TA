<?php

// Example
Route::namespace('Pengajuan')->prefix('pengajuan')->name('pengajuan.')->group(function () {
    
    // pembelian aset
    Route::grid('pembelian-aset', 'PembelianAsetController', [
        'with' => ['submit','approval','tracking','print','history'],
    ]);
    Route::post('pembelian-aset/{record}/updateSummary', 'PembelianAsetController@updateSummary')->name('pembelian-aset.updateSummary');
    // detail
    Route::get('pembelian-aset/{record}/detail', 'PembelianAsetController@detail')->name('pembelian-aset.detail');
    Route::post('pembelian-aset/{record}/detailGrid', 'PembelianAsetController@detailGrid')->name('pembelian-aset.detailGrid');
    Route::get('pembelian-aset/{record}/detailCreate', 'PembelianAsetController@detailCreate')->name('pembelian-aset.detailCreate');
    Route::post('pembelian-aset/{record}/detailStore', 'PembelianAsetController@detailStore')->name('pembelian-aset.detailStore');
    Route::get('pembelian-aset/{detail}/detailShow', 'PembelianAsetController@detailShow')->name('pembelian-aset.detailShow');
    Route::get('pembelian-aset/{detail}/detailEdit', 'PembelianAsetController@detailEdit')->name('pembelian-aset.detailEdit');
    Route::patch('pembelian-aset/{detail}/detailUpdate', 'PembelianAsetController@detailUpdate')->name('pembelian-aset.detailUpdate');
    Route::delete('pembelian-aset/{detail}/detailDestroy', 'PembelianAsetController@detailDestroy')->name('pembelian-aset.detailDestroy');
});