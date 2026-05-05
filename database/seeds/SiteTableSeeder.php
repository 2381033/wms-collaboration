<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_site')->insert([
            [
                'company_id'=>1,
                'site_name'=>'Marunda Center',
                'type_id'=>1,
                'indicator_id'=>1,
                'location_id'=>1,
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'site_name'=>'Surabaya',
                'type_id'=>1,
                'indicator_id'=>1,
                'location_id'=>1,
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'site_name'=>'Belawan',
                'type_id'=>1,
                'indicator_id'=>1,
                'location_id'=>1,
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'site_name'=>'Kimia Farma',
                'type_id'=>1,
                'indicator_id'=>1,
                'location_id'=>1,
                'active'=>'Yes'
            ],
        ]);
    }
}