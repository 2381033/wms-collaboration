<?php

Route::group(['middleware' => 'auth'], function () {

    Route::group(['prefix' => 'vm-price/'], function () {
        Route::get('priceMaster', 'NewUpdated\VMPriceController@priceMaster');
        Route::get('checking-cost', 'NewUpdated\VMPriceController@priceTrace');
        Route::get('priceActivity', 'NewUpdated\VMPriceController@priceActivity');
        Route::get('detailActivity/{user_id}', 'NewUpdated\VMPriceController@detailActivity');
        Route::get('getListMasterPrice/{service}/{mot}/{vendor}', 'NewUpdated\VMPriceController@getListMasterPrice');
        Route::get('templateUploadPrice/{service}/{mot}', 'NewUpdated\VMPriceController@templateUploadPrice');
        Route::POST('uploadPrice', 'NewUpdated\VMPriceController@uploadPrice')->name('uploadPrice');
        Route::POST('templateEditHarga', 'NewUpdated\VMPriceController@templateEditHarga')->name('templateEditHarga');
        Route::POST('traceHarga', 'NewUpdated\VMPriceController@traceHarga')->name('traceHarga');
        Route::POST('updatePriceExcel', 'NewUpdated\VMPriceController@updatePriceExcel')->name('updatePriceExcel');
        Route::get('disablePrice/{id}', 'NewUpdated\VMPriceController@disablePrice');
        Route::get('getMOT/{service}', 'NewUpdated\VMPriceController@getMOT');
        Route::get('getVendor/{service}/{mot}', 'NewUpdated\VMPriceController@getVendor');
        Route::POST('updateData', 'NewUpdated\VMPriceController@updateData')->name('updateData');
        Route::get('editData/{id}', 'NewUpdated\VMPriceController@editData');
        Route::get('historyData/{id}', 'NewUpdated\VMPriceController@historyData');
        Route::get('getSelectService/{mot}/{prod}', 'NewUpdated\VMPriceController@getSelectService');
        Route::get('getKotaKab/{origin}/{mot}/{prod}/{service}/{vehicle}', 'NewUpdated\VMPriceController@getKotaKab');
        Route::get('getSelectDestination/{origin}/{kotakab}/{mot}/{prod}/{service}/{vehicle}', 'NewUpdated\VMPriceController@getDestination');
        Route::get('getSelectVehicle/{service}', 'NewUpdated\VMPriceController@getSelectVehicle');
        Route::get('getSelectService/{mot}/{prod}', 'NewUpdated\VMPriceController@getSelectService');

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

        Route::POST('/store', 'NewUpdated\CycleCountController@store');
        Route::get('/stokTransfer/{id}', 'NewUpdated\CycleCountController@stokTransfer');
        Route::POST('/postStokTransfer', 'NewUpdated\CycleCountController@postStokTransfer');
        Route::get('/monitoring', 'NewUpdated\CycleCountController@monitoring');
        Route::get('/send/{type}', 'NewUpdated\CycleCountController@send');
        Route::get('/cancelHitungan/{id}', 'NewUpdated\CycleCountController@cancelHitungan');
        Route::get('/cariListSKU/{product_code}', 'NewUpdated\CycleCountController@cariListSKU');
        Route::get('/cariData/{tgl_mulai}/{tgl_selesai}', 'NewUpdated\CycleCountController@cariData');

        Route::get('/templateExportSku/{site}', 'NewUpdated\CycleCountController@templateExportSku');
        Route::POST('/import', 'NewUpdated\CycleCountController@import');
    });
});
