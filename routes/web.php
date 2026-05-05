<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', function () {
    // return view('email.stockEmail');
    return view('welcome');
});

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/about', 'ProfileController@about')->name('profile.about');
Route::get('/services', 'ProfileController@services')->name('profile.services');
Route::get('/contact', 'ProfileController@contact')->name('profile.contact');

Route::get('login', 'LoginController@index')->name('login');
Route::post('login/post', 'LoginController@postLogin')->name('login.post');

require base_path('routes/tracing-cust-export.php');
require base_path('routes/Foto-Management-Import.php');
require base_path('routes/fin-tax.php');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home/generate', 'HomeController@generate')->name('dashboard');

    Route::get('/dashboard/location', 'Dashboard\LocationController@index');
    Route::post('/dashboard/location/refresh', 'Dashboard\LocationController@refresh')->name("dashboard-location.refresh");
    Route::get('/dashboard/location/detail/{id}', 'Dashboard\LocationController@getLocationDetail');

    Route::get('/dashboard/chart', 'Dashboard\ChartController@index')->name("dashboard-chart.index");
    Route::post('/dashboard/chart/data', 'Dashboard\ChartController@getData')->name("dashboard-chart.data");
    Route::post('/dashboard/chart/print', 'Dashboard\ChartController@print')->name("dashboard-chart.print");

    Route::get('/settings/email', 'Settings\EmailController@index')->name('settings-email.index');
    Route::get('/settings/email/{id}/edit', 'Settings\EmailController@edit');
    Route::post('/settings/email/store', 'Settings\EmailController@store')->name('settings-email.store');
    Route::delete('/settings/email/{id}', 'Settings\EmailController@destroy')->name('settings-email.destroy');

    Route::get('/settings/email-principal', 'Settings\EmailPrincipalController@index')->name('settings-email-principal.index');
    Route::get('/settings/email-principal/{id}/edit', 'Settings\EmailPrincipalController@edit');
    Route::post('/settings/email-principal/store', 'Settings\EmailPrincipalController@store')->name('settings-email-principal.store');
    Route::delete('/settings/email-principal/{id}', 'Settings\EmailPrincipalController@destroy')->name('settings-email-principal.destroy');

    Route::get('/admin/role', 'Admin\RoleController@index')->name('role.index')->middleware('can:isAdmin');
    Route::get('/admin/role/{id}/edit', 'Admin\RoleController@edit')->middleware('can:isAdmin');
    Route::post('/admin/role/store', 'Admin\RoleController@store')->name('role.store')->middleware('can:isAdmin');
    Route::delete('/admin/role/{id}', 'Admin\RoleController@destroy')->name('role.destroy')->middleware('can:isAdmin');

    Route::get('/admin/menu', 'Admin\MenuController@index')->name('menu.index')->middleware('can:isAdmin');
    Route::get('/admin/menu/{id}/edit', 'Admin\MenuController@edit')->middleware('can:isAdmin');
    Route::post('/admin/menu/store', 'Admin\MenuController@store')->name('menu.store')->middleware('can:isAdmin');
    Route::delete('/admin/menu/{id}', 'Admin\MenuController@destroy')->name('menu.destroy')->middleware('can:isAdmin');

    Route::get('/admin/user', 'Admin\UserController@index')->name('user.index')->middleware('can:isAdmin');
    Route::get('/admin/user/{id}/edit', 'Admin\UserController@edit')->middleware('can:isAdmin');
    Route::post('/admin/user/store', 'Admin\UserController@store')->name('user.store')->middleware('can:isAdmin');
    Route::delete('/admin/user/{id}', 'Admin\UserController@destroy')->name('user.destroy')->middleware('can:isAdmin');

    Route::get('/admin/user-site', 'Admin\UserSiteController@index')->name('user-site.index');
    Route::delete('/admin/user-site/{user_id}/{id}', 'Admin\UserSiteController@destroy')->name('user-site.destroy');
    Route::post('/admin/user-site/store', 'Admin\UserSiteController@store')->name('user-site.store');

    Route::get('/admin/user-principal', 'Admin\UserPrincipalController@index')->name('user-principal.index');
    Route::delete('/admin/user-principal/{user_id}/{id}', 'Admin\UserPrincipalController@destroy')->name('user-principal.destroy');
    Route::post('/admin/user-principal/store', 'Admin\UserPrincipalController@store')->name('user-principal.store');

    Route::get('/admin/user-branch', 'Admin\UserBranchController@index')->name('user-branch.index');
    Route::delete('/admin/user-branch/{user_id}/{id}', 'Admin\UserBranchController@destroy')->name('user-branch.destroy');
    Route::post('/admin/user-branch/store', 'Admin\UserBranchController@store')->name('user-branch.store');

    Route::get('/admin/user-menu', 'Admin\MenuUserController@index')->name('user-menu.index');
    Route::post('/admin/user-menu/store', 'Admin\MenuUserController@store')->name('user-menu.store');

    Route::get('/reference/currency', 'Reference\CurrencyController@index')->name('currency.index');
    Route::get('/reference/currency/{id}/edit', 'Reference\CurrencyController@edit');
    Route::post('/reference/currency/store', 'Reference\CurrencyController@store')->name('currency.store');
    Route::delete('/reference/currency/{id}', 'Reference\CurrencyController@destroy')->name('currency.destroy');

    Route::get('/reference/country', 'Reference\CountryController@index')->name('country.index');
    Route::get('/reference/country/{id}/edit', 'Reference\CountryController@edit');
    Route::post('/reference/country/store', 'Reference\CountryController@store')->name('country.store');
    Route::delete('/reference/country/{id}', 'Reference\CountryController@destroy')->name('country.destroy');

    Route::get('/reference/region', 'Reference\RegionController@index')->name('region.index');
    Route::get('/reference/region/{id}/edit', 'Reference\RegionController@edit');
    Route::post('/reference/region/store', 'Reference\RegionController@store')->name('region.store');
    Route::delete('/reference/region/{id}', 'Reference\RegionController@destroy')->name('region.destroy');

    Route::get('/reference/city', 'Reference\CityController@index')->name('city.index');
    Route::get('/reference/city/{id}/edit', 'Reference\CityController@edit');
    Route::post('/reference/city/store', 'Reference\CityController@store')->name('city.store');
    Route::delete('/reference/city/{id}', 'Reference\CityController@destroy')->name('city.destroy');

    Route::get('/reference/uom', 'Reference\UoMController@index')->name('uom.index');
    Route::get('/reference/uom/{id}/edit', 'Reference\UoMController@edit');
    Route::post('/reference/uom/store', 'Reference\UoMController@store')->name('uom.store');
    Route::delete('/reference/uom/{id}', 'Reference\UoMController@destroy')->name('uom.destroy');

    Route::get('/master/company', 'Master\CompanyController@index')->name('company.index');
    Route::get('/master/company/{id}/edit', 'Master\CompanyController@edit');
    Route::post('/master/company/store', 'Master\CompanyController@store')->name('company.store');
    Route::delete('/master/company/{id}', 'Master\CompanyController@destroy')->name('company.destroy');

    Route::get('/product-master/principal', 'Master\PrincipalController@index')->name('principal.index');
    Route::get('/product-master/principal/create', 'Master\PrincipalController@create')->name('principal.create');
    Route::get('/product-master/principal/edit/{id}', 'Master\PrincipalController@edit');
    Route::post('/product-master/principal/store', 'Master\PrincipalController@store')->name('principal.store');
    Route::delete('/product-master/principal/{id}', 'Master\PrincipalController@destroy')->name('principal.destroy');

    Route::get('/product-master/principal-branch', 'Master\PrincipalBranchController@index')->name('principal-branch.index');
    Route::delete('/product-master/principal-branch/{principal_id}/{id}', 'Master\PrincipalBranchController@destroy')->name('principal-branch.destroy');
    Route::post('/product-master/principal-branch/store', 'Master\PrincipalBranchController@store')->name('principal-branch.store');

    Route::get('/product-master/principal-site', 'Master\PrincipalSiteController@index')->name('principal-site.index');
    Route::delete('/product-master/principal-site/{principal_id}/{id}', 'Master\PrincipalSiteController@destroy')->name('principal-site.destroy');
    Route::post('/product-master/principal-site/store', 'Master\PrincipalSiteController@store')->name('principal-site.store');

    Route::get('/product-master/storage', 'Master\StorageController@index')->name('storage.index');
    Route::post('/product-master/storage/edit', 'Master\StorageController@edit')->name('storage.edit');
    Route::delete('/product-master/storage/{id}', 'Master\StorageController@destroy')->name('storage.destroy');
    Route::post('/product-master/storage/store', 'Master\StorageController@store')->name('storage.store');

    Route::get('/product-master/handling', 'Master\HandlingController@index')->name('handling.index');
    Route::post('/product-master/handling/edit', 'Master\HandlingController@edit')->name('handling.edit');
    Route::delete('/product-master/handling/{id}', 'Master\HandlingController@destroy')->name('handling.destroy');
    Route::post('/product-master/handling/store', 'Master\HandlingController@store')->name('handling.store');

    Route::get('/customer-master/customer-group', 'Master\CustomerGroupController@index')->name('customer-group.index');
    Route::get('/customer-master/customer-group/{id}/edit', 'Master\CustomerGroupController@edit');
    Route::post('/customer-master/customer-group/store', 'Master\CustomerGroupController@store')->name('customer-group.store');
    Route::delete('/customer-master/customer-group/{id}', 'Master\CustomerGroupController@destroy')->name('customer-group.destroy');

    Route::get('/customer-master/customer-type', 'Master\CustomerTypeController@index')->name('customer-type.index');
    Route::get('/customer-master/customer-type/{id}/edit', 'Master\CustomerTypeController@edit');
    Route::post('/customer-master/customer-type/store', 'Master\CustomerTypeController@store')->name('customer-type.store');
    Route::delete('/customer-master/customer-type/{id}', 'Master\CustomerTypeController@destroy')->name('customer-type.destroy');

    Route::get('/customer-master/store', 'Master\StoreController@index')->name('customer-store.index');
    Route::get('/customer-master/store/{id}/edit', 'Master\StoreController@edit');
    Route::post('/customer-master/store/store', 'Master\StoreController@store')->name('customer-store.store');
    Route::delete('/customer-master/store/{id}', 'Master\StoreController@destroy')->name('customer-store.destroy');
    Route::get('/customer-master/store/customer', 'Master\StoreController@customer')->name('customer-store.customer');

    Route::get('/customer-master/customer', 'Master\CustomerController@index')->name('customer.index');
    Route::get('/customer-master/customer/{id}/edit', 'Master\CustomerController@edit');
    Route::post('/customer-master/customer/store', 'Master\CustomerController@store')->name('customer.store');
    Route::delete('/customer-master/customer/{id}', 'Master\CustomerController@destroy')->name('customer.destroy');
    Route::get('/customer-master/customer/reference', 'Master\CustomerController@reference')->name('customer.reference');
    Route::get('/auto/customer-master/store', 'Master\AutoCompleteController@getStore')->name('customer.getStore');

    Route::get('/master/job-class', 'Master\JobClassController@index')->name('job-class.index');
    Route::get('/master/job-class/{id}/edit', 'Master\JobClassController@edit');
    Route::post('/master/job-class/store', 'Master\JobClassController@store')->name('job-class.store');
    Route::delete('/master/job-class/{id}', 'Master\JobClassController@destroy')->name('job-class.destroy');

    Route::get('/master/container-size', 'Master\ContainerSizeController@index')->name('container-size.index');
    Route::get('/master/container-size/{id}/edit', 'Master\ContainerSizeController@edit');
    Route::post('/master/container-size/store', 'Master\ContainerSizeController@store')->name('container-size.store');
    Route::delete('/master/container-size/{id}', 'Master\ContainerSizeController@destroy')->name('container-size.destroy');

    Route::get('/master/container-type', 'Master\ContainerTypeController@index')->name('container-type.index');
    Route::get('/master/container-type/{id}/edit', 'Master\ContainerTypeController@edit');
    Route::post('/master/container-type/store', 'Master\ContainerTypeController@store')->name('container-type.store');
    Route::delete('/master/container-type/{id}', 'Master\ContainerTypeController@destroy')->name('container-type.destroy');

    Route::get('/master/mode', 'Master\ModeOfTransportController@index')->name('mode.index');
    Route::get('/master/mode/{id}/edit', 'Master\ModeOfTransportController@edit');
    Route::post('/master/mode/store', 'Master\ModeOfTransportController@store')->name('mode.store');
    Route::delete('/master/mode/{id}', 'Master\ModeOfTransportController@destroy')->name('mode.destroy');

    Route::get('/site-master/site-type', 'Master\SiteTypeController@index')->name('site-type.index');
    Route::get('/site-master/site-type/{id}/edit', 'Master\SiteTypeController@edit');
    Route::post('/site-master/site-type/store', 'Master\SiteTypeController@store')->name('site-type.store');
    Route::delete('/site-master/site-type/{id}', 'Master\SiteTypeController@destroy')->name('site-type.destroy');

    Route::get('/site-master/site-indicator', 'Master\SiteIndicatorController@index')->name('site-indicator.index');
    Route::get('/site-master/site-indicator/{id}/edit', 'Master\SiteIndicatorController@edit');
    Route::post('/site-master/site-indicator/store', 'Master\SiteIndicatorController@store')->name('site-indicator.store');
    Route::delete('/site-master/site-indicator/{id}', 'Master\SiteIndicatorController@destroy')->name('site-indicator.destroy');

    Route::get('/site-master/location-type', 'Master\LocationTypeController@index')->name('location-type.index');
    Route::get('/site-master/location-type/{id}/edit', 'Master\LocationTypeController@edit');
    Route::post('/site-master/location-type/store', 'Master\LocationTypeController@store')->name('location-type.store');
    Route::delete('/site-master/location-type/{id}', 'Master\LocationTypeController@destroy')->name('location-type.destroy');

    Route::get('/site-master/location-status', 'Master\LocationStatusController@index')->name('location-status.index');
    Route::get('/site-master/location-status/{id}/edit', 'Master\LocationStatusController@edit');
    Route::post('/site-master/location-status/store', 'Master\LocationStatusController@store')->name('location-status.store');
    Route::delete('/site-master/location-status/{id}', 'Master\LocationStatusController@destroy')->name('location-status.destroy');

    Route::get('/site-master/site', 'Master\SiteController@index')->name('site.index');
    Route::get('/site-master/site/{id}/edit', 'Master\SiteController@edit');
    Route::post('/site-master/site/store', 'Master\SiteController@store')->name('site.store');
    Route::delete('/site-master/site/{id}', 'Master\SiteController@destroy')->name('site.destroy');
    Route::get('/site-master/site/indicator', 'Master\SiteController@indicator')->name('site.indicator');

    Route::get('/site-master/area', 'Master\SiteAreaController@index')->name('site-area.index');
    Route::get('/site-master/area/{id}/edit', 'Master\SiteAreaController@edit');
    Route::post('/site-master/area/store', 'Master\SiteAreaController@store')->name('site-area.store');
    Route::delete('/site-master/area/{id}', 'Master\SiteAreaController@destroy')->name('site-area.destroy');

    Route::get('/site-master/location', 'Master\LocationController@index')->name('location.index');
    Route::get('/site-master/location/{id}/edit', 'Master\LocationController@edit');
    Route::post('/site-master/location/store', 'Master\LocationController@store')->name('location.store');
    Route::delete('/site-master/location/{id}', 'Master\LocationController@destroy')->name('location.destroy');
    Route::get('/site-master/location/area', 'Master\LocationController@area')->name('location.area');
    Route::get('/site-master/location/print/{site_id}/{area_id}', 'Master\LocationController@print');

    Route::get('/product-master/manufactur', 'Master\ManufacturController@index')->name('manufactur.index');
    Route::get('/product-master/manufactur/{id}/edit', 'Master\ManufacturController@edit');
    Route::post('/product-master/manufactur/store', 'Master\ManufacturController@store')->name('manufactur.store');
    Route::delete('/product-master/manufactur/{id}', 'Master\ManufacturController@destroy')->name('manufactur.destroy');

    Route::get('/product-master/product-category', 'Master\ProductCategoryController@index')->name('product-category.index');
    Route::get('/product-master/product-category/{id}/edit', 'Master\ProductCategoryController@edit');
    Route::post('/product-master/product-category/store', 'Master\ProductCategoryController@store')->name('product-category.store');
    Route::delete('/product-master/product-category/{id}', 'Master\ProductCategoryController@destroy')->name('product-category.destroy');

    Route::get('/product-master/product-group', 'Master\ProductGroupController@index')->name('product-group.index');
    Route::get('/product-master/product-group/{id}/edit', 'Master\ProductGroupController@edit');
    Route::post('/product-master/product-group/store', 'Master\ProductGroupController@store')->name('product-group.store');
    Route::delete('/product-master/product-group/{id}', 'Master\ProductGroupController@destroy')->name('product-group.destroy');

    Route::get('/product-master/product-brand', 'Master\ProductBrandController@index')->name('product-brand.index');
    Route::get('/product-master/product-brand/{id}/edit', 'Master\ProductBrandController@edit');
    Route::post('/product-master/product-brand/store', 'Master\ProductBrandController@store')->name('product-brand.store');
    Route::delete('/product-master/product-brand/{id}', 'Master\ProductBrandController@destroy')->name('product-brand.destroy');
    Route::get('/product-master/product-brand/group', 'Master\ProductBrandController@group')->name('product-brand.group');

    Route::get('/product-master/product', 'Master\ProductController@index')->name('product.index');
    Route::POST('/product-master/upload', 'Master\ProductController@upload')->name('upload-product-master');
    Route::get('/product-master/product/{id}/edit', 'Master\ProductController@edit');
    Route::post('/product-master/product/store', 'Master\ProductController@store')->name('product.store');
    Route::delete('/product-master/product/{id}', 'Master\ProductController@destroy')->name('product.destroy');
    Route::get('/product-master/product/reference', 'Master\ProductController@reference')->name('product.reference');
    Route::get('/product-master/product/brand', 'Master\ProductController@brand')->name('product.brand');

    Route::get('/product-master/pallet-unit', 'Master\PalletUnitController@index')->name('pallet-unit.index');
    Route::get('/product-master/pallet-unit/{id}/edit', 'Master\PalletUnitController@edit');
    Route::post('/product-master/pallet-unit/store', 'Master\PalletUnitController@store')->name('pallet-unit.store');
    Route::delete('/product-master/pallet-unit/{id}', 'Master\PalletUnitController@destroy')->name('pallet-unit.destroy');

    /* Inbound Job */
    Route::get('/warehouse/inbound', 'Transaction\Inbound\JobController@index')->name('inbound-job.index');
    /* yulio */
    Route::get('/warehouse/inbound/create/{id}', 'Transaction\Inbound\JobController@create')->name('inbound-job.create');
    Route::get('/warehouse/inbound/job', 'Transaction\Inbound\JobController@edit')->name('inbound-job.edit');
    Route::post('/warehouse/inbound/store', 'Transaction\Inbound\JobController@store')->name('inbound-job.store');
    Route::post('/warehouse/inbound/add_per_pallet', 'Transaction\Inbound\JobController@add_per_pallet')->name('inbound.add_per_pallet');
    Route::GET('/warehouse/inbound/bypass/{inbound_id}', 'Transaction\Inbound\JobController@byPassScan');
    /* End Inbound Job */

    /* Inbound Vehicle */
    Route::get('/warehouse/inbound/vehicle/index', 'Transaction\Inbound\VehicleController@index')->name('inbound-vehicle.index');
    Route::post('/warehouse/inbound/vehicle', 'Transaction\Inbound\VehicleController@store')->name('inbound-vehicle.store');
    Route::post('/warehouse/inbound/vehicle/edit', 'Transaction\Inbound\VehicleController@edit')->name('inbound-vehicle.edit');
    Route::delete('/warehouse/inbound/vehicle/destroy', 'Transaction\Inbound\VehicleController@destroy')->name('inbound-vehicle.destroy');
    /* End Inbound Vehicle */

    /* Inbound Detail Packing */
    Route::get('/warehouse/inbound/detail/index', 'Transaction\Inbound\DetailController@index')->name('inbound-detail.index');
    Route::post('/warehouse/inbound/detail/edit', 'Transaction\Inbound\DetailController@edit')->name('inbound-detail.edit');
    Route::post('/warehouse/inbound/detail', 'Transaction\Inbound\DetailController@store')->name('inbound-detail.store');
    Route::delete('/warehouse/inbound/detail/destroy', 'Transaction\Inbound\DetailController@destroy')->name('inbound-detail.destroy');
    Route::post('/warehouse/inbound/detail/import', 'Transaction\Inbound\DetailController@import')->name('inbound-detail.import');
    Route::get('/warehouse/inbound/detail/export', 'Transaction\Inbound\DetailController@export');
    /* yulio */
    Route::get('/warehouse/inbound/detailPallet/{picking_id}/{inbound_id}/{product_code}', 'Transaction\Inbound\DetailController@detailPallet');
    Route::GET('/warehouse/inbound/scanLokasi/{qr}', 'Transaction\Inbound\DetailController@getScanLokasi');
    Route::POST('/warehouse/inbound/scanLokasi', 'Transaction\Inbound\DetailController@postScanLokasi')->name('inbound-scan_lokasi');
    /* End Inbound Detail Packing */

    /* Inbound Detail Packing */
    Route::get('/warehouse/inbound/manual/index', 'Transaction\Inbound\ManualPutawayController@index')->name('inbound-manual.index');
    Route::post('/warehouse/inbound/manual/edit', 'Transaction\Inbound\ManualPutawayController@edit')->name('inbound-manual.edit');
    Route::post('/warehouse/inbound/manual', 'Transaction\Inbound\ManualPutawayController@store')->name('inbound-manual.store');
    /* End Inbound Detail Packing */

    /* Inbound GRN */
    Route::get('/warehouse/inbound/grn/index', 'Transaction\Inbound\GRNController@index')->name('inbound-grn.index');
    Route::post('/warehouse/inbound/grn', 'Transaction\Inbound\GRNController@store')->name('inbound-grn.store');
    Route::post('/warehouse/inbound/grn/submit', 'Transaction\Inbound\GRNController@submit')->name('inbound-grn.submit');
    /* End Inbound GRN */

    /* Inbound Put Away */
    Route::get('/warehouse/inbound/putaway/index', 'Transaction\Inbound\PutawayController@index')->name('inbound-putaway.index');
    Route::get('warehouse/inbound/startPutaway/getLocationAvail/{inbound_id}', 'Transaction\Inbound\PutawayController@getLocationAvail');
    Route::get('/warehouse/inbound/startPutaway/{inbound_id}/{product_id}/{picking_id}', 'Transaction\Inbound\PutawayController@startPutaway');
    Route::get('/warehouse/inbound/startPutaway/getListPutaway/{picking_id}', 'Transaction\Inbound\PutawayController@getListPutaway');

    Route::get('/export/inbound/scanCtn/', 'Transaction\Inbound\ScanCtnController@startScan');
    Route::POST('/export/inbound/scanCtn/submit', 'Transaction\Inbound\ScanCtnController@submit');
    Route::GET('/export/inbound/scanCtn/delete/{id}', 'Transaction\Inbound\ScanCtnController@deleteCtn');
    Route::get('/export/inbound/scanCtn/save/{po_number}', 'Transaction\Inbound\ScanCtnController@updateWhenFinish');
    Route::get('/export/inbound/scanCtn/outstanding', 'Transaction\Inbound\ScanCtnController@OutstandView');
    Route::POST('/export/inbound/scanCtn/outstanding/search', 'Transaction\Inbound\ScanCtnController@showListOutstanding');
    Route::get('/export/inbound/scanCtn/details', 'Transaction\Inbound\ScanCtnController@getPODetails');
    Route::get('/export/inbound/scanCtn/modal/deleteQty/{id}/{po}/{start}/{end}/{status}', 'Transaction\Inbound\ScanCtnController@deleteItem');
    Route::POST('/export/inbound/scanCtn/confirmAll', 'Transaction\Inbound\ScanCtnController@outstandUpdate');
    Route::POST('/export/inbound/scanCtn/add-row', 'Transaction\Inbound\ScanCtnController@addRow');
    Route::get('/export/inbound/scanCtn/resumeScan/{po}', 'Transaction\Inbound\ScanCtnController@resumeScan');
    Route::get('/export/inbound/scanCtn/editQtyActual/{po}/{qty}', 'Transaction\Inbound\ScanCtnController@editQtyActual');
    Route::get('/export/inbound/scanCtn/tagPartial/{po}', 'Transaction\Inbound\ScanCtnController@tagPartial');
    Route::get('/export/inbound/scanCtn/partial', 'Transaction\Inbound\ScanCtnController@partial');

    Route::get('/warehouse/inbound/scanPalletTag/{qrcode}/{id_per_pallet}/{product_code}', 'Transaction\Inbound\PutawayController@scanPalletTag');
    Route::POST('/warehouse/inbound/scanPalletTag', 'Transaction\Inbound\PutawayController@postScanPalletTag')->name('inbound-scan_pallet_tag');
    Route::post('/warehouse/inbound/putaway/submit', 'Transaction\Inbound\PutawayController@submit')->name('inbound-putaway.submit');
    Route::POST('/warehouse/inbound/edit_location_putaway', 'Transaction\Inbound\PutawayController@editLocation')->name('inbound-edit_location_putaway');
    /* End Inbound Put Away */

    /* Inbound Cancel */
    Route::get('/warehouse/inbound/cancel/index', 'Transaction\Inbound\CancelController@index')->name('inbound-cancel.index');
    Route::post('/warehouse/inbound/cancel/submit', 'Transaction\Inbound\CancelController@submit')->name('inbound-cancel.submit');
    /* End Inbound Cancel */

    /* Inbound Batch */
    Route::get('/warehouse/inbound/pallet/index', 'Transaction\Inbound\BatchController@pallet')->name('inbound-pallet.index');
    Route::post('/warehouse/inbound/pallet/store', 'Transaction\Inbound\BatchController@palletStore')->name('inbound-pallet.store');
    Route::post('/warehouse/inbound/confirm/edit', 'Transaction\Inbound\BatchController@edit')->name('inbound-confirm.edit');
    Route::post('/warehouse/inbound/confirm/store', 'Transaction\Inbound\BatchController@store')->name('inbound-confirm.store');
    Route::get('/warehouse/inbound/confirm/index', 'Transaction\Inbound\BatchController@index')->name('inbound-confirm.index');
    Route::post('/warehouse/inbound/confirm/submit', 'Transaction\Inbound\BatchController@submit')->name('inbound-confirm.submit');
    Route::get('/warehouse/inbound/confirm/export', 'Transaction\Inbound\ReportController@export');
    Route::post('/warehouse/inbound/postEditLokasiBatch', 'Transaction\Inbound\BatchController@postEditLokasiBatch')->name('inbound.edit_lokasi_batch');
    Route::GET('/warehouse/inbound/getEditLokasiBatch/{id_batch}', 'Transaction\Inbound\BatchController@getEditLokasiBatch');
    /* End Inbound Batch */

    /* Inbound Cross Dock */
    Route::get('/warehouse/inbound/crossdock/index', 'Transaction\Inbound\CrossDockController@index')->name('inbound-crossdock.index');
    Route::post('/warehouse/inbound/crossdock/store', 'Transaction\Inbound\CrossDockController@store')->name('inbound-crossdock.store');
    /* End Inbound Cancel */

    /* Outbound Job */
    Route::get('/warehouse/outbound', 'Transaction\Outbound\JobController@index')->name('outbound-job.index');
    Route::get('/warehouse/outbound/create/{id}', 'Transaction\Outbound\JobController@create')->name('outbound-job.create');
    Route::get('/warehouse/outbound/job', 'Transaction\Outbound\JobController@edit')->name('outbound-job.edit');
    Route::post('/warehouse/outbound/store', 'Transaction\Outbound\JobController@store')->name('outbound-job.store');
    Route::get('/warehouse/outbound/updateEtd/{tgl}/{outbound_id}', 'Transaction\Outbound\JobController@updateEtd');
    Route::get('/warehouse/outbound/getListPickByChecker/{outbound_id}', 'Transaction\Outbound\JobController@getListPickByChecker');
    /* Yulio */
    Route::get('/warehouse/outbound/scanLokasi/{location_code}', 'Transaction\Outbound\JobController@scanLokasi');
    Route::get('/warehouse/outbound/scanPalletTag/{product_code}/{id_batch}', 'Transaction\Outbound\JobController@scanPalletTag');
    Route::POST('/warehouse/outbound/validasiQtyBatch', 'Transaction\Outbound\JobController@validasiQtyBatch')->name('outbound.validasi_qty_batch');
    Route::POST('/warehouse/outbound/postScanLokasi', 'Transaction\Outbound\JobController@postScanLokasi')->name('outbound-scan_lokasi');
    Route::GET('/warehouse/outbound/bypass/{outbound_id}', 'Transaction\Outbound\JobController@byPassScan');
    /* End Outbound Job */

    /* Outbound Order */
    Route::get('/warehouse/outbound/order/index', 'Transaction\Outbound\OrderController@index')->name('outbound-order.index');
    Route::post('/warehouse/outbound/order', 'Transaction\Outbound\OrderController@store')->name('outbound-order.store');
    Route::get('/warehouse/outbound/order/edit', 'Transaction\Outbound\OrderController@edit')->name('outbound-order.edit');
    Route::delete('/warehouse/outbound/order/destroy', 'Transaction\Outbound\OrderController@destroy')->name('outbound-order.destroy');
    /* End Outbound Order */

    /* Outbound Detail */
    Route::get('/warehouse/outbound/detail/index', 'Transaction\Outbound\DetailController@index')->name('outbound-detail.index');
    Route::post('/warehouse/outbound/detail', 'Transaction\Outbound\DetailController@store')->name('outbound-detail.store');
    Route::get('/warehouse/outbound/detail/edit', 'Transaction\Outbound\DetailController@edit')->name('outbound-detail.edit');
    Route::delete('/warehouse/outbound/detail/destroy', 'Transaction\Outbound\DetailController@destroy')->name('outbound-detail.destroy');
    Route::post('/warehouse/outbound/detail/import', 'Transaction\Outbound\DetailController@import')->name('outbound-detail.import');
    Route::get('/warehouse/outbound/detail/export/{id}', 'Transaction\Outbound\DetailController@export');
    /* End Outbound Detail */

    /* Outbound Picking */
    Route::get('/warehouse/outbound/picking/index', 'Transaction\Outbound\PickingController@index')->name('outbound-picking.index');
    Route::post('/warehouse/outbound/picking/submit', 'Transaction\Outbound\PickingController@submit')->name('outbound-picking.submit');
    /* End Outbound Picking */

    /* Outbound Cancel */
    Route::get('/warehouse/outbound/cancel/index', 'Transaction\Outbound\CancelController@index')->name('outbound-cancel.index');
    Route::post('/warehouse/outbound/cancel/submit', 'Transaction\Outbound\CancelController@submit')->name('outbound-cancel.submit');
    /* End Outbound Cancel */

    /* Outbound Despatch */
    Route::get('/warehouse/outbound/despatch/index', 'Transaction\Outbound\DespatchController@index')->name('outbound-despatch.index');
    Route::get('/warehouse/outbound/despatch/edit', 'Transaction\Outbound\DespatchController@edit')->name('outbound-despatch.edit');
    Route::post('/warehouse/outbound/despatch/store', 'Transaction\Outbound\DespatchController@store')->name('outbound-despatch.store');
    /* End Outbound Despatch */

    /* Outbound Batch */
    Route::get('/warehouse/outbound/confirm/index', 'Transaction\Outbound\BatchController@index')->name('outbound-confirm.index');
    Route::post('/warehouse/outbound/confirm/submit', 'Transaction\Outbound\BatchController@submit')->name('outbound-confirm.submit');
    Route::get('/warehouse/outbound/mapping_lokasi', 'Transaction\Outbound\BatchController@mapping_lokasi');
    Route::get('/warehouse/outbound/getListDetailMapping/{job_no}', 'Transaction\Outbound\BatchController@getListDetailMapping');
    Route::POST('/warehouse/outbound/postMappingLokasi', 'Transaction\Outbound\BatchController@postMappingLokasi')->name('postMappingLokasi');
    Route::get('/warehouse/outbound/confirm/export', 'Transaction\Outbound\ReportController@export');
    /* End Outbound Batch */

    Route::get('/warehouse/report/pending', 'Report\PendingTransactionController@index')->name('pending-report.index');
    Route::post('/warehouse/report/pending/print', 'Report\PendingTransactionController@print')->name('pending-report.print');
    Route::get('/warehouse/report/pending/export', 'Report\PendingTransactionController@export');

    Route::get('/warehouse/stock-freeze', 'Transaction\Stock\FreezeController@index')->name('stock-freeze.index');
    Route::post('/warehouse/stock-freeze/submit', 'Transaction\Stock\FreezeController@submit')->name('stock-freeze.submit');

    /* Warehouse Report */
    Route::get('/warehouse/inbound/report/{type}/{id}/{product_code?}/{picking_id?}', 'Transaction\Inbound\ReportController@index')->name('inbound-report.report');
    Route::get('/warehouse/outbound/report/{type}/{id}', 'Transaction\Outbound\ReportController@index')->name('outbound-report.report');
    Route::get('/warehouse/outbound/palletPickingReport/{id}', 'Transaction\Outbound\ReportController@palletPickingReport');
    Route::get('/warehouse/outbound/palletPickingReportExcel/{id}', 'Transaction\Outbound\ReportController@export');
    Route::get('/warehouse/stock-report', 'Transaction\Stock\LedgerController@index')->name('stock-report.index');
    Route::post('/warehouse/stock-report', 'Transaction\Stock\LedgerController@report')->name('stock-report.report');
    Route::get('/warehouse/stock-report/export', 'Transaction\Stock\LedgerController@export');
    Route::get('/warehouse/transaction-report', 'Transaction\Stock\TransactionController@index')->name('transaction-report.index');
    Route::post('/warehouse/transaction-report', 'Transaction\Stock\TransactionController@report')->name('transaction-report.report');

    //Route::get('/warehouse/transaction-report/print_jasper/{id}', 'Transaction\Stock\TransactionController@print_jasper');
    Route::get('/warehouse/transaction-report/print_jasper/{id}', 'Transaction\Stock\TransactionController@print_jasper')->name('transaction.print_jasper');


    Route::get('/warehouse/transaction-report/export', 'Transaction\Stock\TransactionController@export');
    Route::post('/warehouse/outbound/report/despatch', 'Transaction\Outbound\ReportController@addRemarks')->name('addRemarksDespatch');
    Route::POST('/warehouse/inbound/report/allPallet', 'Transaction\Inbound\ReportController@allPallet');

    //NEW
    Route::get('/warehouse/transaction-report-years', 'Transaction\Stock\TransactionController@indexYears');
    Route::post('/warehouse/transaction-report-years', 'Transaction\Stock\TransactionController@reportYears')->name('transaction-report.reportYears');
    Route::get('/warehouse/transaction-report-years/export', 'Transaction\Stock\TransactionController@exportYears');

    Route::get('/warehouse/handling-report', 'Transaction\Stock\HandlingController@index')->name('handling-report.index');
    Route::post('/warehouse/handling-report', 'Transaction\Stock\HandlingController@report')->name('handling-report.report');
    Route::get('/warehouse/handling-report/export', 'Transaction\Stock\HandlingController@export');

    /* Stock Tranfer */
    Route::get('/inventory/stock-transfer', 'Transaction\Transfer\JobController@index')->name('transfer-job.index');
    Route::get('/inventory/stock-transfer/create/{id}', 'Transaction\Transfer\JobController@create')->name('transfer-job.create');
    Route::get('/inventory/stock-transfer/job', 'Transaction\Transfer\JobController@edit')->name('transfer-job.edit');
    Route::post('/inventory/stock-transfer/store', 'Transaction\Transfer\JobController@store')->name('transfer-job.store');

    Route::GET('/inventory/stock-transfer/downloadTemplate/{job_id}', 'Transaction\Transfer\JobController@downloadTemplate');

    Route::get('/inventory/stock-transfer/index', 'Transaction\Transfer\DetailController@index')->name('transfer-detail.index');
    Route::get('/inventory/stock-transfer/stock-list', 'Transaction\Transfer\DetailController@stockList')->name('transfer-detail.stockList');
    Route::post('/inventory/stock-transfer/edit', 'Transaction\Transfer\DetailController@edit')->name("transfer-detail.edit");
    Route::post('/inventory/stock-transfer/detail', 'Transaction\Transfer\DetailController@store')->name('transfer-detail.store');
    Route::delete('/inventory/stock-transfer/destroy', 'Transaction\Transfer\DetailController@destroy')->name('transfer-detail.destroy');

    Route::post('/inventory/stock-transfer/upload', 'Transaction\Transfer\DetailController@upload');

    Route::get('/inventory/stock-transfer/process/index', 'Transaction\Transfer\ProcessController@index')->name('transfer-process.index');
    Route::post('/inventory/stock-transfer/process/submit', 'Transaction\Transfer\ProcessController@submit')->name('transfer-process.submit');

    Route::get('/inventory/stock-transfer/cancel/index', 'Transaction\Transfer\CancelController@index')->name('transfer-cancel.index');
    Route::post('/inventory/stock-transfer/cancel/submit', 'Transaction\Transfer\CancelController@submit')->name('transfer-cancel.submit');

    Route::get('/inventory/stock-transfer/confirm/index', 'Transaction\Transfer\BatchController@index')->name('transfer-confirm.index');
    Route::post('/inventory/stock-transfer/confirm/submit', 'Transaction\Transfer\BatchController@submit')->name('transfer-confirm.submit');
    /* End Stock Tranfer */

    /* Stock Replenishment */
    Route::get('/inventory/stock-replenish', 'Transaction\Replenish\JobController@index')->name('replenish-job.index');
    Route::get('/inventory/stock-replenish/create/{id}', 'Transaction\Replenish\JobController@create')->name('replenish-job.create');
    Route::get('/inventory/stock-replenish/job', 'Transaction\Replenish\JobController@edit')->name('replenish-job.edit');
    Route::post('/inventory/stock-replenish/store', 'Transaction\Replenish\JobController@store')->name('replenish-job.store');

    Route::get('/inventory/stock-replenish/location', 'Transaction\Replenish\ProcessController@index')->name('replenish-location.index');

    Route::get('/inventory/stock-replenish/cancel/index', 'Transaction\Replenish\CancelController@index')->name('replenish-cancel.index');
    Route::post('/inventory/stock-replenish/cancel/submit', 'Transaction\Replenish\CancelController@submit')->name('replenish-cancel.submit');

    Route::get('/inventory/stock-replenish/confirm/index', 'Transaction\Replenish\ConfirmController@index')->name('replenish-confirm.index');
    Route::post('/inventory/stock-replenish/confirm/submit', 'Transaction\Replenish\ConfirmController@submit')->name('replenish-confirm.submit');
    /* Stock Replenishment */

    /* Autocomplete Replenish */
    Route::get('/replenish/product-auto', 'Transaction\Replenish\AutoCompleteController@productList')->name('replenish-product.auto');
    Route::get('/replenish/site-auto', 'Transaction\Replenish\AutoCompleteController@siteList')->name('replenish-site.auto');
    Route::get('/replenish/area-auto', 'Transaction\Replenish\AutoCompleteController@areaList')->name('replenish-area.auto');
    Route::get('/replenish/location-auto', 'Transaction\Replenish\AutoCompleteController@locationList')->name('replenish-location.auto');
    /* End Autocomplete Replenish */

    /* Stock Cycle Count */
    Route::get('/inventory/cycle-count', 'Transaction\CycleCount\JobController@index')->name('cycle-job.index');
    Route::get('/inventory/cycle-count/create/{id}', 'Transaction\CycleCount\JobController@create')->name('cycle-job.create');
    Route::get('/inventory/cycle-count/job', 'Transaction\CycleCount\JobController@edit')->name('cycle-job.edit');
    Route::post('/inventory/cycle-count/store', 'Transaction\CycleCount\JobController@store')->name('cycle-job.store');

    Route::get('/inventory/cycle-count/detail', 'Transaction\CycleCount\DetailController@index')->name('cycle-detail.index');
    Route::post('/inventory/cycle-count/detail/store', 'Transaction\CycleCount\DetailController@store')->name('cycle-detail.store');

    Route::get('/inventory/cycle-count/release', 'Transaction\CycleCount\ReleaseController@index')->name('cycle-release.index');
    Route::post('/inventory/cycle-count/release/submit', 'Transaction\CycleCount\ReleaseController@submit')->name('cycle-release.submit');

    Route::get('/inventory/cycle-count/confirm', 'Transaction\CycleCount\ConfirmController@index')->name('cycle-confirm.index');
    Route::post('/inventory/cycle-count/confirm/submit', 'Transaction\CycleCount\ConfirmController@submit')->name('cycle-confirm.submit');
    Route::get('/inventory/cycle-count/review/index', 'Transaction\CycleCount\BatchController@index')->name('cycle-review.index');
    /* End Stock Cycle Count */

    /* Autocomplete Cycle Count */
    Route::get('/cycle/product-group', 'Transaction\CycleCount\AutoCompleteController@productGroupList')->name('cycle-product-group.auto');
    Route::get('/cycle/product-brand', 'Transaction\CycleCount\AutoCompleteController@productBrandList')->name('cycle-product-brand.auto');
    Route::get('/cycle/product', 'Transaction\CycleCount\AutoCompleteController@productStockList')->name('cycle-product.auto');
    Route::get('/cycle/site', 'Transaction\CycleCount\AutoCompleteController@siteStockList')->name('cycle-site.auto');
    Route::get('/cycle/site-area', 'Transaction\CycleCount\AutoCompleteController@siteAreaStockList')->name('cycle-siteArea.auto');
    Route::get('/cycle/location', 'Transaction\CycleCount\AutoCompleteController@locationStockList')->name('cycle-location.auto');
    /* End Autocomplete Cycle Count */

    /* Stock Take */
    Route::get('/inventory/stock-take', 'Transaction\StockTake\JobController@index')->name('take-job.index');
    Route::get('/inventory/stock-take/create/{id}', 'Transaction\StockTake\JobController@create')->name('take-job.create');
    Route::get('/inventory/stock-take/job', 'Transaction\StockTake\JobController@edit')->name('take-job.edit');
    Route::post('/inventory/stock-take/store', 'Transaction\StockTake\JobController@store')->name('take-job.store');

    Route::get('/inventory/stock-take/detail', 'Transaction\StockTake\DetailController@index')->name('take-detail.index');
    Route::post('/inventory/stock-take/detail/store', 'Transaction\StockTake\DetailController@store')->name('take-detail.store');

    Route::get('/inventory/stock-take/release', 'Transaction\StockTake\ReleaseController@index')->name('take-release.index');
    Route::post('/inventory/stock-take/release/submit', 'Transaction\StockTake\ReleaseController@submit')->name('take-release.submit');

    Route::get('/inventory/stock-take/adjust', 'Transaction\StockTake\AdjustmentController@index')->name('take-adjust.index');
    Route::post('/inventory/stock-take/adjust/submit', 'Transaction\StockTake\AdjustmentController@submit')->name('take-adjust.submit');
    /* End Stock Take */

    /* Stock Adjustment */
    Route::get('/inventory/stock-adjustment', 'Transaction\Adjustment\JobController@index')->name('adjustment-job.index');
    Route::get('/inventory/stock-adjustment/create/{id}', 'Transaction\Adjustment\JobController@create')->name('adjustment-job.create');
    Route::get('/inventory/stock-adjustment/job', 'Transaction\Adjustment\JobController@edit')->name('adjustment-job.edit');
    Route::post('/inventory/stock-adjustment/store', 'Transaction\Adjustment\JobController@store')->name('adjustment-job.store');

    Route::get('/inventory/stock-adjustment/detail', 'Transaction\Adjustment\DetailController@index')->name('adjustment-detail.index');
    Route::post('/inventory/stock-adjustment/detail/edit', 'Transaction\Adjustment\DetailController@edit')->name('adjustment-detail.edit');
    Route::delete('/inventory/stock-adjustment/detail/destroy', 'Transaction\Adjustment\DetailController@destroy')->name('adjustment-detail.destroy');
    Route::post('/inventory/stock-adjustment/detail/store', 'Transaction\Adjustment\DetailController@store')->name('adjustment-detail.store');

    Route::get('/inventory/stock-adjustment/process', 'Transaction\Adjustment\ProcessController@index')->name('adjustment-process.index');
    Route::post('/inventory/stock-adjustment/process/submit', 'Transaction\Adjustment\ProcessController@submit')->name('adjustment-process.submit');

    Route::get('/inventory/stock-adjustment/cancel', 'Transaction\Adjustment\CancelController@index')->name('adjustment-cancel.index');
    Route::post('/inventory/stock-adjustment/cancel/submit', 'Transaction\Adjustment\CancelController@submit')->name('adjustment-cancel.submit');

    Route::get('/inventory/stock-adjustment/confirm', 'Transaction\Adjustment\ConfirmController@index')->name('adjustment-confirm.index');
    Route::post('/inventory/stock-adjustment/confirm/submit', 'Transaction\Adjustment\ConfirmController@submit')->name('adjustment-confirm.submit');

    Route::get('/inventory/stock-adjustment/autorization', 'Transaction\Adjustment\AutorizationController@index')->name('adjustment-autorization.index');
    Route::get('/inventory/stock-adjustment/autorization/view/{id}', 'Transaction\Adjustment\AutorizationController@view')->name('adjustment-autorization.view');
    Route::get('/inventory/stock-adjustment/autorization/detail', 'Transaction\Adjustment\AutorizationController@detail')->name('adjustment-autorization.detail');
    Route::post('/inventory/stock-adjustment/autorization/submit', 'Transaction\Adjustment\AutorizationController@submit')->name('adjustment-autorization.submit');
    Route::post('/inventory/stock-adjustment/autorization/upload', 'Transaction\Adjustment\AutorizationController@upload')->name('adjustment-autorization.upload');

    Route::get('/adjustment/stock', 'Transaction\Adjustment\AutoCompleteController@stockList')->name('adjustment.stock');
    Route::post('/adjustment/stock/edit', 'Transaction\Adjustment\AutoCompleteController@stockEdit')->name('adjustment.stock-edit');
    Route::get('/adjustment/product', 'Transaction\Adjustment\AutoCompleteController@productList')->name('adjustment.product');
    Route::post('/adjustment/product/edit', 'Transaction\Adjustment\AutoCompleteController@productEdit')->name('adjustment.product-edit');
    /* End Stock Adjustment */

    /* Inventory Report */
    Route::get('/inventory/stock-transfer/report/{type}/{id}', 'Transaction\Transfer\ReportController@index')->name('transfer-report.report');
    Route::get('/inventory/stock-replenish/report/{type}/{id}', 'Transaction\Replenish\ReportController@index')->name('replenish-report.report');
    Route::get('/inventory/cycle-count/report/{type}/{id}', 'Transaction\CycleCount\ReportController@index')->name('cycle-report.report');
    Route::get('/inventory/stock-take/report/{type}/{id}', 'Transaction\StockTake\ReportController@index')->name('take-report.report');
    Route::get('/inventory/stock-adjustment/report/{type}/{id}', 'Transaction\Adjustment\ReportController@index')->name('adjustment-report.report');

    /* CY Booking */
    Route::get('/cy/booking', 'Transaction\CY\BookingController@index')->name('cy-booking.index');
    Route::get('/cy/booking/create/{id}', 'Transaction\CY\BookingController@create')->name('cy-booking.create');
    Route::post('/cy/booking/store', 'Transaction\CY\BookingController@store')->name('cy-booking.store');
    Route::get('/cy/booking/email/{id}', 'Transaction\CY\BookingController@email');
    /* End CY Booking */

    Route::get('/cy/gate-in', 'Transaction\CY\GateController@index')->name('cy-gate.index');
    Route::get('/cy/gate-in/{id}', 'Transaction\CY\GateController@view');
    Route::post('/cy/gate-in/inbound-in', 'Transaction\CY\GateController@inboundGateIn')->name('cy-gate.inboundGateIn');
    Route::post('/cy/gate-in/inbound-out', 'Transaction\CY\GateController@inboundGateOut')->name('cy-gate.inboundGateOut');

    Route::get('/cy/gate-in/outbound-in/list', 'Transaction\CY\GateController@outboundList')->name('cy-gate.outboundList');
    Route::post('/cy/gate-in/outbound-in', 'Transaction\CY\GateController@outboundGateIn')->name('cy-gate.outboundGateIn');
    Route::post('/cy/gate-in/outbound-out', 'Transaction\CY\GateController@outboundGateOut')->name('cy-gate.outboundGateOut');

    Route::get('/cy/inbound', 'Transaction\CY\InboundController@index')->name('cy-inbound.index');
    Route::get('/cy/inbound/view/{id}', 'Transaction\CY\InboundController@view');
    Route::post('/cy/inbound/store', 'Transaction\CY\InboundController@store')->name('cy-inbound.store');

    Route::get('/cy/outbound', 'Transaction\CY\OutboundController@index')->name('cy-outbound.index');
    Route::get('/cy/outbound/create/{id}', 'Transaction\CY\OutboundController@create')->name('cy-outbound.create');
    Route::post('/cy/outbound/store', 'Transaction\CY\OutboundController@store')->name('cy-outbound.store');
    Route::post('/cy/outbound/submit', 'Transaction\CY\OutboundController@submit')->name('cy-outbound.submit');

    Route::get('/cy/invoice', 'Transaction\CY\Invoice\HeaderController@index')->name('cy-invoice.index');
    Route::get('/cy/invoice/create/{id}', 'Transaction\CY\Invoice\HeaderController@create')->name('cy-invoice.create');
    Route::post('/cy/invoice/store', 'Transaction\CY\Invoice\HeaderController@store')->name('cy-invoice.store');
    Route::get('/cy/invoice/print/{id}', 'Transaction\CY\Invoice\HeaderController@print');
    Route::post('/cy/invoice/submit', 'Transaction\CY\Invoice\HeaderController@submit')->name('cy-invoice.submit');

    Route::get('/cy/invoice/detail', 'Transaction\CY\Invoice\DetailController@index')->name('cy-invoice-detail.index');
    Route::get('/cy/invoice/detail/outbound', 'Transaction\CY\Invoice\DetailController@getOutboundList')->name('cy-invoice-detail.outbound');
    Route::post('/cy/invoice/detail/store', 'Transaction\CY\Invoice\DetailController@store')->name('cy-invoice-detail.store');

    Route::get('/cy/payment', 'Transaction\CY\PaymentController@index')->name('cy-payment.index');
    Route::get('/cy/payment/create/{id}', 'Transaction\CY\PaymentController@create');
    Route::post('/cy/payment/store', 'Transaction\CY\PaymentController@store')->name('cy-payment.store');
    Route::post('/cy/payment/submit', 'Transaction\CY\PaymentController@submit')->name('cy-payment.submit');

    Route::get('/cy/payment/detail', 'Transaction\CY\PaymentDetailController@index')->name('cy-payment-detail.index');
    Route::get('/cy/payment/invoice', 'Transaction\CY\PaymentDetailController@invoice')->name('cy-payment-detail.invoice');
    Route::post('/cy/payment/detail/store', 'Transaction\CY\PaymentDetailController@store')->name('cy-payment-detail.store');
    Route::delete('/cy/payment/detail/destroy', 'Transaction\CY\PaymentDetailController@destroy')->name('cy-payment-detail.destroy');

    Route::get('/cy/report/surat-jalan/{id}', 'Transaction\CY\ReportController@suratJalan');
    Route::get('/cy/report/stock', 'Transaction\CY\ReportController@stockIndex')->name('cy-report.stock-index');
    Route::post('/cy/report/stock/print', 'Transaction\CY\ReportController@stock')->name('cy-report.stock');
    Route::get('/cy/report/stock/export', 'Transaction\CY\ReportController@stockExport');

    Route::get('/cy/report/transaction', 'Transaction\CY\ReportController@transactionIndex')->name('cy-report.transaction-index');
    Route::post('/cy/report/transaction/print', 'Transaction\CY\ReportController@transaction')->name('cy-report.transaction');
    Route::get('/cy/report/transaction/export', 'Transaction\CY\ReportController@transactionExport');

    Route::get('/export/inbound/create/{id}', 'Transaction\Export\Inbound\JobController@create')->name('export-inbound.create');
    // Route::get('/export/inbound/show/{id}', 'Transaction\Export\Inbound\JobController@show')->name('export-inbound.show');
    Route::get('/export/inbound/updateStaple/{job_id}/{username}', 'Transaction\Export\Inbound\JobController@updateStaple');

    Route::get('/export/inbound/', 'Transaction\Export\Inbound\JobController@index')->name('export-inbound.index');
    // Route::get('/export/inbound/create', 'Transaction\Export\Inbound\JobController@create')->name('export-inbound.create');
    // Route::get('/export/inbound/show/{id}', 'Transaction\Export\Inbound\JobController@show')->name('export-inbound.show');
    Route::post('/export/inbound/store', 'Transaction\Export\Inbound\JobController@store')->name('export-inbound.store');
    Route::post('/export/inbound/submit', 'Transaction\Export\Inbound\JobController@submit')->name('export-inbound.submit');
    Route::post('/export/inbound/updateWeight', 'Transaction\Export\Inbound\JobController@updateWeight');

    Route::get('/export/inbound/detail', 'Transaction\Export\Inbound\DetailController@index')->name('export-detail.index');
    Route::post('/export/inbound/detail/store', 'Transaction\Export\Inbound\DetailController@store')->name('export-detail.store');
    Route::get('/export/inbound/pallet-tag/{id}', 'Transaction\Export\Inbound\DetailController@palletTag');

    Route::get('/export/inbound/tally_sheet/{type}/{id}', 'Transaction\Export\Inbound\DetailController@tally_sheet');
    Route::get('/export/inbound/tally_sheet/download/{id}', 'Transaction\Export\Inbound\DetailController@tallySheetExcel');

    Route::get('/export/outbound/', 'Transaction\Export\Outbound\JobController@index')->name('export-outbound.index');
    Route::get('/export/outbound/create/{id}', 'Transaction\Export\Outbound\JobController@create')->name('export-outbound.create');
    Route::post('/export/outbound/store', 'Transaction\Export\Outbound\JobController@store')->name('export-outbound.store');
    Route::post('/export/outbound/submit', 'Transaction\Export\Outbound\JobController@submit')->name('export-outbound.submit');
    Route::get('/export/outbound/report/clp/{id}', 'Transaction\Export\Outbound\ReportController@clp');
    Route::post('/export/outbound/update', 'Transaction\Export\Outbound\JobController@update')->name('export-outbound.update');

    Route::get('/export/report/', 'Transaction\Export\Outbound\ReportController@index')->name('export-report.index');
    Route::post('/export/report/clp/', 'Transaction\Export\Outbound\ReportController@clpDetail')->name('export-report.clp');
    Route::get('/export/report/clp/export', 'Transaction\Export\Outbound\ReportController@export');

    Route::get('/export/outbound/order', 'Transaction\Export\Outbound\OrderController@index')->name('export-order.index');
    Route::get('/export/outbound/order/stock', 'Transaction\Export\Outbound\OrderController@stock')->name('export-order.stock');
    Route::post('/export/outbound/order/store', 'Transaction\Export\Outbound\OrderController@store')->name('export-order.store');
    Route::delete('/export/outbound/order/destroy', 'Transaction\Export\Outbound\OrderController@destroy')->name('export-order.destroy');

    Route::get('/export/outbound/detail', 'Transaction\Export\Outbound\DetailController@index')->name('export-outbound-detail.index');
    Route::post('/export/outbound/detail/store', 'Transaction\Export\Outbound\DetailController@store')->name('export-outbound-detail.store');

    //NEW EXPORT
    Route::get('/export/updateNoPeb', 'Transaction\Export\Inbound\DetailController@updateNoPeb');
    Route::get('/export/getListUpdateNoPeb', 'Transaction\Export\Inbound\DetailController@getListUpdateNoPeb');
    Route::post('/export/updatePeb', 'Transaction\Export\Inbound\DetailController@updatePeb');
    Route::get('/export/outbound/pickingList/{id}', 'Transaction\Export\Outbound\DetailController@pickingList');

    Route::get('/export/inbound/container-size/auto', 'Transaction\AutoCompleteController@getContainerSize')->name('export.getContainerSize');
    Route::get('/export/outbound/container/auto', 'Transaction\AutoCompleteController@getContainerExport')->name('export.getContainerExport');
    Route::get('/export/inbound/forwarder/auto', 'Transaction\AutoCompleteController@getForwarder')->name('export.getForwarder');
    Route::get('/export/inbound/consignee/auto', 'Transaction\AutoCompleteController@getConsignee')->name('export.getConsignee');
    Route::get('/export/inbound/shipper/auto', 'Transaction\AutoCompleteController@getShipper')->name('export.getShipper');
    Route::get('/cy/stock/forwarder/auto', 'Transaction\AutoCompleteController@getForwarderStock')->name('export.getForwarderStock');
    Route::get('/cy/invoice/forwarder/auto', 'Transaction\AutoCompleteController@getForwarderInvoice')->name('export.getForwarderInvoice');
    Route::get('/export/stock/forwarder/auto', 'Transaction\AutoCompleteController@getForwarderStockExport')->name('export.getForwarderStockExport');
    Route::get('/export/outbound/forwarder/auto', 'Transaction\AutoCompleteController@getForwarderOutbound')->name('export.getForwarderOutbound');
    Route::get('/cy/outbound/container/auto', 'Transaction\AutoCompleteController@getContainer')->name('cy.getContainer');
    Route::get('/cy/invoice/auto', 'Transaction\AutoCompleteController@getInvoice')->name('cy.getInvoice');

    Route::get('/fleet-master', 'Master\Fleet\MasterController@index')->name('fleet-master.index');
    Route::get('/branch-master', 'Master\BranchController@index')->name('master-branch.index');
    Route::get('/branch-master/edit/{id}', 'Master\BranchController@edit');
    Route::post('/branch-master/store', 'Master\BranchController@store')->name('master-branch.store');
    Route::get('/fleet-document', 'Master\Fleet\DocumentController@index')->name('fleet-document.index');
    Route::get('/fleet-document/edit/{id}', 'Master\Fleet\DocumentController@edit');
    Route::post('/fleet-document/store', 'Master\Fleet\DocumentController@store')->name('fleet-document.store');
    Route::get('/fleet-group', 'Master\Fleet\InspectionGroupController@index')->name('fleet-group.index');
    Route::get('/fleet-group/edit/{id}', 'Master\Fleet\InspectionGroupController@edit');
    Route::post('/fleet-group/store', 'Master\Fleet\InspectionGroupController@store')->name('fleet-group.store');
    Route::get('/fleet-group/item', 'Master\Fleet\InspectionItemController@index')->name('fleet-item.index');
    Route::get('/fleet-group/item/edit/{id}', 'Master\Fleet\InspectionItemController@edit');
    Route::post('/fleet-group/item/store', 'Master\Fleet\InspectionItemController@store')->name('fleet-item.store');
    Route::get('/fleet-driver', 'Master\Fleet\DriverController@index')->name('fleet-driver.index');
    Route::get('/fleet-driver/edit/{id}', 'Master\Fleet\DriverController@edit');
    Route::post('/fleet-driver/store', 'Master\Fleet\DriverController@store')->name('fleet-driver.store');
    Route::get('/fleet-vehicle-type', 'Master\Fleet\VehicleTypeController@index')->name('fleet-vehicle-type.index');
    Route::get('/fleet-vehicle-type/edit/{id}', 'Master\Fleet\VehicleTypeController@edit');
    Route::post('/fleet-vehicle-type/store', 'Master\Fleet\VehicleTypeController@store')->name('fleet-vehicle-type.store');
    Route::get('/fleet-vehicle', 'Master\Fleet\VehicleController@index')->name('fleet-vehicle.index');
    Route::get('/fleet-vehicle/edit/{id}', 'Master\Fleet\VehicleController@edit');
    Route::post('/fleet-vehicle/store', 'Master\Fleet\VehicleController@store')->name('fleet-vehicle.store');

    Route::get('/export-master', 'Master\Export\ExportController@index')->name('export-master.index');
    Route::get('/export-master/edit/{id}', 'Master\Export\ExportController@editChecker');
    Route::post('/export-master/store', 'Master\Export\ExportController@storeChecker')->name('export-master.store');
    Route::post('/export-master/storeLocation', 'Master\Export\ExportController@storeLocation')->name('export-master.storeLocation');
    Route::get('/export-master/toggleLocation/{id}/{type}', 'Master\Export\ExportController@toggleLocation');
    Route::get('/export-master/editLocation/{id}', 'Master\Export\ExportController@editLocation');
    Route::POST('/export-master/uploadLocation', 'Master\Export\ExportController@uploadLocation')->name('export-master.uploadLocation');
    Route::get('actionChecker/{type}/{id}', 'Master\Export\ExportController@actionChecker');
    Route::get('addChecker/{name}', 'Master\Export\ExportController@addChecker');
    Route::get('/export-forwarder', 'Master\Export\ForwarderController@index')->name('export-forwarder.index');
    Route::get('/export-forwarder/edit/{id}', 'Master\Export\ForwarderController@edit');
    Route::post('/export-forwarder/store', 'Master\Export\ForwarderController@store')->name('export-forwarder.store');

    Route::get('/export-forwarder/service', 'Master\Export\ForwarderServiceController@index')->name('export-forwarder-service.index');
    Route::post('/export-forwarder/service/store', 'Master\Export\ForwarderServiceController@store')->name('export-forwarder-service.store');
    Route::delete('/export-forwarder/service/delete/{forwarder_id}/{service_id}', 'Master\Export\ForwarderServiceController@destroy');

    Route::get('/export-forwarder/container-size', 'Master\Export\ForwarderContainerSizeController@index')->name('export-forwarder-container-size.index');
    Route::post('/export-forwarder/container-size/store', 'Master\Export\ForwarderContainerSizeController@store')->name('export-forwarder-container-size.store');
    Route::delete('/export-forwarder/container-size/delete/{forwarder_id}/{size_id}', 'Master\Export\ForwarderContainerSizeController@destroy');

    Route::get('/export-consignee', 'Master\Export\ConsigneeController@index')->name('export-consignee.index');
    Route::get('/export-consignee/edit/{id}', 'Master\Export\ConsigneeController@edit');
    Route::post('/export-consignee/store', 'Master\Export\ConsigneeController@store')->name('export-consignee.store');
    Route::get('/export-shipper', 'Master\Export\ShipperController@index')->name('export-shipper.index');
    Route::get('/export-shipper/edit/{id}', 'Master\Export\ShipperController@edit');
    Route::post('/export-shipper/store', 'Master\Export\ShipperController@store')->name('export-shipper.store');
    Route::get('/cy-checklist', 'Master\CY\ChecklistController@index')->name('cy-checklist.index');
    Route::get('/cy-checklist/edit/{id}', 'Master\CY\ChecklistController@edit');
    Route::post('/cy-checklist/store', 'Master\CY\ChecklistController@store')->name('cy-checklist.store');
    Route::get('/cy-invoice-type', 'Master\CY\InvoiceTypeController@index')->name('cy-invoice-type.index');
    Route::get('/cy-invoice-type/edit/{id}', 'Master\CY\InvoiceTypeController@edit');
    Route::post('/cy-invoice-type/store', 'Master\CY\InvoiceTypeController@store')->name('cy-invoice-type.store');

    Route::get('/fleet-checklist', 'Transaction\Fleet\CheckList\HeaderController@index')->name('fleet-checklist.index');
    Route::get('/fleet-checklist/add', 'Transaction\Fleet\CheckList\HeaderController@add')->name('fleet-checklist.add');
    Route::get('/fleet-checklist/edit/{id}', 'Transaction\Fleet\CheckList\HeaderController@edit');
    Route::post('/fleet-checklist/create', 'Transaction\Fleet\CheckList\HeaderController@create')->name('fleet-checklist.create');
    Route::post('/fleet-checklist/store', 'Transaction\Fleet\CheckList\HeaderController@store')->name('fleet-checklist.store');
    Route::get('/fleet-checklist/report/{id}', 'Transaction\Fleet\CheckList\HeaderController@report');

    /* AutoComplete */
    Route::get('/auto/master/product', 'Master\AutoCompleteController@getProduct')->name('product.getProduct');
    Route::get('/auto/site/area', 'Master\AutoCompleteController@getAreaAuto')->name('site.getAreaAuto');
    Route::get('/list/site/area', 'Master\AutoCompleteController@getAreaList')->name('site.getAreaList');
    Route::get('/auto/site/location', 'Master\AutoCompleteController@getLocationAuto')->name('site.getLocationAuto');
    Route::get('/auto/site/location/principal', 'Master\AutoCompleteController@getLocationPrincipalAuto')->name('site.getLocationPrincipalAuto');
    Route::get('/auto/site/location-mixed', 'Master\AutoCompleteController@getLocationMixedAuto')->name('site.getLocationMixedAuto');
    Route::get('/list/site/location', 'Master\AutoCompleteController@getLocationList')->name('site.getLocationList');
    Route::get('/auto/stock/location', 'Transaction\AutoCompleteController@getStockLocation')->name('stock.getStockLocation');
    Route::get('/auto/site/location-all', 'Master\AutoCompleteController@getLocationAll')->name('site.getLocationAll');
    Route::get('/list/principal/branch', 'Master\AutoCompleteController@getBranchPrincipalList')->name('principal.getBranchList');

    Route::get('/auto/outbound-order', 'Transaction\AutoCompleteController@getOutboundOrder')->name('outbound.getOrder');
    Route::get('/auto/stock-product', 'Transaction\AutoCompleteController@getStockProduct')->name('stock.getStockProduct');
    Route::get('/auto/stock-batch', 'Transaction\AutoCompleteController@getStockBatch')->name('stock.getStockBatch');
    Route::get('/auto/stock-site', 'Transaction\AutoCompleteController@getStockSite')->name('stock.getStockSite');
    Route::get('/auto/outbound-order/cross-dock', 'Transaction\AutoCompleteController@getOutboundOrderCrossDock')->name('outbound.getOrderCrossDock');
    Route::get('/auto/outbound-order/issue', 'Transaction\AutoCompleteController@getOutboundOrderIssue')->name('outbound.getOutboundOrderIssue');

    Route::get('/issue-reason', 'Transaction\Issue\JobController@index')->name('issue-reason.index');
    Route::get('/issue-reason/create/{id}', 'Transaction\Issue\JobController@create')->name('issue-reason.create');
    Route::post('/issue-reason/store', 'Transaction\Issue\JobController@store')->name('issue-reason.store');

    /* Import & Export */
    Route::get('/customer-master/customer/export/{id}', 'Master\CustomerController@export');
    Route::post('/customer-master/customer/import', 'Master\CustomerController@import');

    /* Reporting */
    Route::get('/report/pallet-tag', 'Report\PalletController@index');
    Route::post('/report/pallet-tag/print', 'Report\PalletController@print')->name("pallet-tag.print");
    Route::get('/report/pallet-tag/getLocation/{site}', 'Report\PalletController@getLocation');

    Route::get('/report/dispatch', 'Report\DespatchController@index');
    Route::get('/report/dispatch/list', 'Report\DespatchController@getList')->name("dispatch.list");
    Route::post('/report/dispatch/print', 'Report\DespatchController@print')->name("dispatch.print");
    Route::get('/report/dispatch/export', 'Report\DespatchController@export');

    Route::get('/report/shad/inbound', 'Report\Shad\InboundController@index');
    Route::post('/report/shad/inbound/print', 'Report\Shad\InboundController@print')->name("shad-inbound.print");
    Route::get('/report/shad/inbound/export', 'Report\Shad\InboundController@export');

    Route::get('/customer-master/store/export/{id}', 'Master\StoreController@export');
    Route::post('/customer-master/store/import', 'Master\StoreController@import');

    Route::get('/site-master/location/export/{id}', 'Master\LocationController@export');
    Route::post('/site-master/location/import', 'Master\LocationController@import');

    /* Dropdown List  */
    Route::get('/list/customer', 'Master\AutoCompleteController@getCustomer')->name('customer.getCustomer');
    Route::get('/auto/customer', 'Master\AutoCompleteController@getCustomerAuto')->name('customer.getCustomerAuto');

    Route::get('/list/transaction/inbound/vehicle', 'Transaction\AutoCompleteController@getInboundVehicle')->name('inbound.getInboundVehicle');

    /* Stock Report Filter  */
    Route::get('/list/stock-report/site-area', 'Transaction\Stock\AutoCompleteController@getArea')->name('stock-report.getArea');
    Route::get('/auto/stock-report/location', 'Transaction\Stock\AutoCompleteController@getLocation')->name('stock-report.getLocation');
    Route::get('/list/stock-report/product-group', 'Transaction\Stock\AutoCompleteController@getProductGroup')->name('stock-report.getProductGroup');
    Route::get('/list/stock-report/product-brand', 'Transaction\Stock\AutoCompleteController@getProductBrand')->name('stock-report.getProductBrand');
    Route::get('/auto/stock-report/product', 'Transaction\Stock\AutoCompleteController@getProduct')->name('stock-report.getProduct');

    /* Reference Autocomplete or List */
    Route::get('/region', 'ReferenceController@region')->name('region.list');
    Route::get('/city', 'ReferenceController@city')->name('city.list');

    Route::get('logout', 'LoginController@logout')->name('logout');
    //ubah pw
    Route::POST('user/change-password', 'UserController@ubahPassword')->name('post-change-password');
    Route::get('user/changePassword', 'UserController@changePassword');

    Route::group(['prefix' => 'warehouse/scan-pallet-tag'], function () {
        Route::get('/', 'Transaction\Scan\ScanPalletTagController@index');
        Route::get('/doScan/{qr}', 'Transaction\Scan\ScanPalletTagController@doScan');
    });

    Route::group(['prefix' => 'warehouse/scan-qr-location'], function () {
        Route::get('/', 'Transaction\Scan\ScanPalletTagController@indexLocation');
        Route::get('/getBlokLocation', 'Transaction\Scan\ScanPalletTagController@getBlokLocation');
        Route::get('/getSkuOnBlok/{blok}', 'Transaction\Scan\ScanPalletTagController@getSkuOnBlok');
        Route::get('/doScanLocation/{params}', 'Transaction\Scan\ScanPalletTagController@doScanLocation');
    });

    Route::group(['prefix' => 'warehouse/'], function () {
        Route::get('locationAvailable', 'Transaction\Stock\LedgerController@locationAvailable');
        Route::get('getLocationAvailable', 'Transaction\Stock\LedgerController@getLocationAvailable')->name('getLocationAvailable');
    });


    Route::group(['prefix' => 'inventory/updateBatch/'], function () {
        Route::get('/', 'Transaction\UpdateBatch\JobController@index');
        Route::get('getList', 'Transaction\UpdateBatch\JobController@getList');
        Route::get('getData/{id}', 'Transaction\UpdateBatch\JobController@getData');
        Route::POST('submit', 'Transaction\UpdateBatch\JobController@submit');
    });

    Route::group(['prefix' => 'inventory/updateStatusProduct/'], function () {
        Route::get('/', 'Transaction\UpdateStatusProduct\JobController@index');
        Route::get('getList', 'Transaction\UpdateStatusProduct\JobController@getList');
        Route::get('showData/{id}', 'Transaction\UpdateStatusProduct\JobController@showData');
        Route::get('editData/{id}', 'Transaction\UpdateStatusProduct\JobController@editData');
        Route::POST('submit', 'Transaction\UpdateStatusProduct\JobController@submit')->name('submitUpdateStatusProduct');
    });

    /* Stock Tranfer */


    Route::get('/retry-api-epm', 'Transaction\Api\EpmRetryApiController@index')->name('retry-api-epm.index');
    Route::get('/retry-api-epm/{id}/edit', 'Transaction\Api\EpmRetryApiController@edit');
    Route::post('/retry-api-epm/resend', 'Transaction\Api\EpmRetryApiController@resend')->name('retry-api-epm.resend');
    Route::delete('/retry-api-epm/{id}', 'Transaction\Api\EpmRetryApiController@destroy')->name('retry-api-epm.destroy');

    // Route::get('/settings/email', 'Settings\EmailController@index')->name('settings-email.index');
    // Route::get('/settings/email/{id}/edit', 'Settings\EmailController@edit');
    // Route::post('/settings/email/store', 'Settings\EmailController@store')->name('settings-email.store');
    // Route::delete('/settings/email/{id}', 'Settings\EmailController@destroy')->name('settings-email.destroy');

    /* KPI Report - Distribution Center */
    Route::get('/kpi/distribution-center', 'Report\KPI\DistributionCenterController@index')->name('distribution-center.index');
    Route::post('/kpi/distribution-center', 'Report\KPI\DistributionCenterController@report')->name('distribution-center.report');
    Route::get('/kpi/distribution-center/export', 'Report\KPI\DistributionCenterController@export');

    // NEW ROUT PICKING PALLET REPORT BY ARI RIZKITA
    Route::get('/warehouse/report/picking', 'Report\PickingPalleteController@index')->name('picking-report.index');
    Route::get('/warehouse/picking', 'Report\PickingPalleteController@report')->name('picking-report.report');
    Route::get('/warehouse/picking-report/export', 'Report\PickingPalleteController@export');
    // BATAS

    require base_path('routes/cycle-count.php');
    require base_path('routes/stock-opname.php');
    require base_path('routes/vm-price.php');
    require base_path('routes/cross-dock.php');
    require base_path('routes/generatePalletID.php');
    require base_path('routes/collectingPrice.php');
    require base_path('routes/reportPenagihan.php');
    require base_path('routes/authenticateNew.php');
    require base_path('routes/ScanCargoEkspor.php');
    require base_path('routes/CheckpointDriver.php');
    require base_path('routes/ExportBeaCukai.php');
    require base_path('routes/DashboardOps.php');
    require base_path('routes/cy-new.php');
});
