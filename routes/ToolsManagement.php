<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'mr/tools-management'], function () {
        Route::get('/', 'NewUpdated\ManagementAlat\ManagementAlat@index');
        Route::get('list', 'NewUpdated\ManagementAlat\ManagementAlat@list');
        Route::post('store', 'NewUpdated\ManagementAlat\ManagementAlat@store');
        Route::get('gate-out/{id}', 'NewUpdated\ManagementAlat\ManagementAlat@gateOut');
        Route::group(['prefix' => 'master'], function () {
            Route::get('/', 'NewUpdated\ToolsManagement\MasterController@index');
            Route::get('/getMaster/{type}', 'NewUpdated\ToolsManagement\MasterController@getMaster');
        });
    });
});
