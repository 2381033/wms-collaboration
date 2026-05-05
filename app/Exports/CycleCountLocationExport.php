<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CycleCountLocationExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $site = null;

    public function __construct($site)
    {
        $this->site = $site;
    }

    public function array(): array
    {
        $data = [];
        $site = DB::table('iv_site')->where('id', $this->site)->value('site_name');
        $data = [
            [$site, ''],
        ];
        return $data;
    }

    public function headings(): array
    {
        return [
            "Site",
            "Location Code",
        ];
    }
}
