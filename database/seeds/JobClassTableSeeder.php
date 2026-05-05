<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobClassTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_job_class')->insert([
            [
                'company_id'=>1,
                'class_name'=>'Reguler',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'class_name'=>'Internal',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'class_name'=>'Cross Dock',
                'active'=>'Yes'
            ],
        ]);
    }
}