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

class StockEmailExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
{
    protected $branch_id = null;
    protected $principal_id = null;

    public function __construct($principal_id, $branch_id)
    {
        $this->branch_id = $branch_id;
        $this->principal_id = $principal_id;
    }

    public function collection()
    {
        $principal = MasterPrincipal::find($this->principal_id);

        if ($principal->id == 1) {
            $summary = DB::table("iv_stock_ledger as a")
                ->select(
                    "c.principal_name",
                    "a.job_no",
                    "a.job_date",
                    "a.product_code",
                    "b.product_name",
                    "a.lot_no",
                    "a.mfg_date",
                    "a.exp_date",
                    "d.site_name",
                    "e.area_name",
                    "a.location_code",
                    "a.qtys",
                    "a.qtyp",
                    "a.qtya",
                    "b.puom",
                    "b.muom",
                    "b.buom",
                    "a.freeze_flag",
                    "b.gross_weight",
                    "b.volume",
                    "b.uppp",
                    "b.muppp",
                )
                ->join("iv_product as b", "a.product_id", "b.id")
                ->join("iv_principal as c", "a.principal_id", "c.id")
                ->leftJoin("iv_site as d", "a.site_id", "d.id")
                ->leftjoin("iv_site_area as e", "a.area_id", "e.id")
                ->where("a.company_id", $principal->company_id)
                ->where("a.branch_id", $this->branch_id)
                ->where("a.principal_id", $this->principal_id)
                ->where("a.qtys", ">", 0)
                ->orderBy("b.product_name", "asc")
                ->get();
        } else {
            $summary = DB::table("iv_stock_ledger as a")
                ->select(
                    "c.principal_name",
                    "a.job_no",
                    "a.job_date",
                    "a.product_code",
                    "b.product_name",
                    "a.lot_no",
                    "d.manufactur_name",
                    "a.mfg_date",
                    "a.exp_date",
                    DB::raw("sum(a.qtys) as qtys"),
                    DB::raw("sum(a.qtyp) as qtyp"),
                    DB::raw("sum(a.qtya) as qtya"),
                    "b.puom",
                    "b.muom",
                    "b.buom",
                    "a.freeze_flag",
                    "b.gross_weight",
                    "b.volume",
                    "b.uppp",
                    "b.muppp",
                )
                ->join("iv_product as b", "a.product_id", "b.id")
                ->join("iv_principal as c", "a.principal_id", "c.id")
                ->leftjoin("iv_manufactur as d", "a.manufactur_id", "d.id")
                ->where("a.company_id", $principal->company_id)
                ->where("a.branch_id", $this->branch_id)
                ->where("a.principal_id", $this->principal_id)
                ->where("a.qtys", ">", 0)
                ->groupBy(
                    "c.principal_name",
                    "a.job_no",
                    "a.job_date",
                    "a.product_code",
                    "b.product_name",
                    "a.lot_no",
                    "d.manufactur_name",
                    "a.mfg_date",
                    "a.exp_date",
                    "b.puom",
                    "b.muom",
                    "b.buom",
                    "a.freeze_flag",
                    "b.volume",
                    "b.gross_weight",
                    "b.uppp",
                    "b.muppp",
                )
                ->orderBy("b.product_name", "asc")
                ->get();
        }

        if ($principal->multi_level == "Yes") {
            $list = [];

            if ($principal->id == 1) {
                foreach ($summary as $value) {
                    $list[] = [
                        "principal_name" => $value->principal_name,
                        "job_no" => $value->job_no,
                        "job_date" => $value->job_date,
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "lot_no" => $value->lot_no,
                        "mfg_date" => $value->mfg_date,
                        "exp_date" => $value->exp_date,
                        "site_name" => $value->site_name,
                        "area_name" => $value->area_name,
                        "location_code" => $value->location_code,
                        "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                        "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                        "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "buom" => $value->buom,
                        "freeze_flag" => $value->freeze_flag,
                        "gross_weight" => $value->gross_weight * $value->qtys,
                        "volume" => $value->volume * $value->qtys,
                    ];
                }
            } else {
                foreach ($summary as $value) {
                    $list[] = [
                        "principal_name" => $value->principal_name,
                        "job_no" => $value->job_no,
                        "job_date" => $value->job_date,
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "lot_no" => $value->lot_no,
                        "manufactur_name" => $value->manufactur_name,
                        "mfg_date" => $value->mfg_date,
                        "exp_date" => $value->exp_date,
                        "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                        "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                        "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "buom" => $value->buom,
                        "freeze_flag" => $value->freeze_flag,
                        "gross_weight" => $value->gross_weight * $value->qtys,
                        "volume" => $value->volume * $value->qtys,
                    ];
                }
            }
        } else {
            $list = [];
            if ($principal->id == 1) {
                foreach ($summary as $value) {
                    $list[] = [
                        "principal_name" => $value->principal_name,
                        "job_no" => $value->job_no,
                        "job_date" => $value->job_date,
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "lot_no" => $value->lot_no,
                        "mfg_date" => $value->mfg_date,
                        "exp_date" => $value->exp_date,
                        "site_name" => $value->site_name,
                        "area_name" => $value->area_name,
                        "location_code" => $value->location_code,
                        "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                        "puom" => $value->puom,
                        "freeze_flag" => $value->freeze_flag,
                        "gross_weight" => $value->gross_weight * $value->qtys,
                        "volume" => $value->volume * $value->qtys,
                    ];
                }
            } else {
                foreach ($summary as $value) {
                    $list[] = [
                        "principal_name" => $value->principal_name,
                        "job_no" => $value->job_no,
                        "job_date" => $value->job_date,
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "lot_no" => $value->lot_no,
                        "manufactur_name" => $value->manufactur_name,
                        "mfg_date" => $value->mfg_date,
                        "exp_date" => $value->exp_date,
                        "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                        "puom" => $value->puom,
                        "freeze_flag" => $value->freeze_flag,
                        "gross_weight" => $value->gross_weight * $value->qtys,
                        "volume" => $value->volume * $value->qtys,
                    ];
                }
            }
        }

        return new Collection($list);
    }

