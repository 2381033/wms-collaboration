<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'auth_group'], function () {
        Route::get('/', 'NewUpdated\authenticateNewController@index');
        Route::get('/storeAuth/{name}', 'NewUpdated\authenticateNewController@storeAuth');
        Route::get('/deleteAuth/{id}', 'NewUpdated\authenticateNewController@deleteAuth');
        Route::get('/detailAuth/{id}', 'NewUpdated\authenticateNewController@detailAuth');
        Route::POST('/storeMapping', 'NewUpdated\authenticateNewController@storeMapping');
        Route::get('/deletePermission/{id}', 'NewUpdated\authenticateNewController@deletePermission');
        Route::get('/storePermission/{name}', 'NewUpdated\authenticateNewController@storePermission');
        Route::get('/detailUsers/{id}', 'NewUpdated\authenticateNewController@detailUsers');
        Route::POST('/storeMappingUsers', 'NewUpdated\authenticateNewController@storeMappingUsers');
    });
});
