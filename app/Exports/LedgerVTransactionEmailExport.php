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

class LedgerVTransactionEmailExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
{
    protected $data = array();

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $list = $this->data;
        // dd($list);
        $data = new Collection($list);
        // dd($data);
        return $data;
    }

    public function headings(): array
    {
        return [
            "Branch id",
            "Principal id",
            "Product Id",
            "Product Code",
            "Ledger OnHand",
            "Ledger Actual",
            "Ledger Booking",
            "Transaction OnHand",
        ];
    }

    public function columnFormats(): array
    {
        return [];
    }
}
