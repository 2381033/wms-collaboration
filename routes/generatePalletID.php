<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'inventory/generatePalletID'], function () {
        Route::get('/', 'NewUpdated\generatePalletIDController@index');
        Route::post('/postGenerate', 'NewUpdated\generatePalletIDController@postGenerate');
        Route::post('/postSKUParsial', 'NewUpdated\generatePalletIDController@postSKUParsial');
        Route::post('/postDispatch', 'NewUpdated\generatePalletIDController@postDispatch');
        Route::get('/print/{job_no}', 'NewUpdated\generatePalletIDController@print');
        Route::get('/masterData', 'NewUpdated\generatePalletIDController@masterData');
        Route::get('/showListSKU/{job_no}', 'NewUpdated\generatePalletIDController@showListSKU');
        Route::get('/encryptqr/{job_no}', 'NewUpdated\generatePalletIDController@encryptqr');
        Route::get('/doScan/{qr}', 'NewUpdated\generatePalletIDController@doScan');
        Route::get('/deleteSKU/{id}', 'NewUpdated\generatePalletIDController@deleteSKU');
        Route::get('/dispatch', 'NewUpdated\generatePalletIDController@dispatchJob');
        Route::get('/getDispatchSKU/{qr}', 'NewUpdated\generatePalletIDController@getDispatchSKU');
        Route::get('/scan', 'NewUpdated\generatePalletIDController@scan');
        Route::get('/typeGenerate/{type}', 'NewUpdated\generatePalletIDController@typeGenerate');
        Route::get('/checkidMaster/{job_no}', 'NewUpdated\generatePalletIDController@checkidMaster');
        Route::get('/cariData/{tgl_mulai}/{tgl_selesai}', 'NewUpdated\generatePalletIDController@cariData');
    });
});
