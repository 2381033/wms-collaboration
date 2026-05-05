<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'export/dashboard'], function () {
        Route::get('/', 'NewUpdated\DashboardOps\DashboardExportController@index');
        Route::GET('/search/{branch}/{principal}', 'NewUpdated\DashboardOps\DashboardExportController@searchData');
    });
});
