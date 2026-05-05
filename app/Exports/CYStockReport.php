<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CYStockReport implements FromCollection, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    protected $forwarder_id = null;
    protected $date_from = null;
    protected $date_to = null;

    public function __construct($forwarder_id, $date_from, $date_to) {
        $this->forwarder_id = $forwarder_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
    }

    public function collection()
    {       
        return DB::table("cy_stock_ledger as a")
                    ->select(
                        "b.forwarder_name", 
                        "a.booking_no",
                        "a.job_no",
                        "a.job_date",
                        "a.reference_no",
                        "a.vehicle_no",
                        "a.driver_name",
                        "c.type_name as invoice_type", 
                        "d.size_name", 
                        "e.type_name",
                        "a.container_status",
                        "a.container_no",
                        DB::raw("CASE WHEN a.qtyp = 1 THEN 'Book' Else 'Stock' END as status")
                    )
                    ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                    ->join("cy_invoice_type as c", "a.invoice_type", "c.id")
                    ->join("iv_container_size as d", "a.size_id", "d.id")
                    ->join("iv_container_type as e", "a.type_id", "e.id")
                    ->where("a.qtys", 1)
                    ->where("a.forwarder_id", "like", $this->forwarder_id)
                    ->whereBetween(DB::raw("COALESCE(a.job_date, now())"), [$this->date_from, $this->date_to])
                    ->orderBy("b.forwarder_name", "ASC")
                    ->orderBy("a.job_date", "ASC")
                    ->get();
    }

    public function headings(): array
    {
        return [
            "Company Name",
            'Booking No',
            "Inbound No", 
            "Inbound Date", 
            "Reference No", 
            "Vehicle No", 
            "Driver Name", 
            "Invoice Type", 
            "Container Size", 
            "Container Type", 
            "Container Status", 
            "Container No",
            "Status" 
        ];
    }
}