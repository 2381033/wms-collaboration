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

class AutoCorrectionEmailExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
{
    protected $id = null;

    public function __construct($id) {
        $this->id = $id;
    }

    public function collection()
    {
        $date_from = \Carbon\Carbon::today()->startOfDay();
        $date_to = \Carbon\Carbon::today()->endOfDay();
        $list = DB::table("iv_stock_auto_adjustment_log as cl")
            ->leftjoin('mt_branch as mb','mb.id','cl.branch_id')
            ->leftjoin('iv_principal as pr','pr.id','cl.principal_id')
            ->leftjoin('iv_site as st','st.id','cl.site_id')
            ->leftjoin('iv_site_area as ar','ar.id','cl.area_id')
            ->leftjoin('iv_location as lc','lc.id','cl.location_id')
            ->select(
                'mb.branch_name',
                'pr.principal_name',
                'pr.principal_name',
                'cl.product_id',
                'cl.product_code',
                'cl.lot_no',
                'cl.mfg_date',
                'cl.exp_date',
                'cl.ledger_onhand',
                'cl.ledger_booking',
                'cl.ledger_available',
                'cl.transaction_onhand',
                'cl.variance',
                'st.site_name',
                'ar.area_name',
                'cl.location_id',
                'lc.location_code',
                'cl.action',
                'cl.correction_date'
            )
            // ->where("cl.id", $this->id)
            ->whereBetween("correction_date", [$date_from, $date_to])
            ->get();
        // dd($list,$this->id);

        return new Collection($list);
    }

    public function headings(): array {
        return [
            "Branch Name",
            "Principal Name",
            "Product ID",
            "Product Code",
            "Batch Number",
            "Mfg Date",
            "Exp Date",
            "Ledger OnHand",
            "Ledger Booking",
            "Ledger Available",
            "OnHand Base on Transaction",
            "Variance",
            "Site",
            "Area",
            "Location ID",
            "Location Code",
            "Action",
            "Auto COrrection Date"
        ];
    }

    public function columnFormats(): array {
        return [
            'D' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
