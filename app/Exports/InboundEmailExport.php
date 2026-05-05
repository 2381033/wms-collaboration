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
use App\Models\Transaction\Inbound\Job as InboundJob;

class InboundEmailExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
{
    protected $id = null;
    protected $principal_id = null;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $job = InboundJob::find($this->id);
        $principal = MasterPrincipal::find($job->principal_id);

        $this->principal_id = $job->principal_id;

        if ($principal->multi_level == "Yes") {
            if ($principal->id == 1) {
                $list = DB::table("iv_inbound_batch as a")
                    ->select(
                        "c.principal_name",
                        "d.ata",
                        "a.vehicle_no",
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
                        "a.lot_no",
                        "a.document_ref",
                        "a.mfg_date",
                        "a.exp_date",
                        "e.site_name",
                        "f.area_name",
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
                    ->join("iv_inbound_job as d", "a.inbound_id", "d.id")
                    ->leftJoin("iv_site as e", "a.site_id", "e.id")
                    ->leftjoin("iv_site_area as f", "a.area_id", "f.id")
                    ->where("a.inbound_id", $this->id)
                    ->get();
            } else if ($principal->id == 3) {
                $list = DB::table("iv_inbound_batch as a")
                    ->select(
                        "d.job_no",
                        "d.job_date",
                        "c.principal_name",
                        "d.description",
                        "a.vehicle_no",
                        "e.manufactur_code",
                        "e.manufactur_name",
                        "a.product_code",
                        "b.product_name",
                        DB::raw("sum(a.pqty) as pqty"),
                        "b.puom"
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_inbound_job as d", "a.inbound_id", "d.id")
                    ->leftjoin("iv_manufactur as e", "a.manufactur_id", "e.id")
                    ->where("a.inbound_id", $this->id)
                    ->groupBy(
                        "d.job_no",
                        "d.job_date",
                        "c.principal_name",
                        "d.description",
                        "a.vehicle_no",
                        "e.manufactur_code",
                        "e.manufactur_name",
                        "a.product_code",
                        "b.product_name",
                        "b.puom"
                    )
                    ->get();
            } else {
                $list = DB::table("iv_inbound_batch as a")
                    ->select(
                        "c.principal_name",
                        "d.ata",
                        "a.vehicle_no",
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
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
                    ->join("iv_inbound_job as d", "a.inbound_id", "d.id")
                    ->where("a.inbound_id", $this->id)
                    ->groupBy(
                        "c.principal_name",
                        "d.ata",
                        "a.vehicle_no",
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
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
                $list = DB::table("iv_inbound_batch as a")
                    ->select(
                        "c.principal_name",
                        "d.ata",
                        "a.vehicle_no",
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
                        "a.lot_no",
                        "a.document_ref",
                        "e.site_name",
                        "f.area_name",
                        "a.location_code",
                        "a.mfg_date",
                        "a.exp_date",
                        "a.pqty",
                        "b.puom",
                        DB::raw("a.qty * b.gross_weight as gross_weight"),
                        DB::raw("a.qty * b.volume as volume"),
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_inbound_job as d", "a.inbound_id", "d.id")
                    ->leftJoin("iv_site as e", "a.site_id", "e.id")
                    ->leftjoin("iv_site_area as f", "a.area_id", "f.id")
                    ->where("a.inbound_id", $this->id)
                    ->get();
            } else if ($principal->id == 3) {
                $list = DB::table("iv_inbound_batch as a")
                    ->select(
                        "d.job_no",
                        "d.job_date",
                        "c.principal_name",
                        "d.description",
                        "a.vehicle_no",
                        "e.manufactur_code",
                        "e.manufactur_name",
                        "a.product_code",
                        "b.product_name",
                        DB::raw("sum(a.pqty) as pqty"),
                        "b.puom",
                        "a.ean_code",
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_inbound_job as d", "a.inbound_id", "d.id")
                    ->leftjoin("iv_manufactur as e", "a.manufactur_id", "e.id")
                    ->where("a.inbound_id", $this->id)
                    ->groupBy(
                        "d.job_no",
                        "d.job_date",
                        "c.principal_name",
                        "d.description",
                        "a.vehicle_no",
                        "e.manufactur_code",
                        "e.manufactur_name",
                        "a.product_code",
                        "b.product_name",
                        "b.puom"
                    )
                    ->get();
            } else {
                $list = DB::table("iv_inbound_batch as a")
                    ->select(
                        "c.principal_name",
                        "d.ata",
                        "a.vehicle_no",
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
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
                    ->join("iv_inbound_job as d", "a.inbound_id", "d.id")
                    ->where("a.inbound_id", $this->id)
                    ->groupBy(
                        "c.principal_name",
                        "d.ata",
                        "a.vehicle_no",
                        "a.product_code",
                        "b.product_name",
                        "a.po_number",
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
        $job = InboundJob::find($this->id);
        $principal = MasterPrincipal::find($job->principal_id);

        if ($principal->multi_level == "Yes") {
            if ($principal->id == 1) {
                return [
                    "Principal Name",
                    "ATA",
                    "Vehicle No",
                    "SKU No",
                    'SKU Name',
                    "DO No",
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
                    "Job No",
                    "Job Date",
                    "Principal Name",
                    "Description",
                    "Vehicle No",
                    "Manufactur Code",
                    "Manufactur Name",
                    "SKU No",
                    'SKU Name',
                    "Qty",
                    "Unit"
                ];
            } else {
                return [
                    "Principal Name",
                    "ATA",
                    "Vehicle No",
                    "SKU No",
                    'SKU Name',
                    "DO No",
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
                    "ATA",
                    "Vehicle No",
                    "SKU No",
                    'SKU Name',
                    "DO No",
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
                    "Job No",
                    "Job Date",
                    "Principal Name",
                    "Description",
                    "Vehicle No",
                    "Manufactur Code",
                    "Manufactur Name",
                    "SKU No",
                    'SKU Name',
                    "Qty",
                    "Unit",
                    "Carton ID"
                ];
            } else {
                return [
                    "Principal Name",
                    "ATA",
                    "Vehicle No",
                    "SKU No",
                    'SKU Name',
                    "DO No",
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

        if ($this->principal_id == 3) {
            return [
                'H' => NumberFormat::FORMAT_TEXT,
            ];
        } else {
            return [
                'D' => NumberFormat::FORMAT_TEXT,
            ];
        }
    }
}
