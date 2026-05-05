<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CycleCountSKUExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $site = null;

    public function __construct($site) {
        $this->site = $site;
    }
    
    public function collection()
    {
        return DB::table("iv_site")
        ->select(
            'site_name',
        )
        ->where("id", $this->site)
        ->get();
    }

    public function headings(): array
    {
        return [
            "Site",
            "Product Code",
        ];
    }
}