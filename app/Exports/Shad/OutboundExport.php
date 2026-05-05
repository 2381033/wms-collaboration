<?php

namespace App\Exports\Shad;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OutboundExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $company_id = null;
    protected $principal_id = null;
    protected $date_from = null;
    protected $date_to = null;
    protected $customer_from = null;
    protected $customer_to = null;
    protected $store_from = null;
    protected $store_to = null;

    public function __construct($company_id, $principal_id, $date_from, $date_to, $customer_from, $customer_to, $store_from, $store_to) {
        $this->company_id = $company_id;
        $this->principal_id = $principal_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->customer_from = $customer_from;
        $this->customer_to = $customer_to;
        $this->store_from = $store_from;
        $this->store_to = $store_to;
    }

    public function collection()
    {
        $list = DB::table("iv_outbound_job as a")
                    ->select(
                        "a.job_no",           
                        "a.job_date",
                        "f.customer_code",
                        "f.customer_name",
                        "b.order_no",
                        "e.store_code",
                        "e.store_name",
                        "e.address1",
                        "e.address2",
                        "e.address3",
                        "e.address4",
                        "c.reference_no",
                        "g.product_code",
                        "h.product_name",
                        DB::raw("sum(g.qty) as qty"),
                        "g.puom"
                    )
                    ->join("iv_outbound_order as b", "a.id", "b.outbound_id")
                    ->join("iv_outbound_despatch as c", function($query) {
                        $query->on("b.outbound_id", "c.outbound_id")
                              ->on("b.customer_id", "c.customer_id");
                    })
                    ->join("tm_store as e", "c.store_id", "e.id")
                    ->join("iv_customer as f", "b.customer_id", "f.id")
                    ->join("iv_outbound_batch as g", function($query) {
                        $query->on("b.outbound_id", "g.outbound_id")
                              ->on("b.customer_id", "g.customer_id")
                              ->on("b.order_no", "g.order_no");
                    })
                    ->join("iv_product as h", "g.product_id", "h.id")
                    ->where('a.company_id', $this->company_id)
                    ->where('a.principal_id', $this->principal_id)
                    ->where("a.confirmed_flag", "Yes")
                    ->whereBetween('a.job_date', [date($this->date_from), date($this->date_to)])
                    ->whereBetween(DB::raw("COALESCE(f.customer_code, '')"), [$this->customer_from, $this->customer_to])
                    ->whereBetween(DB::raw("COALESCE(e.store_code, '')"), [$this->store_from, $this->store_to])
                    ->groupBy(          
                        "a.job_no",             
                        "a.job_date",
                        "f.customer_code",
                        "f.customer_name",
                        "b.order_no",
                        "e.store_code",
                        "e.store_name",
                        "e.address1",
                        "e.address2",
                        "e.address3",
                        "e.address4",
                        "c.reference_no",
                        "g.product_code",
                        "h.product_name",
                        "g.puom"
                    )
                    ->orderBy("f.customer_name", "ASC")
                    ->orderBy("b.order_no", "ASC")
                    ->get();

        return new Collection($list);
    }

    public function headings(): array
    {
        return [
            'Job No',
            'Job Date',
            'Customer Code',
            'Customer Name',
            'Order No',
            'Store Code',
            'Store Name',
            'Address 1',
            'Address 2',
            'Address 3',
            'Address 4',
            'Reference No',
            "SKU No",
            "SKU Name",
            "Qty",
            "Unit",
        ];
    }
}