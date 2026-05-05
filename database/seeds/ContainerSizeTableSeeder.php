<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContainerSizeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_container_size')->insert([
            [
                'company_id'=>1,
                'size_name'=>'20 Foot',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'size_name'=>'40 Foot',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'size_name'=>'Builtup (45 CBM)',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'size_name'=>'Fuso (22 CBM)',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'size_name'=>'Highcube',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'size_name'=>'Large (20 CBM)',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'size_name'=>'Medium (12 CBM)',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'size_name'=>'Small (6 CBM)',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'size_name'=>'Tiny (2 CBM)',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'size_name'=>'Tronton (30 CBM)',
                'active'=>'Yes'
            ],
        ]);
    }
}