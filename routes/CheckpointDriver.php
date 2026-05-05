<?php
Route::group(['prefix' => 'MonitoringCheckpoint/dashboard'], function () {
    Route::get('/', 'NewUpdated\MonitoringCheckpointController@index');
    Route::get('/getDisplay/{id}', 'NewUpdated\MonitoringCheckpointController@getDisplay');
    Route::get('/updateDisplay', 'NewUpdated\MonitoringCheckpointController@updateDisplay');
    Route::get('/showFoto/{type}/{file}', 'NewUpdated\MonitoringCheckpointController@showFoto');
    Route::POST('/export', 'NewUpdated\MonitoringCheckpointController@export');
});
Route::group(['prefix' => 'MonitoringCheckpoint/planner'], function () {
    Route::get('/', 'NewUpdated\MonitoringCheckpointController@planner');
    Route::POST('/submitPlanner', 'NewUpdated\MonitoringCheckpointController@submitPlanner')->name('submitPlanner');
    Route::get('/searchJenisArmada/{no_mobil}', 'NewUpdated\MonitoringCheckpointController@searchJenisArmada');
    Route::get('/deleteJob/{token}', 'NewUpdated\MonitoringCheckpointController@deleteJob');
    Route::get('/databasePerjalanan', 'NewUpdated\MonitoringCheckpointController@databasePerjalanan');
    Route::get('/getListDatabasePerjalanan/{start}/{end}/{status}', 'NewUpdated\MonitoringCheckpointController@getListDatabasePerjalanan');
    Route::get('/historyPerjalanan/{token}', 'NewUpdated\MonitoringCheckpointController@historyPerjalanan');
    Route::get('/detailRevenueCost/{token}', 'NewUpdated\MonitoringCheckpointController@detailRevenueCost');
    Route::POST('/submitAdditionalRevenueCost', 'NewUpdated\MonitoringCheckpointController@submitAdditionalRevenueCost')->name('submitAdditionalRevenueCost');
    Route::get('/downloadFotoPerjalanan/{token}', 'NewUpdated\MonitoringCheckpointController@downloadFotoPerjalanan');
    Route::POST('/updateOrderNo', 'NewUpdated\MonitoringCheckpointController@updateOrderNo')->name('updateOrderNo');
});
