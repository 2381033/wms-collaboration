<?php
Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'export/report/'], function () {

        //STOCK LEDGER
        Route::get('stock-ledger', 'Transaction\Export\LedgerController@index');
        Route::post('stock-ledger/post', 'Transaction\Export\LedgerController@report')->name('stock-export.report');
        Route::post('stock-ledger/download', 'Transaction\Export\LedgerController@export')->name('stock-export.download');
        Route::get('loadCharts/{branchId}', 'Transaction\Export\LedgerController@loadCharts');

        Route::get('lcl-performance', 'Transaction\Export\LCLPerformanceController@index');
        Route::GET('post-lcl-performance/{start}/{end}', 'Transaction\Export\LCLPerformanceController@post');

        Route::post('stock-ledger/post/occupancy', 'Transaction\Export\LedgerController@occupancy')->name('stock-export.occupancy');
    });
});
