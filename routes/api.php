<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// EPM
Route::middleware(['blockIP'])->group(function () {
    //Inbound
    // Route::get('EPM/inbound/job/{userid}', 'Api\EPM\InboundController@index');
    Route::post('EPM/inbound/job/submit', 'Api\EPM\InboundController@submit');
    //outbound
    // Route::get('EPM/outbound/job/{userid}', 'Api\EPM\OutboundController@index');
    Route::post('EPM/outbound/job/submit', 'Api\EPM\OutboundController@submit');
    // Route::post('EPM/product/submit', 'Api\EPM\ProductController@submit');
    // Route::get('EPM/soh/job/all', 'Api\EPM\StockOnHandController@all');
    Route::get('EPM/stock/onhand', 'Api\EPM\StockOnHandController@all');
});

Route::get('grn/job/{userid}', 'Api\GrnController@index');
Route::get('grn/job/{userid}/{param}', 'Api\GrnController@search');
Route::get('grn/vehicle/{inbound_id}', 'Api\GrnController@vehicle');
Route::get('grn/detail/{inbound_id}/{vehicle_no}', 'Api\GrnController@detail');
Route::post('grn/vehicle/start', 'Api\GrnController@start');
Route::post('grn/vehicle/finish', 'Api\GrnController@finish');
Route::post('grn/store', 'Api\GrnController@store');
Route::post('grn/submit', 'Api\GrnController@submit');

Route::get('inbound/job/{userid}', 'Api\InboundController@index');
Route::get('inbound/job/{userid}/{param}', 'Api\InboundController@search');
Route::get('inbound/vehicle/{inbound_id}', 'Api\InboundController@vehicle');
Route::get('inbound/detail/{inbound_id}/{vehicle_no}', 'Api\InboundController@detail');
Route::post('inbound/detail/submit', 'Api\InboundController@submit');

Route::get('outbound-job/{userid}', 'Api\OutboundController@index');
Route::get('outbound-job/{userid}/{param}', 'Api\OutboundController@search');
Route::get('outbound-job/order/{userid}/{id}', 'Api\OutboundController@order');
Route::get('outbound-job/detail/{userid}/{outbound_id}/{order_id}', 'Api\OutboundController@detail');
Route::post('outbound-job/submit', 'Api\OutboundController@submit');

Route::get('export/outbound/job-list/{user_id}/{param}', 'Api\Export\JobController@index');
Route::get('export/outbound/job-list/{user_id}', 'Api\Export\JobController@index');
Route::get('export/outbound/check/{userid}', 'Api\Export\JobController@userChecking');
Route::get('export/outbound/order-list/{id}/{param}', 'Api\Export\OrderController@index');
Route::get('export/outbound/order-list/{id}', 'Api\Export\OrderController@index');
Route::get('export/outbound/detail-list/{id}', 'Api\Export\DetailController@index');
Route::post('export/outbound/detail-view', 'Api\Export\DetailController@view');

Route::get('cy/gate/booking/{user_id}/{booking_no}', 'Api\CY\GateController@checkingBooking');
Route::post('cy/gate/booking/store', 'Api\CY\GateController@inboundGateIn');
Route::get('cy/gate/inbound/list/{user_id}', 'Api\CY\GateController@inboundGateOutList');
Route::post('cy/gate/outbound/store', 'Api\CY\GateController@outboundGateIn');
Route::get('cy/gate/outbound/list/{user_id}', 'Api\CY\GateController@outboundGateOutList');
Route::post('cy/gate/store', 'Api\CY\GateController@gateOut');

Route::get('cy/inbound/checklist/list/{user_id}', 'Api\CY\CheckListController@inboundList');
Route::get('cy/outbound/checklist/list/{user_id}', 'Api\CY\CheckListController@outboundList');
Route::get('cy/checklist/detail/{id}', 'Api\CY\CheckListController@checkListView');
Route::post('cy/checklist/upload', 'Api\CY\CheckListController@uploadCheckList');
Route::post('cy/checklist/signature', 'Api\CY\CheckListController@uploadSignature');

Route::post('login', 'Api\UserController@login');
Route::post('upload-images', 'Api\UploadImageController@upload');

Route::get('master/container-type/list', 'Api\MasterController@containerType');
Route::get('master/container-size/list', 'Api\MasterController@containerSize');
Route::get('master/vehicle-size/list', 'Api\MasterController@vehicleSize');
Route::get('master/principal/list/{user_id}', 'Api\MasterController@principal');
Route::get('master/vendor/list', 'Api\MasterController@vendor');

