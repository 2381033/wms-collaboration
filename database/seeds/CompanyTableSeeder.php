<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mt_company')->insert([
            [
                'company_code'=>'MKT',
                'company_name'=>'PT Masaji Kargosentra Tama',            
            ]
        ]);
    }
}
