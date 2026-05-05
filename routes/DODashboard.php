<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'warehouse/do-dashboard/'], function () {
        Route::GET('/', 'NewUpdated\DashboardDODC\DashboardDODCController@index');
        Route::GET('getData', 'NewUpdated\DashboardDODC\DashboardDODCController@getData')->name('do-dashboard.getData');
        Route::GET('outstanding', 'NewUpdated\DashboardDODC\DashboardDODCController@outstanding');
        Route::GET('getListOutstanding', 'NewUpdated\DashboardDODC\DashboardDODCController@getListOutstanding')->name('getListOutstanding');
        Route::post('outstanding/mark-done', 'NewUpdated\DashboardDODC\DashboardDODCController@markAsDone')->name('outstanding.markDone');
    });
});
