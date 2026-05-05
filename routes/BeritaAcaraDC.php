<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'warehouse/ba/'], function () {
        Route::GET('/', 'NewUpdated\BerittaAcaraDC\BeritaAcaraDCController@index');
        Route::POST('/store', 'NewUpdated\BerittaAcaraDC\BeritaAcaraDCController@store')->name('storeBADC');
        Route::GET('/print/{id}', 'NewUpdated\BerittaAcaraDC\BeritaAcaraDCController@print')->name('printBADC');
        Route::GET('/filter-ba', 'NewUpdated\BerittaAcaraDC\BeritaAcaraDCController@filter')->name('filterBADC');
    });
});
