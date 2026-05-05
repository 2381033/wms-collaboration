<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrincipalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_principal')->insert([
            [
                'company_id'=>1,
                'principal_name'=>'PT Robert Bosch',
                'short_name'=>'RB',
                'interface_mode'=>'FMCG',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'principal_name'=>'PT Hempel Indonesia',
                'short_name'=>'Hempel',
                'interface_mode'=>'FMCG',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'principal_name'=>'PT Shad Indonesia',
                'short_name'=>'SHAD',
                'interface_mode'=>'FMCG',
                'active'=>'Yes'
            ],
        ]);
    }
}