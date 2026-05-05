<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'export/ScanCargoEkspor'], function () {
        // Route::get('/in', 'NewUpdated\ScanCargoEksporController@index');
        Route::POST('/submitReceiving', 'NewUpdated\ScanCargoEksporController@submitReceiving')->name('submitReceiving');
        Route::get('/list', 'NewUpdated\ScanCargoEksporController@list');
        Route::get('/getListReceive', 'NewUpdated\ScanCargoEksporController@getListReceive')->name('getListReceive');

        // Route::get('/out', 'NewUpdated\ScanCargoEksporController@stuffing');
        Route::POST('/submitStuffing', 'NewUpdated\ScanCargoEksporController@submitStuffing')->name('submitStuffing');
        Route::get('/getListStuffing', 'NewUpdated\ScanCargoEksporController@getListStuffing')->name('getListStuffing');

        Route::get('/downloadReceiving/{job_no}', 'NewUpdated\ScanCargoEksporController@downloadReceiving');
        Route::get('/downloadStuffing/{job_no}', 'NewUpdated\ScanCargoEksporController@downloadStuffing');

        Route::get('/pallet-tag', 'NewUpdated\ScanCargoEksporController@PrintPallettag');
        Route::POST('/doPrint', 'NewUpdated\ScanCargoEksporController@doPrint');
        Route::get('/getPalletStuffing/{pallet}', 'NewUpdated\ScanCargoEksporController@getPalletStuffing');
    });
});
