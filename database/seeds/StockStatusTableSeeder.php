<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_stock_status')->insert([
            [
                'company_id'=>1,
                'principal_id'=>1,
                'status_name'=>'Reguler',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'principal_id'=>2,
                'status_name'=>'Reguler',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'principal_id'=>3,
                'status_name'=>'Titipan',
                'active'=>'Yes'
            ],
        ]);
    }
}