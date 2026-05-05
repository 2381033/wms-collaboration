<?php

Route::group(['prefix' => 'tax/'], function () {
    Route::get('home', 'NewUpdated\TaxController@home');
    Route::POST('login', 'NewUpdated\TaxController@login');
    Route::get('index', 'NewUpdated\TaxController@index');
    Route::POST('uploadzip', 'NewUpdated\TaxController@uploadzip');
    Route::get('getList', 'NewUpdated\TaxController@getList');
    Route::get('tracking', 'NewUpdated\TaxController@tracking');
    Route::POST('postTracking', 'NewUpdated\TaxController@postTracking');
});
