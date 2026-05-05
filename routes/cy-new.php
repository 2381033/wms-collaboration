<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'cy-new/'], function () {
        Route::get('/', 'NewUpdated\CYNewController@index');
        Route::post('downloadExcel', 'NewUpdated\CYNewController@downloadExcel');
        Route::post('downloadTransaction', 'NewUpdated\CYNewController@downloadTransaction');
        Route::post('searchImages', 'NewUpdated\CYNewController@searchImages');
        Route::get('downloadFoto/{barcode}/{type}/{container_no}', 'NewUpdated\CYNewController@downloadFoto');

        //BONGKAR
        Route::get('getListBongkar/{start}/{end}/{status}', 'NewUpdated\CYNewController@getListBongkar');
        Route::post('storeBongkar', 'NewUpdated\CYNewController@storeBongkar');
        Route::get('printBongkar/{barcode}', 'NewUpdated\CYNewController@printBongkar');
        Route::get('truckNumberBongkar/{truck_number}/{id}', 'NewUpdated\CYNewController@truckNumberBongkar');
        Route::get('gateInBongkar/{id}', 'NewUpdated\CYNewController@gateInBongkar');
        Route::post('gateOutBongkar', 'NewUpdated\CYNewController@gateOutBongkar');
        Route::get('showBongkar/{barcode}', 'NewUpdated\CYNewController@showBongkar');
        Route::get('deleteBongkar/{id}', 'NewUpdated\CYNewController@deleteBongkar');


        //MUAT
        Route::post('storeMuat', 'NewUpdated\CYNewController@storeMuat');
        Route::post('getStock', 'NewUpdated\CYNewController@getStock');
        Route::get('getListMuat/{start}/{end}/{status}', 'NewUpdated\CYNewController@getListMuat');
        Route::get('printMuat/{barcode}', 'NewUpdated\CYNewController@printMuat');
        Route::get('showMuat/{barcode}', 'NewUpdated\CYNewController@showMuat');
        Route::get('truckNumberMuat/{truck_number}/{id}', 'NewUpdated\CYNewController@truckNumberMuat');
        Route::get('gateInMuat/{id}', 'NewUpdated\CYNewController@gateInMuat');
        Route::post('gateOutMuat', 'NewUpdated\CYNewController@gateOutMuat');
        Route::get('deleteMuat/{id}/{id_bongkar}', 'NewUpdated\CYNewController@deleteMuat');
        Route::get('validasiMuat/{id}', 'NewUpdated\CYNewController@validasiMuat');
    });
});
