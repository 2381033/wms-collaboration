<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'warehouse/inboundPlanningDC/'], function () {
        Route::GET('/', 'NewUpdated\InboundPlanningDC\InboundPlanningDCController@index');
        Route::GET('getListStock', 'NewUpdated\InboundPlanningDC\InboundPlanningDCController@getListStock');
        Route::POST('update', 'NewUpdated\InboundPlanningDC\InboundPlanningDCController@update')->name('updateInboundPlanningDC');
        Route::get('downloadTemplate', 'NewUpdated\InboundPlanningDC\InboundPlanningDCController@downloadTemplate');
        Route::POST('upload', 'NewUpdated\InboundPlanningDC\InboundPlanningDCController@upload');
    });
});
