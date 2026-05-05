<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_site_type')->insert([
            [
                'company_id'=>1,
                'type_name'=>'Racking',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Bulk',
                'active'=>'Yes'
            ],
        ]);
    }
}