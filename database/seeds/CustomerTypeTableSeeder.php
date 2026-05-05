<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_customer_type')->insert([
            [
                'company_id'=>1,
                'principal_id'=>2,
                'type_name'=>'Local',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'principal_id'=>2,
                'type_name'=>'Internal',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'principal_id'=>2,
                'type_name'=>'Foreign',
                'active'=>'Yes'
            ],
        ]);
    }
}