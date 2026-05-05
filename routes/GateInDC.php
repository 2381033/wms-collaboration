<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'warehouse/gate-in/'], function () {
        Route::get('/', 'NewUpdated\GateInDC\GateInController@index');
        Route::get('list', 'NewUpdated\GateInDC\GateInController@list');
        Route::post('store', 'NewUpdated\GateInDC\GateInController@store');
        Route::get('gate-out/{id}', 'NewUpdated\GateInDC\GateInController@gateOut');
    });
});
