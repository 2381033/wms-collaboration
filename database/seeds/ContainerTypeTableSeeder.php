<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContainerTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('iv_container_type')->insert([
            [
                'company_id'=>1,
                'type_name'=>'Standard',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Highcube',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Refer',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'High Cube Refer',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Open Top',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Flat Rack',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Main Deck Pallet',
                'active'=>'Yes'
            ],
            [
                'company_id'=>1,
                'type_name'=>'Lower Deck Pallet',
                'active'=>'Yes'
            ],
        ]);
    }
}
