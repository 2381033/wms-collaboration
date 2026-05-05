<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ScanCargoExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $job_no;

    public function __construct($job_no) {
        $this->job_no = $job_no;
    }
    
    public function collection()
    {
        return DB::table("ex_scan_cargo_header as header")
        ->select(
            'header.job_no',
            'header.po_no',
            // 'header.qty',
            'detail.barcode',
            'detail.scan_at',
            'detail.scan_by',
        )
        ->join("ex_scan_cargo_detail as detail", "header.id", "detail.id_header")
        ->where("detail.job_no", $this->job_no)
        ->get();
    }

    public function headings(): array
    {
        return [
            "Job No",
            "PO No",
            // "Qty",
            "Carton ID",
            "Scan Time",
            "Scan By",
        ];
    }
}