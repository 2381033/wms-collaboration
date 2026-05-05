<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_mode')->insert([
            [
                'company_id'=>1,
                'mode_name'=>'Land',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'mode_name'=>'Sea',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'mode_name'=>'Air',
                'active'=>'Yes'
            ],
        ]);
    }
}