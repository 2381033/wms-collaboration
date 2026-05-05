<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class outboundOrderDetailExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $principal_id = null;
    public function __construct($principal_id)
    {
        $this->principal_id = $principal_id;
    }

    public function collection()
    {
        $table = DB::table("iv_outbound_detail as a")
            ->select(
                "b.customer_code",
                "b.customer_name",
                "a.order_no",
                "a.document_ref",
                "a.location_from",
                "a.lot_no",
                "a.product_code",
                "c.product_name",
                "a.pqty",
                "a.mqty",
                "a.bqty",
                // "a.principal_id"
            )
            ->join("iv_customer as b", "a.customer_id", "b.id")
            ->join("iv_product as c", "a.product_id", "c.id")
            ->where("a.outbound_id", $this->principal_id)
            ->get();
        return $table;
    }

    public function headings(): array
    {
        return [
            "Customer Code",
            "Customer Name",
            "Order No",
            "Customer Ref",
            "SKU No",
            "SKU Name",
            "Batch No",
            "Qty 1",
            "Qty 2",
            "Qty 3",
            $this->principal_id == 32 || $this->principal_id == 40  ? 'Location Code' : '',
        ];
    }
}
