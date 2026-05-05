<?php
Route::group(['prefix' => 'foto-management/'], function () {
    Route::get('tracing', 'NewUpdated\Import\FotoManagementController@tracing');
    Route::POST('postTracing', 'NewUpdated\Import\FotoManagementController@postTracing');
    Route::get('downloadFoto/{token}', 'NewUpdated\Import\FotoManagementController@downloadFoto');
    Route::group(['middleware' => 'auth'], function () {
        Route::get('outstanding', 'NewUpdated\Import\FotoManagementController@outstanding');
        Route::get('previewFoto/{token}', 'NewUpdated\Import\FotoManagementController@previewFoto');
        Route::get('showFoto/{token}', 'NewUpdated\Import\FotoManagementController@showFoto');
        Route::get('deleteFoto/{id}', 'NewUpdated\Import\FotoManagementController@deleteFoto');
        Route::post('uploadFoto', 'NewUpdated\Import\FotoManagementController@uploadFoto');
    });
});
