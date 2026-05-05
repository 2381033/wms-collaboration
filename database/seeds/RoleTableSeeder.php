<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sm_role')->insert([
            [
                'role_name'=>'Vendor',
                'active'=>'Yes'
            ],
            [
                'role_name'=>'User',
                'active'=>'Yes'
            ],
            [
                'role_name'=>'Admin',
                'active'=>'Yes'
            ]
        ]);
    }
}