<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow
{
    use Importable;
    public $collection;
    protected $principal = null;

    public function __construct($principal) {
        $this->principal = $principal;
    }

    public function model(array $row)
    {
        $company_id = Auth::user()->company_id;

        return new \App\Models\Master\Customer([
            "company_id" => $company_id,
            "principal_id" => $this->principal,
            "customer_code" => $row['customer_code'],
            "customer_name" => $row['customer_name'],
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
