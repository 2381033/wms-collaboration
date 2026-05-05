<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StoreImport implements ToModel, WithHeadingRow
{
    protected $principal = null;

    public function __construct($principal) {
        $this->principal = $principal;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $company_id = Auth::user()->company_id;

        return new \App\Models\Master\Store([
            "company_id" => $company_id,
            "principal_id" => $this->principal,
            "store_code" => $row['store_code'],
            "store_name" => $row['store_name'],
            "country_code" => $row['country'],
            "region_code" => $row['region'],
            "city_code" => $row['city'],
            "address1" => $row['address_1'],
            "address2" => $row['address_2'],
            "address3" => $row['address_3'],
            "address4" => $row['address_4'],
            "phone" => $row['phone'],
            "email" => $row['email'],
            "pic_name" => $row['pic_name'],
            "pic_phone" => $row['pic_phone']
        ]);
    }
}
