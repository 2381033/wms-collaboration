<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sm_menu')->insert([
            [
                'id'=>2,
                'name'=>'Reference Data',
                'parent_id'=>0,
                'url'=>null,
                'icon'=>'fas fa-globe'
            ],
            [
                'id'=>201,
                'name'=>'Currency',
                'parent_id'=>2,
                'url'=>'reference/currency',
                'icon'=>'fas fa-euro-sign'
            ],
            [
                'id'=>202,
                'name'=>'Country',
                'parent_id'=>2,
                'url'=>'reference/country',
                'icon'=>'fas fa-flag'
            ],
            [
                'id'=>203,
                'name'=>'Region',
                'parent_id'=>2,
                'url'=>'reference/region',
                'icon'=>'fas fa-landmark'
            ],
            [
                'id'=>204,
                'name'=>'City',
                'parent_id'=>2,
                'url'=>'reference/city',
                'icon'=>'fas fa-city'
            ],
            [
                'id'=>205,
                'name'=>'Unit Of Measure',
                'parent_id'=>2,
                'url'=>'reference/uom',
                'icon'=>'fas fa-box-open'
            ],
            [
                'id'=>3,
                'name'=>'Master',
                'parent_id'=>0,
                'url'=>'',
                'icon'=>'fas fa-tools'
            ],
            [
                'id'=>301,
                'name'=>'Global',
                'parent_id'=>3,
                'url'=>'',
                'icon'=>'fas fa-building'
            ],
            [
                'id'=>30101,
                'name'=>'Company',
                'parent_id'=>301,
                'url'=>'master/company',
                'icon'=>'fas fa-building'
            ],
            [
                'id'=>30102,
                'name'=>'Job Class',
                'parent_id'=>301,
                'url'=>'master/job-class',
                'icon'=>'fas fa-file-alt'
            ],
            [
                'id'=>30103,
                'name'=>'Container Type',
                'parent_id'=>301,
                'url'=>'master/container-type',
                'icon'=>'fas fa-car'
            ],
            [
                'id'=>30104,
                'name'=>'Container Size',
                'parent_id'=>301,
                'url'=>'master/container-size',
                'icon'=>'fas fa-truck-monster'
            ],
            [
                'id'=>30105,
                'name'=>'Mode Of Transport',
                'parent_id'=>301,
                'url'=>'master/mode',
                'icon'=>'fas fa-plane'
            ],
            [
                'id'=>302,
                'name'=>'Site & Location',
                'parent_id'=>3,
                'url'=>'',
                'icon'=>'fas fa-building'
            ],
            [
                'id'=>30201,
                'name'=>'Site Type',
                'parent_id'=>302,
                'url'=>'site-master/site-type',
                'icon'=>'fas fa-map-signs'
            ],
            [
                'id'=>30202,
                'name'=>'Site Indicator',
                'parent_id'=>302,
                'url'=>'site-master/site-indicator',
                'icon'=>'fas fa-building'
            ],
            [
                'id'=>30203,
                'name'=>'Location Type',
                'parent_id'=>302,
                'url'=>'site-master/location-type',
                'icon'=>'fas fa-pallet'
            ],
            [
                'id'=>30204,
                'name'=>'Location Status',
                'parent_id'=>302,
                'url'=>'site-master/location-status',
                'icon'=>'fas fa-signal'
            ],
            [
                'id'=>30205,
                'name'=>'Site',
                'parent_id'=>302,
                'url'=>'site-master/site',
                'icon'=>'fas fa-warehouse'
            ],
            [
                'id'=>30206,
                'name'=>'Area',
                'parent_id'=>302,
                'url'=>'site-master/area',
                'icon'=>'fas fa-road'
            ],
            [
                'id'=>30207,
                'name'=>'Location',
                'parent_id'=>302,
                'url'=>'site-master/location',
                'icon'=>'fas fa-server'
            ],
            [
                'id'=>303,
                'name'=>'Product Master',
                'parent_id'=>3,
                'url'=>'',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>30301,
                'name'=>'Principal',
                'parent_id'=>303,
                'url'=>'product-master/principal',
                'icon'=>'fas fa-industry'
            ],
            [
                'id'=>30302,
                'name'=>'Manufactur',
                'parent_id'=>303,
                'url'=>'product-master/manufactur',
                'icon'=>'fas fa-industry'
            ],
            [
                'id'=>30303,
                'name'=>'Product Category',
                'parent_id'=>303,
                'url'=>'product-master/product-category',
                'icon'=>'fas fa-shopping-bag'
            ],
            [
                'id'=>30304,
                'name'=>'Product Group',
                'parent_id'=>303,
                'url'=>'product-master/product-group',
                'icon'=>'fas fa-layer-group'
            ],
            [
                'id'=>30305,
                'name'=>'Product Brand',
                'parent_id'=>303,
                'url'=>'product-master/product-brand',
                'icon'=>'fas fa-object-group'
            ],
            [
                'id'=>30306,
                'name'=>'Product',
                'parent_id'=>303,
                'url'=>'product-master/product',
                'icon'=>'fas fa-tshirt'
            ],
            [
                'id'=>304,
                'name'=>'Customer Master',
                'parent_id'=>3,
                'url'=>'',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>30401,
                'name'=>'Store',
                'parent_id'=>304,
                'url'=>'customer-master/store',
                'icon'=>'fas fa-user-friends'
            ],
            [
                'id'=>30402,
                'name'=>'Customer Group',
                'parent_id'=>304,
                'url'=>'customer-master/customer-group',
                'icon'=>'fas fa-user-friends'
            ],
            [
                'id'=>30403,
                'name'=>'Customer Type',
                'parent_id'=>304,
                'url'=>'customer-master/customer-type',
                'icon'=>'fas fa-restroom'
            ],
            [
                'id'=>30404,
                'name'=>'Customer',
                'parent_id'=>304,
                'url'=>'customer-master/customer',
                'icon'=>'fas fa-address-book'
            ],
            [
                'id'=>4,
                'name'=>'Transaction',
                'parent_id'=>0,
                'url'=>'',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>401,
                'name'=>'Warehouse Transaction',
                'parent_id'=>4,
                'url'=>'',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>40101,
                'name'=>'Inbound',
                'parent_id'=>401,
                'url'=>'warehouse/inbound',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>40102,
                'name'=>'Outbound',
                'parent_id'=>401,
                'url'=>'warehouse/outbound',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>402,
                'name'=>'Inventory Transaction',
                'parent_id'=>4,
                'url'=>'',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>40201,
                'name'=>'Stock Transfer',
                'parent_id'=>402,
                'url'=>'inventory/stock-transfer',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>40202,
                'name'=>'Stock Replenishment',
                'parent_id'=>402,
                'url'=>'inventory/stock-replenish',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>40203,
                'name'=>'Cycle Count',
                'parent_id'=>402,
                'url'=>'inventory/cycle-count',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>40204,
                'name'=>'Stock Take',
                'parent_id'=>402,
                'url'=>'inventory/stock-take',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>40205,
                'name'=>'Stock Adjustment',
                'parent_id'=>402,
                'url'=>'inventory/stock-adjustment',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>5,
                'name'=>'Report',
                'parent_id'=>0,
                'url'=>'',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>501,
                'name'=>'Warehouse Report',
                'parent_id'=>5,
                'url'=>'',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>50101,
                'name'=>'Stock Report',
                'parent_id'=>501,
                'url'=>'warehouse/stock-report',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>50102,
                'name'=>'Transaction Report',
                'parent_id'=>501,
                'url'=>'warehouse/transaction-report',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>502,
                'name'=>'Inventory Report',
                'parent_id'=>5,
                'url'=>'',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>9,
                'name'=>'Administrator',
                'parent_id'=>0,
                'url'=>'',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>901,
                'name'=>'Role',
                'parent_id'=>9,
                'url'=>'admin/role',
                'icon'=>'fas fa-home'
            ],
            [
                'id'=>902,
                'name'=>'Menu',
                'parent_id'=>9,
                'url'=>'admin/menu',
                'icon'=>'fas fa-users'
            ],
            [
                'id'=>903,
                'name'=>'User',
                'parent_id'=>9,
                'url'=>'admin/user',
                'icon'=>'fas fa-users'
            ],
            // [
            //     'id'=>6,
            //     'name'=>'Warehouse Transaction',
            //     'level_menu'=>'main_menu',
            //     'parent_id'=>0,
            //     'url'=>'warehouse',
            //     'icon'=>'fas fa-warehouse',
            //     'nomor_urut'=>6
            // ],
            // [
            //     'id'=>601,
            //     'name'=>'Gate In',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>6,
            //     'url'=>'warehouse/gate-in',
            //     'icon'=>'fas fa-truck',
            //     'nomor_urut'=>6
            // ],
            // [
            //     'id'=>602,
            //     'name'=>'Inbound',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>6,
            //     'url'=>'warehouse/inbound',
            //     'icon'=>'fas fa-pallet',
            //     'nomor_urut'=>6
            // ],
            // [
            //     'id'=>603,
            //     'name'=>'Outbound',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>6,
            //     'url'=>'warehouse/outbound',
            //     'icon'=>'fas fa-dolly-flatbed',
            //     'nomor_urut'=>6
            // ],
            // [
            //     'id'=>7,
            //     'name'=>'Inventory Transaction',
            //     'level_menu'=>'main_menu',
            //     'parent_id'=>0,
            //     'url'=>'inventory',
            //     'icon'=>'fas fa-warehouse',
            //     'nomor_urut'=>7
            // ],
            // [
            //     'id'=>701,
            //     'name'=>'Stock Transfer',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>7,
            //     'url'=>'inventory/transfer',
            //     'icon'=>'fas fa-truck',
            //     'nomor_urut'=>7
            // ],
            // [
            //     'id'=>702,
            //     'name'=>'Adjustment',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>7,
            //     'url'=>'inventory/adjustment',
            //     'icon'=>'fas fa-pallet',
            //     'nomor_urut'=>7
            // ],
            // [
            //     'id'=>703,
            //     'name'=>'Replenishment',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>7,
            //     'url'=>'inventory/replenishment',
            //     'icon'=>'fas fa-dolly-flatbed',
            //     'nomor_urut'=>7
            // ],
            // [
            //     'id'=>704,
            //     'name'=>'Stock Opname',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>7,
            //     'url'=>'inventory/stock-opname',
            //     'icon'=>'fas fa-dolly-flatbed',
            //     'nomor_urut'=>7
            // ],
            // [
            //     'id'=>705,
            //     'name'=>'Cycle Count',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>7,
            //     'url'=>'inventory/cycle-count',
            //     'icon'=>'fas fa-dolly-flatbed',
            //     'nomor_urut'=>7
            // ],
            // [
            //     'id'=>8,
            //     'name'=>'Warehouse Report',
            //     'level_menu'=>'main_menu',
            //     'parent_id'=>0,
            //     'url'=>'ware-report',
            //     'icon'=>'fas fa-user-shield',
            //     'nomor_urut'=>8
            // ],
            // [
            //     'id'=>801,
            //     'name'=>'Stock Report',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>8,
            //     'url'=>'ware-report/stock',
            //     'icon'=>'fas fa-boxes',
            //     'nomor_urut'=>8
            // ],
            // [
            //     'id'=>802,
            //     'name'=>'Transaction Report',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>8,
            //     'url'=>'ware-report/transaction',
            //     'icon'=>'fas fa-boxes',
            //     'nomor_urut'=>8
            // ],
            // [
            //     'id'=>20,
            //     'name'=>'Administrator',
            //     'level_menu'=>'main_menu',
            //     'parent_id'=>0,
            //     'url'=>'admin',
            //     'icon'=>'fas fa-user-shield',
            //     'nomor_urut'=>996
            // ],
            // [
            //     'id'=>2001,
            //     'name'=>'Menu',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>20,
            //     'url'=>'admin/menu',
            //     'icon'=>'fas fa-bars',
            //     'nomor_urut'=>997
            // ],
            // [
            //     'id'=>2002,
            //     'name'=>'User',
            //     'level_menu'=>'sub_menu',
            //     'parent_id'=>20,
            //     'url'=>'admin/user',
            //     'icon'=>'fas fa-users',
            //     'nomor_urut'=>998
            // ],
        ]);
    }
}
