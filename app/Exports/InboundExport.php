<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

use App\Models\Master\Principal as MasterPrincipal;
use App\Models\Transaction\Inbound\Job as InboundJob;

class InboundExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $id = null;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $job = InboundJob::find($this->id);
        $principal = MasterPrincipal::find($job->principal_id);

        if ($principal->multi_level == "Yes") {
            $list = DB::table("iv_inbound_batch as a")
                ->select(
                    "a.vehicle_no",
                    "a.product_code",
                    "b.product_name",
                    "a.po_number",
                    "a.lot_no",
                    // "a.document_ref",
                    // "a.mfg_date",
                    "a.exp_date",
                    // "c.site_name",
                    // "d.area_name",
                    "a.location_code",
                    "a.qty",
                    "a.mqty",
                    "a.bqty",
                    "b.muppp",
                    DB::raw("a.qty * b.muppp as quantum"),
                    "b.puom",
                    "b.muom",
                    "b.buom",
                    DB::raw("a.qty * b.gross_weight as gross_weight"),
                    DB::raw("a.qty * b.volume as volume"),
                )
                ->join("iv_product as b", "a.product_id", "b.id")
                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                ->where("a.inbound_id", $this->id)
                ->get();
        } else {
            $list = DB::table("iv_inbound_batch as a")
                ->select(
                    "a.vehicle_no",
                    "a.product_code",
                    "b.product_name",
                    "a.po_number",
                    "a.lot_no",
                    "a.document_ref",
                    "a.mfg_date",
                    "a.exp_date",
                    "c.site_name",
                    "d.area_name",
                    "a.location_code",
                    "a.pqty",
                    "b.puom",
                    DB::raw("a.qty * b.gross_weight as gross_weight"),
                    DB::raw("a.qty * b.volume as volume"),
                )
                ->join("iv_product as b", "a.product_id", "b.id")
                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                ->where("a.inbound_id", $this->id)
                ->get();
        }

        return new Collection($list);
    }

    public function headings(): array
    {
        $job = InboundJob::find($this->id);
        $principal = MasterPrincipal::find($job->principal_id);

        if ($principal->multi_level == "Yes") {
            return [
                "Vehicle No",
                "SKU No",
                'SKU Name',
                "DO No",
                "Batch No",
                // "Document Ref",
                // "Mfg Date",
                "Exp Date",
                // "Site Name",
                // "Area Name",
                "Location",
                "1st Qty",
                "2nd Qty",
                "3rd Qty",
                "Conversion Qty",
                "Quantum",
                "1st Unit",
                "2nd Unit",
                "3rd Unit",
                "Gross Weight",
                "Volume"
            ];
        } else {
            return [
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
        }
    }
}
