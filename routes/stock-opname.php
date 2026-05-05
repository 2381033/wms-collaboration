<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'inventory/stock-opname/'], function () {
        Route::get('/', 'NewUpdated\StockOpnameController@index');
    });
});
