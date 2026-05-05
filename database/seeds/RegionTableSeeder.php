<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rt_region')->insert([
            [
                'country_code'=>'ID',
                'region_code'=>'ACEH',
                'region_name'=>'Aceh',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'SUMUT',
                'region_name'=>'Sumatera Utara',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'SUMBAR',
                'region_name'=>'Sumatera Barat',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'SUMSEL',
                'region_name'=>'Sumatera Selatan',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'RIAU',
                'region_name'=>'Riau',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'LAMPUNG',
                'region_name'=>'Lampung',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'KEPRI',
                'region_name'=>'Kepulauan Riau',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'JAMBI',
                'region_name'=>'Jambi',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'BENGKULU',
                'region_name'=>'Bengkulu',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'BANGKA',
                'region_name'=>'Bangka Belitung',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'JAKARTA',
                'region_name'=>'DKI Jakarta',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'BANTEN',
                'region_name'=>'Banten',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'JABAR',
                'region_name'=>'Jawa Barat',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'JATENG',
                'region_name'=>'Jawa Tengah',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'JATIM',
                'region_name'=>'Jawa Timur',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'YOGYA',
                'region_name'=>'D.I. Yogyakarta',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'BALI',
                'region_name'=>'Bali',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'KALTENG',
                'region_name'=>'Kalimatan Tengah',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'KALSEL',
                'region_name'=>'Kalimatan Selatan',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'KALBAR',
                'region_name'=>'Kalimatan Barat',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'KALTIM',
                'region_name'=>'Kalimatan Timur',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'MALUKU',
                'region_name'=>'Maluku',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'MALUKU UTARA',
                'region_name'=>'Maluku Utara',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'SULUT',
                'region_name'=>'Sulawesi Utara',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'SULTENG',
                'region_name'=>'Sulawesi Tengah',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'SULBAR',
                'region_name'=>'Sulawesi Barat',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'SULSEL',
                'region_name'=>'Sulawesi Selatan',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'SULTRA',
                'region_name'=>'Sulawesi Tenggara',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'NTB',
                'region_name'=>'Nusa Tenggara Barat',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'NTT',
                'region_name'=>'Nusa Tenggara Timur',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'GORONTALO',
                'region_name'=>'Gorontalo',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'PAPUA',
                'region_name'=>'Papua',
                'active'=>'Yes'
            ],
            [
                'country_code'=>'ID',
                'region_code'=>'PAPUA BARAT',
                'region_name'=>'Papua Barat',
                'active'=>'Yes'
            ],
        ]);
    }
}