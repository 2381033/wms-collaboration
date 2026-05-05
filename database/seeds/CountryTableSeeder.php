<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rt_country')->insert([
            [
                'country_code'=>'ID',
                'country_name'=>'Indonesia',
                'active'=>'Yes'
            ],
        ]);
    }
}