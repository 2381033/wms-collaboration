<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteIndicatorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_site_indicator')->insert([
            [
                'company_id'=>1,
                'type_id'=>1,
                'indicator_name'=>'Dry (Ambient)',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_id'=>1,
                'indicator_name'=>'Cool Room (+18)',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_id'=>1,
                'indicator_name'=>'Deep Frozen (-29)',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_id'=>2,
                'indicator_name'=>'Cross Docking',
                'active'=>'Yes'
            ],
        ]);
    }
}