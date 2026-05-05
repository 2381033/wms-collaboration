<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_product_group')->insert([
            [
                'company_id'=>1,
                'principal_id'=>1,
                'group_code'=>'RB',
                'group_name'=>'PT Robert Bosch',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'principal_id'=>2,
                'group_code'=>'HEMPEL',
                'group_name'=>'PT Hempel Indonesia',
                'active'=>'Yes'
            ],
        ]);
    }
}