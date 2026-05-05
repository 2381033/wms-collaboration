<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'reportPenagihan'], function () {
        Route::get('/', 'NewUpdated\reportPenagihanController@index');
    });
});
