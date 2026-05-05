<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CLPExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    protected $forwarder_id = null;
    protected $shipper_id = null;
    protected $date_from = null;
    protected $date_to = null;
    protected $container_no = null;

    public function __construct($forwarder_id, $shipper_id, $date_from, $date_to, $container_no) {
        $this->forwarder_id = $forwarder_id;
        $this->shipper_id = $shipper_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->container_no = $container_no;
    }
    
    public function collection()
    {    
        return DB::table("ex_outbound_header as a")
                    ->select(
                        "a.job_no",
                        "a.job_date",
                        "b.forwarder_name",
                        "g.shipper_name",
                        "e.consignee_name",
                        "a.container_no",
                        "c.po_number",
                        "c.peb_no",
                        "d.serial_no",
                        "d.quantity",
                        "d.status_flag"
                    )
                    ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                    ->join("ex_outbound_order as c", "a.id", "c.job_id")
                    ->join("ex_outbound_detail as d", function($query) {
                        $query->on("c.job_id", "d.job_id")
                            ->on("c.id", "d.order_id");
                    })
                    ->join("mt_consignee as e", "c.consignee_id", "e.id")
                    ->join("ex_stock_ledger as f", "d.serial_no", "f.serial_no")
                    ->join("mt_shipper as g", "f.shipper_id", "g.id")
                    ->where(DB::raw("COALESCE(a.forwarder_id, 0)"), "LIKE", $this->forwarder_id)
                    ->where(DB::raw("COALESCE(f.shipper_id, 0)"), "LIKE", $this->shipper_id)
                    ->whereBetween('a.job_date', [date($this->date_from), date($this->date_to)])
                    ->where("a.container_no", "LIKE", $this->container_no)
                    ->orderBy("a.container_no", "ASC")
                    ->orderBy("c.id", "ASC")
                    ->orderBy("d.id", "ASC")
                    ->get();
    }

    public function headings(): array
    {
        return [
            "Job No",
            'Job Date',
            "Forwarder Name", 
            "Shipper Name", 
            "Consignee Name", 
            "Container No", 
            "PO Number", 
            "PEB No", 
            "Pallet No", 
            "Quantity", 
            "Status" 
        ];
    }
}