Route::get('truck/gate/list/{user_id}', 'Api\Truck\GateProcessController@gateList');
Route::get('truck/gate/checklist/list/{user_id}', 'Api\Truck\GateProcessController@gateJobList');
Route::get('truck/gate/view/{gate_id}', 'Api\Truck\GateProcessController@gateView');
Route::get('truck/gate/checklist/{gate_id}', 'Api\Truck\GateProcessController@gateCheckList');
Route::get('truck/gate/check/list/{gate_id}', 'Api\Truck\GateProcessController@checkList');
Route::get('truck/gate/process/{gate_id}/{type_name}', 'Api\Truck\GateProcessController@gateProcessList');
Route::post('truck/gate/entry', 'Api\Truck\GateProcessController@gateEntry');
Route::post('truck/gate/checklist/submit', 'Api\Truck\GateProcessController@gateCheckListUpdate');
Route::post('truck/gate/update', 'Api\Truck\GateProcessController@gateUpdate');
Route::post('truck/gate/process/submit', 'Api\Truck\GateProcessController@gateProcessUpdate');

Route::middleware('basicauth')->group(function (): void {
    // PROTECTED ROUTES
    Route::post('EPM/stock-transfer/job/submit', 'Api\EPM\StockTransferController@submit');
});

Route::group(['prefix' => 'gateContainer/'], function () {
    Route::post('login', 'Api\GateContainer\UserController@login');
    Route::get('logout', 'Api\GateContainer\UserController@logout');
    Route::post('submitGateIn', 'Api\GateContainer\GateCargoController@submitGateIn');
});

Route::group(['prefix' => 'CheckpointDriver/'], function () {
    Route::post('login', 'Api\CheckpointDriver\UserController@login');
    Route::group(['prefix' => 'Garage/'], function () {
        Route::post('submitFoto', 'Api\CheckpointDriver\GarageController@submitFoto');
        Route::get('startFromGarage/{job_no}', 'Api\CheckpointDriver\GarageController@startFromGarage');
        Route::get('countJob/{user_id}', 'Api\CheckpointDriver\GarageController@countJob');
        Route::get('getJobMe/{user_id}', 'Api\CheckpointDriver\GarageController@getJobMe');
        Route::POST('balikKeGarasi', 'Api\CheckpointDriver\GarageController@balikKeGarasi');
        Route::get('tibaDiGarasi/{job_no}', 'Api\CheckpointDriver\GarageController@tibaDiGarasi');

        //detailJob
        Route::get('detailJobMe/{token}', 'Api\CheckpointDriver\GarageController@detailJobMe');
        Route::get('getJenisArmada', 'Api\CheckpointDriver\GarageController@getJenisArmada');
        Route::POST('submitSuratJalan', 'Api\CheckpointDriver\GarageController@submitSuratJalan');
        Route::get('timelinePerjalanan/{token}', 'Api\CheckpointDriver\GarageController@timelinePerjalanan');
        Route::POST('uploadFotoKmFinish', 'Api\CheckpointDriver\GarageController@uploadFotoKmFinish');
    });
    Route::group(['prefix' => 'LokasiMuat/'], function () {
        Route::post('submitFotoGateIn', 'Api\CheckpointDriver\LokasiMuatController@submitFotoGateIn');
        Route::post('submitFotoGateOut', 'Api\CheckpointDriver\LokasiMuatController@submitFotoGateOut');

        //NEW
        Route::get('detailLokasiMuat/{id}', 'Api\CheckpointDriver\LokasiMuatController@detailLokasiMuat');
        Route::get('gateInLokasiMuat/{id}', 'Api\CheckpointDriver\LokasiMuatController@gateInLokasiMuat');
        Route::get('gateOutLokasiMuat/{id}', 'Api\CheckpointDriver\LokasiMuatController@gateOutLokasiMuat');
    });
    Route::group(['prefix' => 'LokasiBongkar/'], function () {
        Route::post('submitFotoGateIn', 'Api\CheckpointDriver\LokasiBongkarController@submitFotoGateIn');
        Route::post('submitFotoGateOut', 'Api\CheckpointDriver\LokasiBongkarController@submitFotoGateOut');
        Route::get('gateOutLokasiBongkar/{job_no}', 'Api\CheckpointDriver\LokasiBongkarController@gateOutLokasiBongkar');
        Route::post('finishJob', 'Api\CheckpointDriver\LokasiBongkarController@finishJob');

        //NEW
        Route::get('gateInLokasiBongkar/{id}', 'Api\CheckpointDriver\LokasiBongkarController@gateInLokasiBongkar');
        Route::get('validasiLokasiBongkar/{token}', 'Api\CheckpointDriver\LokasiBongkarController@validasiLokasiBongkar');
        Route::get('detailLokasiBongkar/{id}', 'Api\CheckpointDriver\LokasiBongkarController@detailLokasiBongkar');
    });
});

