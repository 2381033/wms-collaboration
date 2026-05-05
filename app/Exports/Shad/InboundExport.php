<?php

namespace App\Exports\Shad;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InboundExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $list = DB::table("iv_inbound_job as a")
                    ->select(
                        "a.job_no",
                        "a.job_date",
                        "a.description",
                        "b.vehicle_no",
                        "b.transporter_name",
                        "b.driver_name",
                        "e.manufactur_code",
                        "e.manufactur_name",
                        "c.product_code",
                        "d.product_name",
                        DB::raw("sum(c.qty) as qty"),
                        "c.puom"
                    )
                    ->join("iv_inbound_vehicle as b", "a.id", "b.inbound_id")
                    ->join("iv_inbound_batch as c", function($query) {
                        $query->on("b.inbound_id", "c.inbound_id")
                            ->on("b.vehicle_no", "c.vehicle_no");
                    })
                    ->join("iv_product as d", "c.product_id", "d.id")
                    ->leftjoin("iv_manufactur as e", "c.manufactur_id", "e.id")
                    ->where('a.company_id', $this->company_id)
                    ->where('a.principal_id', $this->principal_id)
                    ->whereBetween('a.job_date', [date($this->date_from), date($this->date_to)])
                    ->where("a.confirmed_flag", "Yes")
                    ->groupBy(                        
                        "a.job_no",
                        "a.job_date",
                        "a.description",
                        "b.vehicle_no",
                        "b.transporter_name",
                        "b.driver_name",
                        "e.manufactur_code",
                        "e.manufactur_name",
                        "c.product_code",
                        "d.product_name",
                        "c.puom"
                    )
                    ->get();

        return new Collection($list);
    }

    public function headings(): array
    {
        return [
            'Job No',
            'Job Date',
            'Description',
            'Vehicle No',
            'Transporter Name',
            'Driver Name',
            'Vendor Code',
            'Vendor Name',
            "SKU No",
            "SKU Name",
            "Qty",
            "Unit",
        ];
    }
}