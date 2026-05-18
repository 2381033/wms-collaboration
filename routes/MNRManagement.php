<?php

Route::group(['middleware' => 'auth'], function () {

    Route::group(['prefix' => 'mnr-spareparts/'], function () {
        Route::get('/', 'NewUpdated\MNRManagement\DashboardController@home');

        Route::group(['prefix' => 'master/'], function () {
            Route::get('/locations', 'NewUpdated\MNRManagement\Master\LocationsMasterController@index')->name('locations.index');
            Route::post('/locations/store', 'NewUpdated\MNRManagement\Master\LocationsMasterController@store')->name('locations.store');
            Route::get('/locations/delete/{id}', 'NewUpdated\MNRManagement\Master\LocationsMasterController@delete')->name('locations.delete');
            Route::post('/locations/update', 'NewUpdated\MNRManagement\Master\LocationsMasterController@update')->name('locations.update');

            Route::get('/spareparts', 'NewUpdated\MNRManagement\Master\SparepartsMasterController@index')->name('spareparts.index');
            Route::post('/spareparts/store', 'NewUpdated\MNRManagement\Master\SparepartsMasterController@store')->name('spareparts.store');
            Route::post('/spareparts/update', 'NewUpdated\MNRManagement\Master\SparepartsMasterController@update')->name('spareparts.update');
            Route::get('/spareparts/delete/{id}', 'NewUpdated\MNRManagement\Master\SparepartsMasterController@delete')->name('spareparts.delete');

            Route::get('/equipment', 'NewUpdated\MNRManagement\Master\EquipmentMasterController@index')->name('equipment.index');
            Route::post('/equipment/store', 'NewUpdated\MNRManagement\Master\EquipmentMasterController@store')->name('equipment.store');
            Route::post('/equipment/update', 'NewUpdated\MNRManagement\Master\EquipmentMasterController@update')->name('equipment.update');
            Route::get('/equipment/delete/{id}', 'NewUpdated\MNRManagement\Master\EquipmentMasterController@delete')->name('equipment.delete');

            Route::get('/vendors', 'NewUpdated\MNRManagement\Master\VendorsMasterController@index')->name('vendors.index');
            Route::post('/vendors/store', 'NewUpdated\MNRManagement\Master\VendorsMasterController@store')->name('vendors.store');
            Route::post('/vendors/update', 'NewUpdated\MNRManagement\Master\VendorsMasterController@update')->name('vendors.update');
            Route::get('/vendors/delete/{id}', 'NewUpdated\MNRManagement\Master\VendorsMasterController@delete')->name('vendors.delete');
        });

        Route::group(['prefix' => 'transaction/'], function () {
            Route::get('/in', 'NewUpdated\MNRManagement\Transaction\in\TransactionInController@index')->name('transaction.in');
            Route::get('/in/create', 'NewUpdated\MNRManagement\Transaction\in\TransactionInController@create')->name('transaction.in.create');
            Route::post('/in/store', 'NewUpdated\MNRManagement\Transaction\in\TransactionInController@store')->name('transaction.in.store');
            Route::get('/in/{job_number}', 'NewUpdated\MNRManagement\Transaction\in\TransactionInController@show')->name('transaction.in.show');
            Route::post('/in/{job_number}/update-status', 'NewUpdated\MNRManagement\Transaction\in\TransactionInController@updateStatus')->name('transaction.in.update-status');
            
            Route::get('/out', 'NewUpdated\MNRManagement\Transaction\out\TransactionOutController@index');
            Route::get('/report', 'NewUpdated\MNRManagement\Transaction\report\TransactionReportController@index');
        });
    });
});
