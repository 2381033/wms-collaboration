<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'warehouse/OccupancyDaillyDC/'], function () {
        Route::GET('/', 'NewUpdated\OccupancyDaillyDC\OccupancyDaillyDCController@index');
        Route::POST('search', 'NewUpdated\OccupancyDaillyDC\OccupancyDaillyDCController@search')->name('searchOccupancyDaillyDC');
    });
});
