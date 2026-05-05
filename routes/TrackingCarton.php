<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'warehouse/tracking-carton/'], function () {
        Route::get('/', 'NewUpdated\TrackingCartonController@index');
        Route::POST('search', 'NewUpdated\TrackingCartonController@search')->name('search');
        Route::POST('exportBySku', 'NewUpdated\TrackingCartonController@exportBySku')->name('exportBySku');
        Route::POST('exportByCarton', 'NewUpdated\TrackingCartonController@exportByCarton')->name('exportByCarton');
    });
});
