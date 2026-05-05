<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'export/ob/'], function () {
        Route::GET('/', 'NewUpdated\Export\OB\JobController@index');
        Route::GET('/create', 'NewUpdated\Export\OB\JobController@create');
        Route::GET('autocomplete/peb', 'NewUpdated\Export\OB\JobController@autocompletePeb')->name('autocomplete.peb');
        Route::GET('autocomplete/ajuo', 'NewUpdated\Export\OB\JobController@autocompleteAju')->name('autocomplete.aju');
        Route::GET('getDetail', 'NewUpdated\Export\OB\JobController@getDetail');
        Route::GET('show/{id}', 'NewUpdated\Export\OB\JobController@show');
        Route::post('store', 'NewUpdated\Export\OB\JobController@store')->name('export.ob.store');
        Route::GET('chooseStapel/{job_id}/{username}', 'NewUpdated\Export\OB\JobController@chooseStapel');
        Route::GET('chooseChecker/{job_id}/{username}', 'NewUpdated\Export\OB\JobController@chooseChecker');
        Route::GET('searchData/{start}/{end}/{status}', 'NewUpdated\Export\OB\JobController@searchData');
        Route::GET('confirmationJob/{job_id}', 'NewUpdated\Export\OB\JobController@confirmationJob');
        Route::GET('showImages/{job_no}', 'NewUpdated\Export\OB\JobController@showImages');
        Route::get('deleteImage/{id}', 'NewUpdated\Export\OB\JobController@deleteImage');
        Route::get('backtoChecker/{job_id}', 'NewUpdated\Export\OB\JobController@backtoChecker');
        Route::GET('tally_sheet/{id}', 'NewUpdated\Export\OB\JobController@tally_sheet');
    });
});
