<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdjustmentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_adjustment_type')->insert([
            [
                'company_id'=>1,
                'type_name'=>'Broken',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Cycle Count Adjustment',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Damaged',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Data Entry Error',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Duplicate Entry',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Empty Cartons',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Error In Issues',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Error In Receipts',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Found Excess In Pallet',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Found Short In Pallet',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Stock Return',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'System Error',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Wrong Adjustment',
                'active'=>'Yes'
            ],
        ]);
    }
}