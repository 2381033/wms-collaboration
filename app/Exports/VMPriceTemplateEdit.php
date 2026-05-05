<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VMPriceTemplateEdit implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $service = null;
    protected $mot = null;
    protected $vendor = [];

    public function __construct($service, $mot, $vendor)
    {
        $this->service = $service;
        $this->mot = $mot;
        $this->vendor = $vendor;
    }

    public function collection()
    {
        if ($this->service == 'FCL' and $this->mot == 'SEA') {
            $table = DB::table("price_master as a")
                ->orderBy('vendor', 'ASC')
                ->join("price_fcl_sea as b", "a.id", "b.id_master")
                ->select(
                    'a.id',
                    'vendor',
                    'origin',
                    'kota_kab',
                    'destination',
                    'mot',
                    'product_type',
                    'service',
                    'vehicle_type',
                    'shipping_line',
                    'trucking_origin',
                    'adm_bl',
                    'segel',
                    'materai',
                    'apbs',
                    'thc_lolo',
                    'ffs',
                    'ocf',
                    'thc_lolo_destinasi',
                    'trucking_destinasi',
                    'price',
                )
                ->where("service", $this->service)
                ->where("mot", $this->mot)
                ->whereIn("vendor", $this->vendor)
                ->where("a.active", 'Yes')
                ->get();
        } else {
            $table = DB::table("price_master")
                ->orderBy('vendor', 'ASC')
                ->where("service", $this->service)
                ->select(
                    'id',
                    'vendor',
                    'origin',
                    'kota_kab',
                    'destination',
                    'mot',
                    'product_type',
                    'service',
                    'uom',
                    'vehicle_type',
                    'min_charge',
                    'price',
                    'valid_untill',
                )
                ->where("mot", $this->mot)
                ->whereIn("vendor", $this->vendor)
                ->where("active", 'Yes')
                ->get();
        }
        return $table;
    }

    public function headings(): array
    {
        if ($this->service == 'FCL' and $this->mot == 'SEA') {
            return [
                "Reference ID",
                "Vendor",
                "origin",
                "Kota Kab.",
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
        } else {
            return [
                "Reference ID",
                "Vendor",
                "origin",
                "Kota Kab.",
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
