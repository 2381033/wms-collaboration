<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_location_status')->insert([
            [
                'status_code'=>'E',
                'status_name'=>'Empty',
                'active'=>'Yes'
            ],
            [
                'status_code'=>'R',
                'status_name'=>'Reserved',
                'active'=>'Yes'
            ],
            [
                'status_code'=>'F',
                'status_name'=>'Full',
                'active'=>'Yes'
            ],
            [
                'status_code'=>'P',
                'status_name'=>'Pickslot',
                'active'=>'Yes'
            ],
            [
                'status_code'=>'M',
                'status_name'=>'Mixed',
                'active'=>'Yes'
            ],
            [
                'status_code'=>'B',
                'status_name'=>'Bad',
                'active'=>'Yes'
            ],
        ]);
    }
}