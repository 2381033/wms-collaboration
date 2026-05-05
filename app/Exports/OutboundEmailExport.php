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

use App\Models\Master\Principal as MasterPrincipal;
use App\Models\Transaction\Outbound\Job as OutboundJob;

class OutboundEmailExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
{
    protected $id = null;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $job = OutboundJob::find($this->id);
        $principal = MasterPrincipal::find($job->principal_id);

        if ($principal->multi_level == "Yes") {
            if ($principal->id == 1) {
                $list = DB::table("iv_outbound_batch as a")
                    ->select(
                        "c.principal_name",
                        "d.ata",
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        "f.site_name",
                        "g.area_name",
                        "a.location_code",
                        "a.pqty",
                        "a.mqty",
                        "a.bqty",
                        "b.puom",
                        "b.muom",
                        "b.buom",
                        DB::raw("a.qty * b.gross_weight as gross_weight"),
                        DB::raw("a.qty * b.volume as volume"),
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_outbound_job as d", "a.outbound_id", "d.id")
                    ->join("iv_customer as e", "a.customer_id", "e.id")
                    ->leftjoin("iv_site as f", "a.site_id", "f.id")
                    ->leftjoin("iv_site_area as g", "a.area_id", "g.id")
                    ->where("a.outbound_id", $this->id)
                    ->get();
            } else if ($principal->id == 3) {
                $list = DB::table("iv_outbound_batch as a")
                    ->select(
                        "c.principal_name",
                        "d.job_no",
                        "d.job_date",
                        "e.customer_code",
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        DB::raw("sum(a.pqty) as pqty"),
                        "b.puom"
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_outbound_job as d", "a.outbound_id", "d.id")
                    ->join("iv_customer as e", "a.customer_id", "e.id")
                    ->where("a.outbound_id", $this->id)
                    ->groupBy(
                        "c.principal_name",
                        "d.job_no",
                        "d.job_date",
                        "e.customer_code",
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        "b.puom"
                    )
                    ->get();
            } else {
                $list = DB::table("iv_outbound_batch as a")
                    ->select(
                        "c.principal_name",
                        "d.ata",
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        DB::raw("sum(a.pqty) as pqty"),
                        DB::raw("sum(a.mqty) as mqty"),
                        DB::raw("sum(a.bqty) as bqty"),
                        "b.puom",
                        "b.muom",
                        "b.buom",
                        DB::raw("sum(a.qty) * b.gross_weight as gross_weight"),
                        DB::raw("sum(a.qty) * b.volume as volume"),
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_outbound_job as d", "a.outbound_id", "d.id")
                    ->join("iv_customer as e", "a.customer_id", "e.id")
                    ->where("a.outbound_id", $this->id)
                    ->groupBy(
                        "c.principal_name",
                        "d.ata",
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        "b.puom",
                        "b.muom",
                        "b.buom",
                        "b.gross_weight",
                        "b.volume"
                    )
                    ->get();
            }
        } else {
            if ($principal->id == 1) {
                $list = DB::table("iv_outbound_batch as a")
                    ->select(
                        "c.principal_name",
                        "d.ata",
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        "f.site_name",
                        "g.area_name",
                        "a.location_code",
                        "a.pqty",
                        "b.puom",
                        DB::raw("a.qty * b.gross_weight as gross_weight"),
                        DB::raw("a.qty * b.volume as volume"),
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_outbound_job as d", "a.outbound_id", "d.id")
                    ->join("iv_customer as e", "a.customer_id", "e.id")
                    ->leftjoin("iv_site as f", "a.site_id", "f.id")
                    ->leftjoin("iv_site_area as g", "a.area_id", "g.id")
                    ->where("a.outbound_id", $this->id)
                    ->get();
            } else if ($principal->id == 3) {
                $list = DB::table("iv_outbound_batch as a")
                    ->select(
                        "c.principal_name",
                        "d.job_no",
                        "d.job_date",
                        "e.customer_code",
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        DB::raw("sum(a.pqty) as pqty"),
                        "b.puom",
                        "a.ean_code"
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_outbound_job as d", "a.outbound_id", "d.id")
                    ->join("iv_customer as e", "a.customer_id", "e.id")
                    ->where("a.outbound_id", $this->id)
                    ->groupBy(
                        "c.principal_name",
                        "d.job_no",
                        "d.job_date",
                        "e.customer_code",
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        "b.puom"
                    )
                    ->get();
            } else {
                $list = DB::table("iv_outbound_batch as a")
                    ->select(
                        "c.principal_name",
                        "d.ata",
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        DB::raw("sum(a.pqty) as pqty"),
                        "b.puom",
                        DB::raw("sum(a.qty) * b.gross_weight as gross_weight"),
                        DB::raw("sum(a.qty) * b.volume as volume"),
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_outbound_job as d", "a.outbound_id", "d.id")
                    ->join("iv_customer as e", "a.customer_id", "e.id")
                    ->where("a.outbound_id", $this->id)
                    ->groupBy(
                        "c.principal_name",
                        "d.ata",
                        "e.customer_name",
                        "a.order_no",
                        "a.product_code",
                        "b.product_name",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        "b.puom",
                        "b.gross_weight",
                        "b.volume"
                    )
                    ->get();
            }
        }

        return new Collection($list);
    }

    public function headings(): array
    {
        $job = OutboundJob::find($this->id);
        $principal = MasterPrincipal::find($job->principal_id);

        if ($principal->multi_level == "Yes") {
            if ($principal->id == 1) {
                return [
                    "Principal Name",
                    "ATD",
                    "Customer Name",
                    "Order NO",
                    "SKU No",
                    'SKU Name',
                    "Batch No",
                    "Document Ref",
                    "Mfg Date",
                    "Exp Date",
                    "Site Name",
                    "Area Name",
                    "Location",
                    "1st Qty",
                    "2nd Qty",
                    "3rd Qty",
                    "1st Unit",
                    "2nd Unit",
                    "3rd Unit",
                    "Gross Weight",
                    "Volume"
                ];
            } else if ($principal->id == 3) {
                return [
                    "Principal Name",
                    "Job No",
                    "Job Date",
                    "Customer Code",
                    "Customer Name",
                    "Order No",
                    "SKU No",
                    'SKU Name',
                    "Qty",
                    "Unit"
                ];
            } else {
                return [
                    "Principal Name",
                    "ATD",
                    "Customer Name",
                    "Order NO",
                    "SKU No",
                    'SKU Name',
                    "Batch No",
                    "Document Ref",
                    "Mfg Date",
                    "Exp Date",
                    "1st Qty",
                    "2nd Qty",
                    "3rd Qty",
                    "1st Unit",
                    "2nd Unit",
                    "3rd Unit",
                    "Gross Weight",
                    "Volume"
                ];
            }
        } else {
            if ($principal->id == 1) {
                return [
                    "Principal Name",
                    "ATD",
                    "Customer Name",
                    "Order NO",
                    "SKU No",
                    'SKU Name',
                    "Batch No",
                    "Document Ref",
                    "Mfg Date",
                    "Exp Date",
                    "Site Name",
                    "Area Name",
                    "Location",
                    "Qty",
                    "Unit",
                    "Gross Weight",
                    "Volume"
                ];
            } else if ($principal->id == 3) {
                return [
                    "Principal Name",
                    "Job No",
                    "Job Date",
                    "Customer Code",
                    "Customer Name",
                    "Order No",
                    "SKU No",
                    'SKU Name',
                    "Qty",
                    "Unit",
                    "Carton ID",
                ];
            } else {
                return [
                    "Principal Name",
                    "ATD",
                    "Customer Name",
                    "Order NO",
                    "SKU No",
                    'SKU Name',
                    "Batch No",
                    "Document Ref",
                    "Mfg Date",
                    "Exp Date",
                    "Qty",
                    "Unit",
                    "Gross Weight",
                    "Volume"
                ];
            }
        }
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
