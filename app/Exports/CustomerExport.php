<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomerExport implements FromCollection, ShouldAutoSize
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
        return DB::table("iv_customer as a")
        ->select(
            "b.principal_name",
            "a.id",
            "a.customer_name", 
            "a.address1", 
            "a.address2", 
            "a.address3", 
            "a.address4", 
            "a.phone", 
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
            "Customer", 
            "Address", 
            "Village", 
            "Districts", 
            "City", 
            "Phone", 
            "Email", 
            "PIC Name", 
            "PIC Phone", 
        ];
    }
}