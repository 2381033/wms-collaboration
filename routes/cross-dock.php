<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'crossDock/'], function () {
        Route::group(['prefix' => 'tools/'], function () {
            Route::GET('/', 'NewUpdated\CrossdockNew\Tools\ToolsController@index');
            Route::GET('getListInbound', 'NewUpdated\CrossdockNew\Tools\ToolsController@getListInbound');
        });

        Route::group(['prefix' => 'masterData/'], function () {
            Route::GET('/', 'NewUpdated\CrossdockNew\Master\MasterDataController@index');
            Route::group(['prefix' => 'warehouse/'], function () {
                Route::POST('add', 'NewUpdated\CrossdockNew\Master\MasterDataController@addWarehouse')->name('addWarehouse');
                Route::GET('getList', 'NewUpdated\CrossdockNew\Master\MasterDataController@getListWarehouse')->name('getListWarehouse');
                Route::GET('delete/{id}', 'NewUpdated\CrossdockNew\Master\MasterDataController@deleteWarehouse');
                Route::POST('update', 'NewUpdated\CrossdockNew\Master\MasterDataController@updateWarehouse')->name('updateWarehouse');
                Route::GET('edit/{id}', 'NewUpdated\CrossdockNew\Master\MasterDataController@editWarehouse');
            });
            Route::group(['prefix' => 'customer/'], function () {
                Route::POST('add', 'NewUpdated\CrossdockNew\Master\MasterDataController@addCustomer')->name('addCustomer');
                Route::GET('getList', 'NewUpdated\CrossdockNew\Master\MasterDataController@getListCustomer')->name('getListCustomer');
                Route::GET('delete/{id}', 'NewUpdated\CrossdockNew\Master\MasterDataController@deleteCustomer');
                Route::POST('update', 'NewUpdated\CrossdockNew\Master\MasterDataController@updateCustomer')->name('updateCustomer');
                Route::GET('edit/{id}', 'NewUpdated\CrossdockNew\Master\MasterDataController@editCustomer');
                Route::POST('import', 'NewUpdated\CrossdockNew\Master\MasterDataController@importCustomer')->name('importCustomer');
            });
            Route::group(['prefix' => 'mapping/'], function () {
                Route::POST('add', 'NewUpdated\CrossdockNew\Master\MasterDataController@addMapping')->name('addMapping');
                Route::GET('delete/{id}', 'NewUpdated\CrossdockNew\Master\MasterDataController@deleteMapping');
                Route::GET('getListMapping/{id_user}', 'NewUpdated\CrossdockNew\Master\MasterDataController@getListMapping');
            });
        });

        Route::get('/', 'NewUpdated\CrossdockNew\DashboardController@index');
        Route::get('/getListJob/{startDate}/{endDate}/{jobtype}/{statusJob}', 'NewUpdated\CrossdockNew\DashboardController@getListJob');
        Route::get('/searchLocation', 'NewUpdated\CrossdockNew\Inbound\InboundController@searchLocation')->name('searchLocation');

        Route::group(['prefix' => 'report/'], function () {
            Route::POST('/', 'NewUpdated\CrossdockNew\Report\StockLedgerController@search')->name('searchStockReport');
            Route::POST('daily', 'NewUpdated\CrossdockNew\Report\DailyInOutController@search');
            Route::POST('storage', 'NewUpdated\CrossdockNew\Report\StorageController@search');
            Route::POST('transaction', 'NewUpdated\CrossdockNew\Report\TransactionReportController@search');
        });

        Route::group(['prefix' => 'inbound/'], function () {
            Route::get('/', 'NewUpdated\CrossdockNew\Inbound\InboundController@index');
            Route::POST('storeHeader', 'NewUpdated\CrossdockNew\Inbound\InboundController@storeHeader')->name('storeHeader');
            Route::get('showJob/{id}', 'NewUpdated\CrossdockNew\Inbound\InboundController@showJob');
            Route::get('showJobFrontend/{id}', 'NewUpdated\CrossdockNew\Inbound\InboundController@showJobFrontend');
            Route::POST('importCargo', 'NewUpdated\CrossdockNew\Inbound\InboundController@import')->name('importCargo');
            Route::POST('addCargo', 'NewUpdated\CrossdockNew\Inbound\InboundController@storeCargo')->name('addCargo');
            Route::POST('addCargo', 'NewUpdated\CrossdockNew\Inbound\InboundController@storeCargo')->name('addCargo');
            Route::POST('updateCargo', 'NewUpdated\CrossdockNew\Inbound\InboundController@updateCargo')->name('updateCargo');
            Route::get('editCargo/{id}', 'NewUpdated\CrossdockNew\Inbound\InboundController@editCargo');
            Route::get('deleteCargo/{id}', 'NewUpdated\CrossdockNew\Inbound\InboundController@deleteCargo');
            Route::get('confirmCargo/{id}', 'NewUpdated\CrossdockNew\Inbound\InboundController@confirmCargo');
            Route::get('getMappingPallet/{id}', 'NewUpdated\CrossdockNew\Inbound\InboundController@getMappingPallet');
            Route::POST('postMappingPallet/', 'NewUpdated\CrossdockNew\Inbound\InboundController@postMappingPallet')->name('postMappingPallet');
            Route::get('report/{type}/{id}', 'NewUpdated\CrossdockNew\Inbound\InboundController@report');
            Route::get('mappingConfirm/{id}', 'NewUpdated\CrossdockNew\Inbound\InboundController@mappingConfirm');
            Route::get('confirm/{type}/{id}', 'NewUpdated\CrossdockNew\Inbound\InboundController@confirm');
            Route::POST('postPutaway/', 'NewUpdated\CrossdockNew\Inbound\InboundController@postPutaway')->name('postPutaway');
            Route::POST('submitUnloading/', 'NewUpdated\CrossdockNew\Inbound\InboundController@submitUnloading')->name('submitUnloading');
        });

        Route::group(['prefix' => 'outbound/'], function () {
            Route::get('/', 'NewUpdated\CrossdockNew\Outbound\OutboundController@index');
            Route::POST('storeHeader', 'NewUpdated\CrossdockNew\Outbound\OutboundController@storeHeader')->name('storeHeaderOutbound');
            Route::get('showJob/{id}', 'NewUpdated\CrossdockNew\Outbound\OutboundController@showJob');
            Route::get('showJobFrontend/{id}', 'NewUpdated\CrossdockNew\Outbound\OutboundController@showJobFrontend');
            Route::get('getStock/{cargo_id}/{warehouse}/{customer}/{branch}/{id}', 'NewUpdated\CrossdockNew\Outbound\OutboundController@getStock');
            Route::POST('storeOrderDetail', 'NewUpdated\CrossdockNew\Outbound\OutboundController@storeOrderDetail')->name('storeOrderDetail');
            Route::get('deleteCargo/{id}', 'NewUpdated\CrossdockNew\Outbound\OutboundController@deleteCargo');
            Route::POST('updateCargo', 'NewUpdated\CrossdockNew\Outbound\OutboundController@updateCargo')->name('updateCargoOutbound');
            Route::get('confirmation/{value}/{idheader}', 'NewUpdated\CrossdockNew\Outbound\OutboundController@confirmation');
            Route::get('report/{type}/{id}', 'NewUpdated\CrossdockNew\Outbound\OutboundController@report');
            Route::POST('cancelPicking', 'NewUpdated\CrossdockNew\Outbound\OutboundController@cancelPicking')->name('cancelPicking');
            Route::POST('postPicking', 'NewUpdated\CrossdockNew\Outbound\OutboundController@postPicking')->name('postPicking');
            Route::POST('scanByPass', 'NewUpdated\CrossdockNew\Outbound\OutboundController@scanByPass')->name('scanByPass');
            Route::POST('postDespatch', 'NewUpdated\CrossdockNew\Outbound\OutboundController@postDespatch')->name('postDespatch');
            Route::POST('updateDespatch', 'NewUpdated\CrossdockNew\Outbound\OutboundController@updateDespatch')->name('updateDespatch');
            Route::get('editDespatch/{id}', 'NewUpdated\CrossdockNew\Outbound\OutboundController@editDespatch');
            Route::POST('confirmOutbound', 'NewUpdated\CrossdockNew\Outbound\OutboundController@confirmOutbound')->name('confirmOutbound');
            Route::POST('submitLoading', 'NewUpdated\CrossdockNew\Outbound\OutboundController@submitLoading')->name('submitLoading');
        });

        Route::group(['prefix' => 'scanCargo/'], function () {
            Route::get('/', 'NewUpdated\CrossdockNew\Scan\ScanCargoController@index');
            Route::get('searchJob/{id}', 'NewUpdated\CrossdockNew\Scan\ScanCargoController@searchJob');
            Route::get('detailJob/{id}', 'NewUpdated\CrossdockNew\Scan\ScanCargoController@detailJob');
            Route::get('detailJobFrontend/{id}', 'NewUpdated\CrossdockNew\Scan\ScanCargoController@detailJobFrontend');
            Route::get('validasiCargo/{qrcode}/{id_detail}/{id_stock}', 'NewUpdated\CrossdockNew\Scan\ScanCargoController@validasiCargo');
        });
    });
});