Route::group(['prefix' => 'stuffingExport/'], function () {
    Route::post('login', 'Api\Export\Stuffing\StuffingController@login');
    Route::post('getList', 'Api\Export\Stuffing\StuffingController@getList');
    Route::post('getCounting', 'Api\Export\Stuffing\StuffingController@getCounting');
    Route::post('scanPalletTag', 'Api\Export\Stuffing\StuffingController@scanPalletTag');
    Route::get('mybranch/{username}', 'Api\Export\UserController@mybranch');
    // Route::get('detailPallet/{job_id}', 'Api\Export\Stuffing\StuffingController@detailPallet');
    // Route::get('headerPallet/{id}', 'Api\Export\Stuffing\StuffingController@headerPallet');
    // Route::get('getCargoNotCompleted/{id}', 'Api\Export\Stuffing\StuffingController@getCargoNotCompleted');
    // Route::get('getCargoCompleted/{id}', 'Api\Export\Stuffing\StuffingController@getCargoCompleted');
});

Route::group(['prefix' => 'exportAndroid/'], function () {
    Route::post('login', 'Api\Export\UserController@login');
    Route::get('mybranch/{username}', 'Api\Export\UserController@mybranch');

    //MASTER DATA
    Route::group(['prefix' => 'master/'], function () {
        Route::get('getVehicleNo', 'Api\Export\Master\MasterController@getVehicleNo');
        Route::get('getTransporter', 'Api\Export\Master\MasterController@getTransporter');
        Route::get('getForwarder/{username}', 'Api\Export\Master\MasterController@getForwarder');
        Route::get('getShipper/{username}', 'Api\Export\Master\MasterController@getShipper');
        Route::get('getConsignee/{username}', 'Api\Export\Master\MasterController@getConsignee');
        Route::get('getDestination', 'Api\Export\Master\MasterController@getDestination');
        Route::get('myBranch/{username}', 'Api\Export\Master\MasterController@branchMe');
        Route::get('getChecker/{username}', 'Api\Export\Master\MasterController@getChecker');
        Route::get('getLocation/{username}', 'Api\Export\Master\MasterController@getLocation');
        Route::get('getVehicleType', 'Api\Export\Master\MasterController@getVehicleType');
        Route::get('getUom', 'Api\Export\Master\MasterController@getUom');
    });

    //SECURITY
    Route::POST('outstandingGateOut', 'Api\Export\GateOutController@outstandingGateOut');

    //INBOUND
    Route::group(['prefix' => 'inbound/'], function () {
        Route::post('store', 'Api\Export\Inbound\JobController@store');
        Route::get('listMappingChecker/{username}', 'Api\Export\Inbound\JobController@listMappingChecker');
        Route::get('getJobMe/{username}', 'Api\Export\Inbound\JobController@getJobMe');
        Route::get('detailJob/{job_id}', 'Api\Export\Inbound\JobController@detailJob');
        Route::POST('postPIC', 'Api\Export\Inbound\JobController@postPIC');
        Route::POST('closeJobByChecker', 'Api\Export\Inbound\JobController@closeJobByChecker');

        Route::get('getJobChecker/{username}', 'Api\Export\Inbound\JobController@getJobChecker');
        Route::POST('storeFotoCargo', 'Api\Export\Inbound\DetailController@storeFotoCargo');
        Route::POST('storeFotoCargoDamage', 'Api\Export\Inbound\DetailController@storeFotoCargoDamage');
        Route::get('getFotoCargo/{job_id}/{po}', 'Api\Export\Inbound\DetailController@getFotoCargo');
        Route::get('getFotoCargoDamage/{job_id}/{po}', 'Api\Export\Inbound\DetailController@getFotoCargoDamage');
        Route::get('getDetailCargo/{job_id}/{po}', 'Api\Export\Inbound\DetailController@getDetailCargo');
        Route::get('resultPalletize/{job_id}/{po}', 'Api\Export\Inbound\DetailController@resultPalletize');
        Route::get('deletePallet/{job_id}/{id_detail}', 'Api\Export\Inbound\DetailController@deletePallet');
        Route::get('perkalianPallet/{job_id}/{id_detail}/{perkalian}/{po_number}', 'Api\Export\Inbound\DetailController@perkalianPallet');
        Route::POST('storeDetail', 'Api\Export\Inbound\DetailController@storeDetail');
        Route::POST('storeSignature', 'Api\Export\Inbound\DetailController@storeSignature');
        Route::get('listingPO/{job_id}', 'Api\Export\Inbound\DetailController@listingPO');
        Route::POST('storeFotoTruck', 'Api\Export\Inbound\DetailController@storeFotoTruck');
        Route::get('getFotoTruck/{job_id}', 'Api\Export\Inbound\DetailController@getFotoTruck');

        Route::get('getJobPutaway/{username}', 'Api\Export\Inbound\JobController@getJobPutaway');
        Route::get('detailPutaway/{job_id}', 'Api\Export\Inbound\JobController@detailPutaway');
        Route::get('finishPutaway/{job_id}', 'Api\Export\Inbound\JobController@finishPutaway');
        Route::get('getListDetailPutaway/{type}/{job_id}', 'Api\Export\Inbound\JobController@getListDetailPutaway');
        Route::POST('postScanPalletTag', 'Api\Export\Inbound\DetailController@postScanPalletTag');
        Route::POST('postScanLocation', 'Api\Export\Inbound\DetailController@postScanLocation');
        Route::get('cancelPutaway/{id_detail}', 'Api\Export\Inbound\JobController@cancelPutaway');
        Route::get('cancelPutaway/{id_detail}', 'Api\Export\Inbound\JobController@cancelPutaway');
        Route::POST('confirmPutaway', 'Api\Export\Inbound\JobController@confirmPutaway');
    });

    //OB 
    Route::group(['prefix' => 'ob/'], function () {
        Route::get('getJobOB/{type}/{username}', 'Api\Export\OB\JobController@getJobOB');
        Route::get('detailOB/{type}/{job_no}', 'Api\Export\OB\JobController@detailOB');
        Route::post('postScanPalletTag', 'Api\Export\OB\JobController@postScanPalletTag');
        Route::post('postScanLocation', 'Api\Export\OB\JobController@postScanLocation');
        Route::get('getFoto/{job_no}', 'Api\Export\OB\JobController@getFoto');
        Route::post('storeFoto', 'Api\Export\OB\JobController@storeFoto');
        Route::GET('confirmJobChecker/{job_no}', 'Api\Export\OB\JobController@confirmJobChecker');
    });

    //GATEIN
    Route::group(['prefix' => 'gatein/'], function () {
        Route::post('store', 'Api\Export\GateInController@store');
        Route::get('detailGateIn/{id}', 'Api\Export\GateInController@detailGateIn');
    });

    //GATEOUT
    Route::group(['prefix' => 'gateout/'], function () {
        Route::post('storeFoto', 'Api\Export\GateOutController@storeFoto');
        Route::get('getFoto/{id}', 'Api\Export\GateOutController@getFoto');
        Route::get('closedJob/{id}', 'Api\Export\GateOutController@closedJob');
    });
});

