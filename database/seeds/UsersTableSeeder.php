<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'company_id'=>1,
                'role_id'=>3,
                'name'=>'Firman Setiawan',
                'username'=>'firman',
                'email'=>'firman.setiawan@samudera.id',
                'password'=>bcrypt('rahasia!'),
                'remember_token'=>Str::random(40)
            ],
            [
                'company_id'=>1,
                'role_id'=>2,
                'name'=>'Firman Setiawan',
                'username'=>'setiawan',
                'email'=>'fsetiawan8@yahoo.com',
                'password'=>bcrypt('rahasia!'),
                'remember_token'=>Str::random(40)
            ],
            [
                'company_id'=>1,
                'role_id'=>2,
                'name'=>'Slamet Riyanto',
                'username'=>'slamet',
                'email'=>'slamet.riyanto@samudera.id',
                'password'=>bcrypt('840093'),
                'remember_token'=>Str::random(40)
            ],
            [
                'company_id'=>1,
                'role_id'=>2,
                'name'=>'Muhammad Irsan',
                'username'=>'irsan',
                'email'=>'muhammad.irsan@samudera.id',
                'password'=>bcrypt('923365'),
                'remember_token'=>Str::random(40)
            ],
            [
                'company_id'=>1,
                'role_id'=>2,
                'name'=>'Reni Purbaningtyas',
                'username'=>'reni',
                'email'=>'reni.purbaningtyas@samudera.id',
                'password'=>bcrypt('401018'),
                'remember_token'=>Str::random(40)
            ],
            [
                'company_id'=>1,
                'role_id'=>2,
                'name'=>'Sri Handayani',
                'username'=>'sri',
                'email'=>'sri.handayani@samudera.id',
                'password'=>bcrypt('143317'),
                'remember_token'=>Str::random(40)
            ],
        ]);
    }
}
