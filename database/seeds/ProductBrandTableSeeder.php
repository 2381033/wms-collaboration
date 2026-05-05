<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductBrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_product_brand')->insert([
            [
                'company_id'=>1,
                'principal_id'=>1,
                'group_id'=>1,
                'brand_code'=>'RB',
                'brand_name'=>'PT Robert Bosch',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'principal_id'=>2,
                'group_id'=>2,
                'brand_code'=>'HEMPEL',
                'brand_name'=>'PT Hempel Indonesia',
                'active'=>'Yes'
            ],
        ]);
    }
}