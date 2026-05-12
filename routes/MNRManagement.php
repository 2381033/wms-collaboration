<?php

Route::group(['middleware' => 'auth'], function () {

    Route::group(['prefix' => 'mnr-spareparts/'], function () {
        Route::group(['prefix' => 'master/'], function () {
            Route::get('/locations', 'NewUpdated\MNRManagement\Master\LocationsMasterController@index');
            Route::post('/locations/store', 'NewUpdated\MNRManagement\Master\LocationsMasterController@store')->name('locations.store');
            Route::get('/spareparts', 'NewUpdated\MNRManagement\Master\SparepartsMasterController@index');
            Route::post('/spareparts/store', 'NewUpdated\MNRManagement\Master\SparepartsMasterController@store')->name('spareparts.store');
            Route::get('/tools', 'NewUpdated\MNRManagement\Master\ToolsMasterController@index');
            Route::post('/tools/store', 'NewUpdated\MNRManagement\Master\ToolsMasterController@store')->name('tools.store');
            Route::get('/vendors', 'NewUpdated\MNRManagement\Master\VendorsMasterController@index');
            Route::post('/vendors/store', 'NewUpdated\MNRManagement\Master\VendorsMasterController@store')->name('vendors.store');

            });
    });
});
