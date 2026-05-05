<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditPutawayDailyExport implements FromCollection, WithHeadings
{
    protected $jobs;
    protected $stocks;

    public function __construct($jobs, $stocks)
    {
        $this->jobs = $jobs;
        $this->stocks = $stocks;
    }

    public function collection()
    {
        $rows = [];

        foreach ($this->jobs as $job) {
            foreach ($this->stocks[$job->id] ?? [] as $row) {
                $rows[] = [
                    'Job No'       => $job->job_no,
                    'Principal'    => $job->principal_name,
                    'PO No'        => $row->po_number,
                    'SKU Code'     => $row->product_code,
                    'SKU Name'     => $row->product_name,
                    'Batch No'     => $row->lot_no,
                    'Site'         => $row->site_name,
                    'Area'         => $row->area_name,
                    'Location'     => $row->location_code,
                    'Qty'          => $row->qty,
                    'UOM'          => $row->puom,
                ];
            }
        }

        return new Collection($rows);
    }

    public function headings(): array
    {
        return [
            'Job No',
            'Principal',
            'PO No',
            'SKU Code',
            'SKU Name',
            'Batch No',
            'Site',
            'Area',
            'Location',
            'Qty',
            'UOM',
            'Checklist'
        ];
    }
}
