<?php

Route::group(['middleware' => 'auth'], function () {
    // Route::POST('/inventory/stock-transfer/import', 'NewUpdated\CycleCountController@importStockTransfer');

    Route::group(['prefix' => 'inventory/cycleCount'], function () {
        Route::get('/setup', 'NewUpdated\CycleCountController@setup');
        Route::get('/getList/{param}/{site}', 'NewUpdated\CycleCountController@getList');
        Route::POST('/storeJob', 'NewUpdated\CycleCountController@storeJob');
        Route::get('/editJob/{id}', 'NewUpdated\CycleCountController@editJob');
        Route::POST('/updateJob', 'NewUpdated\CycleCountController@updateJob')->name('updateJob');
        Route::get('/deleteJob/{id}', 'NewUpdated\CycleCountController@deleteJob');
        Route::get('/getListData/{site_id}/{location}', 'NewUpdated\CycleCountController@getListData');
        Route::get('/countByChecker/{id}', 'NewUpdated\CycleCountController@countByChecker');
        Route::get('/getTransferLokasi/{id}', 'NewUpdated\CycleCountController@getTransferLokasi');
        Route::get('/confirm/{job_no}', 'NewUpdated\CycleCountController@confirm');
        Route::get('/addLocationByChecker/{site_id}/{location}', 'NewUpdated\CycleCountController@addLocationByChecker');

        Route::get('/', 'NewUpdated\CycleCountController@index');
        Route::POST('/store', 'NewUpdated\CycleCountController@store');
        Route::get('/stokTransfer/{id}', 'NewUpdated\CycleCountController@stokTransfer');
        Route::POST('/postStokTransfer', 'NewUpdated\CycleCountController@postStokTransfer');
        Route::get('/monitoring', 'NewUpdated\CycleCountController@monitoring');
        Route::get('/send/{type}', 'NewUpdated\CycleCountController@send');
        Route::get('/cancelHitungan/{id}', 'NewUpdated\CycleCountController@cancelHitungan');
        Route::get('/cariListSKU/{product_code}', 'NewUpdated\CycleCountController@cariListSKU');
        Route::get('/cariData/{tgl_mulai}/{tgl_selesai}', 'NewUpdated\CycleCountController@cariData');

        Route::get('/templateExport/{site}/{type_upload}', 'NewUpdated\CycleCountController@templateExport');
        Route::POST('/import', 'NewUpdated\CycleCountController@import');
        Route::POST('/importByLocation', 'NewUpdated\CycleCountController@importByLocation');
    });
});
