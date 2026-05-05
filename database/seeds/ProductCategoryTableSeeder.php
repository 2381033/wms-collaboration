<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_product_category')->insert([
            [
                'company_id'=>1,
                'principal_id'=>1,
                'category_name'=>'PT Robert Bosch',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'principal_id'=>2,
                'category_name'=>'Finish Goods',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'principal_id'=>2,
                'category_name'=>'Raw Material',
                'active'=>'Yes'
            ],
        ]);
    }
}