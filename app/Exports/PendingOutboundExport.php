<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PendingOutboundExport implements FromCollection, WithHeadings, ShouldAutoSize
{    
    protected $company_id = null;
    protected $principal_id = null;
    protected $date_from = null;
    protected $date_to = null;

    public function __construct($company_id, $principal_id, $date_from, $date_to) {
        $this->company_id = $company_id;
        $this->principal_id = $principal_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;        
    }

    public function collection()
    {
        $list = DB::table("iv_outbound_job as a")
                            ->select(
                                "f.principal_name",
                                "a.job_no",
                                "a.job_date",
                                "a.description",
                                "c.customer_name",
                                "b.order_no",
                                "e.product_code",
                                "e.product_name",
                                DB::raw("sum(d.qty) as qty")
                            )
                            ->join("iv_outbound_order as b", "a.id", "b.outbound_id")
                            ->join("iv_customer as c", "b.customer_id", "c.id")
                            ->join("iv_outbound_detail as d", "b.id", "d.order_id")
                            ->join("iv_product as e", "d.product_id", "e.id")
                            ->join("iv_principal as f", "a.principal_id", "f.id")
                            ->where("a.company_id", $this->company_id)
                            ->where("a.principal_id", $this->principal_id)
                            ->whereBetween('a.job_date', [date($this->date_from), date($this->date_to)])
                            ->whereNotIn("a.confirmed_flag", ["Yes", "Cancel"])
                            ->orderBy("a.job_no", "ASC")
                            ->orderBy("c.customer_name", "ASC")
                            ->groupBy(
                                "f.principal_name",
                                "a.job_no",
                                "a.job_date",
                                "a.description",
                                "c.customer_name",
                                "b.order_no",
                                "e.product_code",
                                "e.product_name"
                            )
                            ->get();

        return new Collection($list);
    }

    public function headings(): array
    {
        return [
            "Principal Name",
            "Job No",
            "Job Date",
            'Description',
            "Customer Name", 
            "Order No", 
            "SKU Code",
            "SKU Name", 
            "Quantity",
        ];
    }
}