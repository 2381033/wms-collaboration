<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VMPriceTemplate implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $service = null;
    protected $mot = null;

    public function __construct($service, $mot) {
        $this->service = $service;
        $this->mot = $mot;
    }

    public function collection()
    {
        $table = DB::table("price_master")
            ->where("active", 'Yes')
            ->get()->take(0);
        return $table;
    }

    public function headings(): array
    {
        if($this->service == 'FCL' and $this->mot == 'SEA'){
            return [
                "origin",
                "kota_kab",
                "destination",
                "mot",
                "product_type",
                "service",
                "vehicle_type",
                "shipping_line",
                "trucking_origin",
                "adm_bl",
                "segel",
                "materai",
                "apbs",
                "thc_lolo",
                "ffs",
                "ocf",
                "thc_lolo_destinasi",
                "trucking_destinasi",
                "price",
            ];
        }else{
            return [
                "vendor",
                "origin",
                "kota_kab",
                "destination",
                "mot",
                "product_type",
                "service",
                "uom",
                "vehicle_type",
                "min_charge",
                "price",
                "valid_untill"
            ];
        }
    }
}
