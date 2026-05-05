<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LocationExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $site = null;

    public function __construct($site) {
        $this->site = $site;
    }
    
    public function collection()
    {
        return DB::table("iv_location as a")
        ->select(
            "b.id as site_id",
            "b.site_name",
            "c.id as area_id",
            "c.area_name",
            "a.id",
            "a.location_code", 
            "a.location_name", 
            "a.status_code", 
            "a.type_id", 
            "d.description", 
            "a.location_aisle", 
            "a.location_column", 
            "a.location_level", 
        )
        ->join("iv_site as b", "a.site_id", "b.id")
        ->join("iv_site_area as c", "a.area_id", "c.id")
        ->join("iv_location_type as d", "a.type_id", "d.id")
        ->where("a.site_id", $this->site)
        ->get();
    }

    public function headings(): array
    {
        return [
            "Site ID",
            'Site Name',
            "Area ID", 
            "Area Name", 
            "Location ID", 
            "Location Code", 
            "Location Name", 
            "Status", 
            "Type ID", 
            "Type Name", 
            "Aisle", 
            "Column", 
            "Level", 
        ];
    }
}