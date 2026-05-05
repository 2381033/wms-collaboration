<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'export/ScanCargoEkspor'], function () {
        Route::get('/', 'NewUpdated\ScanCargoEksporController@index');
        Route::POST('/storeHeader', 'NewUpdated\ScanCargoEksporController@storeHeader');
        Route::get('/detailJob/{job_no}', 'NewUpdated\ScanCargoEksporController@detailJob');
        Route::get('/getListJob/{job_no}', 'NewUpdated\ScanCargoEksporController@getListJob');
        Route::get('/encryptJob/{job_no}', 'NewUpdated\ScanCargoEksporController@encryptJob');
        Route::get('/ajaxEncryptJob/{job_no}', 'NewUpdated\ScanCargoEksporController@ajaxEncryptJob');
        Route::get('/validasiCargo/{barcode}/{job_no}', 'NewUpdated\ScanCargoEksporController@validasiCargo');
        Route::get('/konfirmJob/{job_no}', 'NewUpdated\ScanCargoEksporController@konfirmJob');
        Route::get('/getListJobTable/{start}/{end}/{type}', 'NewUpdated\ScanCargoEksporController@getListJobTable');
        Route::get('/exportExcel/{job_no}', 'NewUpdated\ScanCargoEksporController@exportExcel');
        // Route::get('/addCargo', 'NewUpdated\ScanCargoEksporController@addCargo');
    });
});