Route::group(['prefix' => 'import/FotoManagement'], function () {
    Route::post('storeJob', 'Api\Import\FotoManagement\FotoManagementController@storeJob');
    Route::post('scanBarcode', 'Api\Import\FotoManagement\FotoManagementController@scanBarcode');
    Route::post('checkJob', 'Api\Import\FotoManagement\FotoManagementController@checkJob');
    Route::post('checkFoto', 'Api\Import\FotoManagement\FotoManagementController@checkFoto');
    Route::post('storeFoto', 'Api\Import\FotoManagement\FotoManagementController@storeFoto');
    Route::post('getFoto', 'Api\Import\FotoManagement\FotoManagementController@getFoto');
    Route::post('confirmJob', 'Api\Import\FotoManagement\FotoManagementController@confirmJob');
    Route::get('mybranch/{username}', 'Api\Export\UserController@mybranch');
});

Route::group(['prefix' => 'export/ScanCargoExport'], function () {
    Route::post('login', 'Api\Export\ScanCargo\ScanCargoController@login');
    Route::post('postReceive', 'Api\Export\ScanCargo\ScanCargoController@postReceive');
    Route::GET('getListReceive/{username}', 'Api\Export\ScanCargo\ScanCargoController@getListReceive');
    Route::GET('generateJobNoReceive/{username}', 'Api\Export\ScanCargo\ScanCargoController@generateJobNoReceive');
    Route::GET('getListOutstanding/{job_no}', 'Api\Export\ScanCargo\ScanCargoController@getListOutstanding');
    Route::GET('deleteListReceive/{id}', 'Api\Export\ScanCargo\ScanCargoController@deleteListReceive');
    Route::GET('confirmJobReceive/{job_no}', 'Api\Export\ScanCargo\ScanCargoController@confirmJobReceive');
    //STUFFING
    Route::GET('generateJobNoStuffing/{username}', 'Api\Export\ScanCargo\ScanCargoController@generateJobNoStuffing');
    Route::POST('postStuffing', 'Api\Export\ScanCargo\ScanCargoController@postStuffing');
    Route::GET('getListStuffing/{job_no}', 'Api\Export\ScanCargo\ScanCargoController@getListStuffing');
    Route::GET('deleteListStuffing/{pallet}', 'Api\Export\ScanCargo\ScanCargoController@deleteListStuffing');
    Route::GET('confirmJobStuffing/{job_no}', 'Api\Export\ScanCargo\ScanCargoController@confirmJobStuffing');
});
