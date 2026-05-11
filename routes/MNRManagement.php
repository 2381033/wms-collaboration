<?php

Route::group(['middleware' => 'auth'], function () {

    Route::group(['prefix' => 'mnr-spareparts/'], function () {
        Route::group(['prefix' => 'master/'], function () {
            Route::get('/location', 'NewUpdated\MNRManagement\Master\LocationMasterController@index');
            Route::get('/spareparts', 'NewUpdated\MNRManagement\Master\SparepartsMasterController@index');
        });
    });
});
