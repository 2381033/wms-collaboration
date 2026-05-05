<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'export/BeaCukai/'], function () {
        Route::get('/', 'NewUpdated\Export\BeaCukai\BeaCukaiController@index');
        Route::POST('getPEB', 'NewUpdated\Export\BeaCukai\BeaCukaiController@getPEB');
        Route::POST('getShipper', 'NewUpdated\Export\BeaCukai\BeaCukaiController@getShipper');
        Route::get('detailPEB/{id}', 'NewUpdated\Export\BeaCukai\BeaCukaiController@DetailPEB');
        Route::POST('store', 'NewUpdated\Export\BeaCukai\BeaCukaiController@store')->name('storeBC');
        Route::POST('updateBC', 'NewUpdated\Export\BeaCukai\BeaCukaiController@updateBC')->name('updateBC');
        Route::POST('storeDetail', 'NewUpdated\Export\BeaCukai\BeaCukaiController@storeDetail')->name('storeDetail');
        Route::get('detailBC/{id}', 'NewUpdated\Export\BeaCukai\BeaCukaiController@DetailBC');
        Route::get('deleteDetail/{id}', 'NewUpdated\Export\BeaCukai\BeaCukaiController@deleteDetail');

        //REPORTING
        Route::get('Report/inbound/icr/{id}', 'NewUpdated\Export\BeaCukai\ReportController@icr');
        Route::get('Report/{type}', 'NewUpdated\Export\BeaCukai\ReportController@index');
        Route::get('Report/monthly/{start}/{end}', 'NewUpdated\Export\BeaCukai\ReportController@monthly');
        Route::get('Report/inbound/{start}/{end}', 'NewUpdated\Export\BeaCukai\ReportController@inbound');
        Route::get('Report/outbound/{start}/{end}', 'NewUpdated\Export\BeaCukai\ReportController@outbound');
        Route::get('Report/stock_report/{shipper}', 'NewUpdated\Export\BeaCukai\ReportController@stock_report');
        Route::get('Report/downloadPDF/{type}/{start}/{end}', 'NewUpdated\Export\BeaCukai\ReportController@downloadPDF');
        Route::get('Report/downloadExcel/{type}/{tahun}/{bulan}', 'NewUpdated\Export\BeaCukai\ReportController@downloadExcel');
    });
});
