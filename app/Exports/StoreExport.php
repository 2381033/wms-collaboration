<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StoreExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $principal = null;

    public function __construct($principal) {
        $this->principal = $principal;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table("tm_store as a")
        ->select(
            "b.principal_name",
            "a.id",
            "a.store_code", 
            "a.store_name", 
            "a.country_code", 
            "a.region_code", 
            "a.city_code", 
            "a.address1", 
            "a.address2", 
            "a.address3", 
            "a.address4", 
            "a.telephone", 
            "a.email", 
            "a.pic_name", 
            "a.pic_phone" 
        )
        ->join("iv_principal as b", "a.principal_id", "b.id")
        ->where("a.principal_id", $this->principal)
        ->get();
    }

    public function headings(): array
    {
        return [
            "Principal",
            'id',
            "Customer Code", 
            "Customer Name", 
            "Country", 
            "Region", 
            "City", 
            "Address 1", 
            "Address 2", 
            "Address 3", 
            "Address 4", 
            "Phone", 
            "Email", 
            "PIC Name", 
            "PIC Phone", 
        ];
    }
}