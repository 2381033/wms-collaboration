<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteAreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_site_area')->insert([
            [
                'company_id'=>1,
                'site_id'=>1,
                'area_name'=>'Gudang A',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'site_id'=>1,
                'area_name'=>'Gudang B',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'site_id'=>2,
                'area_name'=>'Hempel',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'site_id'=>3,
                'area_name'=>'Selving',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'site_id'=>3,
                'area_name'=>'Racking',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'site_id'=>4,
                'area_name'=>'NDC 1',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'site_id'=>4,
                'area_name'=>'NDC 2',
                'active'=>'Yes'
            ],
        ]);
    }
}