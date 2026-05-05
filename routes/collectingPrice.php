<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'collectingPrice'], function () {
        Route::get('/', 'NewUpdated\collectingPriceController@index');
        Route::POST('/postPrice', 'NewUpdated\collectingPriceController@submit')->name('postPrice');
        Route::get('/delete/{id}', 'NewUpdated\collectingPriceController@delete');
    });
});
