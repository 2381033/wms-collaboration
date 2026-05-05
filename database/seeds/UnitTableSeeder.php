<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rt_uom')->insert([
            [
                'code'=>'PCS',
                'uom_name'=>'Pieces',
                'active'=>'Yes'
            ],
            [
                'code'=>'CTN',
                'uom_name'=>'Carton',
                'active'=>'Yes'
            ],
            [
                'code'=>'BOX',
                'uom_name'=>'Box',
                'active'=>'Yes'
            ],
            [
                'code'=>'STR',
                'uom_name'=>'Strip',
                'active'=>'Yes'
            ],
            [
                'code'=>'BTL',
                'uom_name'=>'Bottle',
                'active'=>'Yes'
            ],
            [
                'code'=>'KG',
                'uom_name'=>'Kilogram',
                'active'=>'Yes'
            ],
            [
                'code'=>'LTR',
                'uom_name'=>'Litre',
                'active'=>'Yes'
            ],
        ]);
    }
}