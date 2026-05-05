<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class inboundPackingExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $id = null;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $table = DB::table("iv_inbound_detail as a")
            ->select(
                "a.vehicle_no",
                "a.product_code",
                "b.product_name",
                "a.po_number",
                "a.lot_no",
                "a.document_ref",
                "a.mfg_date",
                "a.exp_date",
                "a.manufactur_id",
                "c.status_name",
                "a.pallet_id",
                "a.pqty",
                "a.mqty",
                "a.bqty"
            )
            ->join("iv_product as b", "a.product_id", "b.id")
            ->join("iv_stock_status as c", "a.status_id", "c.id")
            ->where("a.inbound_id", $this->id)
            ->get();

        return $table;
    }

    public function headings(): array
    {
        return [
            "Vehicle No",
            "SKU No",
            "SKU Name",
            "DO No",
            "Batch No",
            "Document Ref",
            "Mfg Date",
            "Exp Date",
            "Manufactur",
            "Status",
            "Pallet ID",
            "Qty 1",
            "Qty 2",
            "Qty 3",
        ];
    }
}
