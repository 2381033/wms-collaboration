<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LedgerExportMailExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
{
    protected $data = array();

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $list = $this->data;
        $data = new Collection($list);
        return $data;
    }

    public function headings(): array
    {
        return [
            "FORWARDER",
            "PEB",
            "AJU",
            "PO. NUMBER",
            "DESTINATION",
            "TOTAL QTY",
            "TOTAL PALLET",
            "DATE",
        ];
    }

    public function columnFormats(): array
    {
        // Menentukan format untuk kolom yang ingin diatur
        return [];
    }
}
