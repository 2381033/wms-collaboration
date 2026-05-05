<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'dashboard-ops'], function () {
        Route::get('/', 'NewUpdated\DashboardOps\DashboardOpsController@index');
        Route::POST('/searchData', 'NewUpdated\DashboardOps\DashboardOpsController@searchData')->name('searchData');
        Route::get('/getData/{branch}/{principal}/{start}/{end}', 'NewUpdated\DashboardOps\DashboardOpsController@getData');
        Route::get('/getMonthTruck/{p}/{branch}/{principal}', 'NewUpdated\DashboardOps\DashboardOpsController@getMonthTruck');
        Route::get('/getDetailVehicle/{bulan}/{branch}/{principal}', 'NewUpdated\DashboardOps\DashboardOpsController@getDetailVehicle');
        Route::get('/getListOccupancy/{branch}/{principal}/{start}/{end}', 'NewUpdated\DashboardOps\DashboardOpsController@getListOccupancy');
    });
});
