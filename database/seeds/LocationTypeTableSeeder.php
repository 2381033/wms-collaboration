<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_location_type')->insert([
            [
                'description'=>'1.2 x 100',
                'cbm'=>1.2,
                'weight'=>100,
                'active'=>'Yes'
            ],
            [
                'description'=>'1.2 x  120',
                'cbm'=>1.2,
                'weight'=>120,
                'active'=>'Yes'
            ],
            [
                'description'=>'1.5 x 150',
                'cbm'=>1.5,
                'weight'=>150,
                'active'=>'Yes'
            ],
        ]);
    }
}