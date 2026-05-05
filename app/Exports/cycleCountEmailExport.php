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
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use App\Models\Master\Principal as MasterPrincipal;

class cycleCountEmailExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison, WithDrawings, WithCustomStartCell, WithEvents
{
    protected $branch_id = null;
    protected $principal_id = null;

    public function __construct($principal_id, $branch_id)
    {
        $this->branch_id = $branch_id;
        $this->principal_id = $principal_id;
    }

    public function collection()
    {
        $date_from = date('Y-m-d', strtotime('-7 days'));
        $date_to   = date('Y-m-d', strtotime('-1 days'));
        $list = DB::table("iv_cyclecount_detail as icd")
            ->select(DB::raw('DATE_FORMAT(icd.created_at, "%d/%m/%Y") AS cycle_count_date'), 'icd.product_code AS sku_no', 'ipr.product_name AS sku_name', 'icd.location_code AS location', 'icd.pqty AS soh', 'icd.puom AS uom',  DB::raw("CASE WHEN isl.status = 'G' THEN 'GOODS' ELSE 'BAD' END AS status"), 'icd.pqty AS qty_actual', 'icd.scan_by AS penghitung')
            ->join('iv_cyclecount_job as icj', 'icj.job_no', 'icd.job_no')
            ->join('iv_stock_ledger as isl', 'isl.id', 'icd.id_ledger')
            ->join('iv_product as ipr', 'ipr.id', 'icd.product_id')
            ->join('iv_principal as ip', 'ip.id', 'icd.principal_id')
            ->whereBetween("icd.created_at", [$date_from, $date_to])
            ->where("icd.principal_id", $this->principal_id)
            ->where("icd.branch_id", $this->branch_id);
        // ->toSql();
        // dd($list->toSql(),$list->getBindings());

        return new Collection($list->get());
    }

    public function headings(): array
    {
        $header = [
            "Tanggal Cycle Count",
            "SKU No",
            "SKU Name",
            "Location",
            "SOA",
            "UOM",
            "Status",
            "Qty Aktual",
            "Penghitung"
        ];

        return $header;
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/images/logos.png'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function map($item): array
    {
        return [
            $item->cycle_count_date,
            $item->sku_no,
            $item->sku_name,
            $item->location,
            $item->soh,
            $item->uom,
            $item->status,
            $item->qty_actual,
            $item->penghitung
        ];
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('F1', 'PT MASAJI KARGO SENTRA TAMA');
                $event->sheet->mergeCells('F1:I1');
                $event->sheet->getDelegate()->getStyle('F1:H1')->getFont()->setBold(true);

                $event->sheet->setCellValue('A4', 'INVENTORI - CYCLE COUNT REPORT');
                $event->sheet->mergeCells('A4:I4');
                $event->sheet->getDelegate()->getStyle('A4:I4')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A4:I4')->getFont()->setSize(18);
                $event->sheet->getDelegate()->getStyle('A4:I4')->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle('A4:I4')->getAlignment()->setHorizontal('center');

                $date_from = date('d', strtotime('-7 days'));
                $date_to   = date('d', strtotime('-3 days'));
                $month_from   = date('M', strtotime('-7 days'));
                $month_to   = date('M', strtotime('-3 days'));
                $year_from   = date('Y', strtotime('-7 days'));
                $year_to   = date('Y', strtotime('-3 days'));
                $string_date = '';
                if ($year_from == $year_to) {
                    if ($month_from == $month_to) {
                        $string_date = $date_from . ' - ' . $date_to . ',' . $month_to . '/' . $year_to;
                    } else {
                        $string_date = $date_from . ' ' . $month_from . ' - ' . $date_to . ' ' . $month_to . '/' . $year_to;
                    }
                } else {
                    $string_date = $date_from . ' ' . $month_from . ' ' . $year_from . ' - ' . $date_to . ' ' . $month_to . ' ' . $year_to;
                }
                // dd($string_date);
                $event->sheet->setCellValue('A5', "Periode " . $string_date);
                $event->sheet->mergeCells('A5:I5');

                $event->sheet->getDelegate()->getStyle('A7:I7')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A7:I7')->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle('A7:I7')->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('A7:I7')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFF00');
                $event->sheet->setAutoFilter('A7:I7');
            },
        ];
    }
}
