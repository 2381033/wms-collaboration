<?php
Route::group(['prefix' => 'tracing-export/'], function () {
    Route::get('/', 'NewUpdated\TracingCustomereExportController@index');
    Route::POST('/', 'NewUpdated\TracingCustomereExportController@trace')->name('tracingCustExport');
});
