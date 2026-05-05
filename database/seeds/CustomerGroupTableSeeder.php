<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_customer_group')->insert([
            [
                'company_id'=>1,
                'principal_id'=>2,
                'group_name'=>'PT Hempel Indonesia',
                'active'=>'Yes'
            ],
        ]);
    }
}