    public function headings(): array
    {
        $principal = MasterPrincipal::find($this->principal_id);

        if ($principal->multi_level == "Yes") {
            if ($principal->id == 1) {
                return [
                    "Principal Name",
                    "Job No",
                    "Job Date",
                    "SKU No",
                    "SKU Name",
                    "Batch No",
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
                    "Freeze",
                    "Gross Weight",
                    "Volume",
                ];
            } else {
                return [
                    "Principal Name",
                    "Job No",
                    "Job Date",
                    "SKU No",
                    "SKU Name",
                    "Batch No",
                    "Manufactur Name",
                    "Mfg Date",
                    "Exp Date",
                    "1st Qty",
                    "2nd Qty",
                    "3rd Qty",
                    "1st Unit",
                    "2nd Unit",
                    "3rd Unit",
                    "Freeze",
                    "Gross Weight",
                    "Volume",
                ];
            }
        } else {
            if ($principal->id == 1) {
                $header = [
                    "Principal Name",
                    "Job No",
                    "Job Date",
                    "SKU No",
                    "SKU Name",
                    "Batch No",
                    "Mfg Date",
                    "Exp Date",
                    "Site Name",
                    "Area Name",
                    "Location",
                    "Qty",
                    "Unit",
                    "Freeze",
                    "Gross Weight",
                    "Volume",
                ];
            } else {
                $header = [
                    "Principal Name",
                    "Job No",
                    "Job Date",
                    "SKU No",
                    "SKU Name",
                    "Batch No",
                    "Manufactur Name",
                    "Mfg Date",
                    "Exp Date",
                    "Qty",
                    "Unit",
                    "Freeze",
                    "Gross Weight",
                    "Volume",
                ];
            }
        }

        return $header;
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
