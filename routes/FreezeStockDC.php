<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'warehouse/FreezeStockDC/'], function () {
        Route::GET('/', 'NewUpdated\FreezeStockDC\FreezeStockDCController@index');
        Route::POST('/', 'NewUpdated\FreezeStockDC\FreezeStockDCController@store')->name('freezeStockDC');
        Route::POST('/unFreeze', 'NewUpdated\FreezeStockDC\FreezeStockDCController@unFreeze')->name('unfreezeStockDC');
    });
});
