<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class StockLedgerReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    protected $report_type = null;
    protected $branch_id = null;
    protected $principal_id = null;
    protected $group_from = null;
    protected $group_to = null;
    protected $brand_from = null;
    protected $brand_to = null;
    protected $product_from = null;
    protected $product_to = null;
    protected $exp_from = null;
    protected $exp_to = null;
    protected $site_id = null;
    protected $area_id = null;
    protected $location_from = null;
    protected $location_to = null;

    public function __construct($report_type, $branch_id, $principal, $group_from, $group_to, $brand_from, $brand_to, $product_from, $product_to, $exp_from, $exp_to, $site_id, $area_id, $location_from, $location_to)
    {
        $this->report_type = $report_type;
        $this->branch_id = $branch_id;
        $this->principal_id = $principal;
        $this->group_from = $group_from;
        $this->group_to = $group_to;
        $this->brand_from = $brand_from;
        $this->brand_to = $brand_to;
        $this->product_from = $product_from;
        $this->product_to = $product_to;
        $this->exp_from = $exp_from;
        $this->exp_to = $exp_to;
        $this->site_id = $site_id;
        $this->area_id = $area_id;
        $this->location_from = $location_from;
        $this->location_to = $location_to;
    }

    public function collection()
    {
        $company_id = Auth::user()->company_id;
        if ($this->principal_id === 'ALL') {
            $principal = (object)[
                'principal_name' => 'ALL PRINCIPAL',
                'multi_level' => 'No' // atau default yg paling aman
            ];
        } else {
            $principal = \App\Models\Master\Principal::find($this->principal_id);
        }

        $user = DB::table("users as a")
            ->select(
                "a.*",
                "b.role_name"
            )
            ->join("sm_role as b", "a.role_id", "b.id")
            ->where("a.username", Auth::user()->username)
            ->first();
        $isVendor = ($user->role_name == 'Vendor');

        if ($this->report_type == "summary") {
            $principal_id = $this->principal_id;
            $stok = DB::table('iv_stock_transaction as a')
                ->select(
                    'product_id',
                    DB::raw("sum(CASE WHEN a.job_type IN ('IMP', 'TFRI', 'ADJ+') THEN a.qty ELSE 0 END ) as qty_received"),
                    DB::raw("sum(CASE WHEN a.job_type IN ('EXP', 'TFRO', 'ADJ-') THEN a.qty ELSE 0 END ) as qty_issue")
                )
                ->join('iv_product as b', 'a.product_id', 'b.id')
                ->where('a.company_id', $company_id)
                ->when($this->principal_id !== 'ALL', function ($q) use ($principal_id) {
                    $q->where('a.principal_id', $principal_id);
                })
                ->where('a.branch_id', $this->branch_id)
                ->whereBetween('b.product_code', [$this->product_from, $this->product_to])
                ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $this->area_id)
                ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$this->location_from, $this->location_to])
                ->groupBy('a.product_id')
                ->orderBy("product_name", "asc");
            $stok_query = $stok->toSql();
            $stok_bindings = $stok->getBindings();
            $stok = $stok->get();

            $summary = DB::table("iv_stock_ledger as a")
                ->select(
                    "p.principal_name",
                    "a.planning",
                    "a.product_code",
                    "a.product_id",
                    "b.product_name",
                    "b.uppp",
                    "b.muppp",
                    "b.volume",
                    "b.gross_weight",
                    "b.puom",
                    "b.muom",
                    "b.buom",
                    DB::raw("sum(a.qtys) as qtys"),
                    DB::raw("sum(a.qtya) as qtya"),
                    DB::raw("sum(a.qtyp) as qtyp"),
                    DB::raw("CASE WHEN a.status = 'B' THEN 'BAD' ELSE 'GOODS' END as status_code")
                )
                ->join("iv_product as b", "a.product_id", "b.id")
                ->join("iv_product_group as e", "b.group_id", "e.id")
                ->join("iv_product_brand as f", "b.brand_id", "f.id")
                ->join("iv_principal as p", "a.principal_id", "p.id")
                ->leftjoin("iv_location as g", "a.location_id", "g.id")
                ->where("a.company_id", $company_id)
                ->when($this->principal_id !== 'ALL', function ($q) use ($principal_id) {
                    $q->where('a.principal_id', $principal_id);
                })
                ->where("a.branch_id", $this->branch_id)
                ->where("a.qtys", ">", 0)
                ->whereBetween("e.group_code", [$this->group_from, $this->group_to])
                ->whereBetween("f.brand_code", [$this->brand_from, $this->brand_to])
                ->whereBetween("b.product_code", [$this->product_from, $this->product_to])
                ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $this->site_id)
                ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $this->area_id)
                ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$this->location_from, $this->location_to])
                ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [date($this->exp_from), date($this->exp_to)])
                ->groupBy("a.product_id")
                ->orderBy("b.product_name", "asc");
            $summary_query = $summary->toSql();
            $summary_bindings = $summary->getBindings();
            $summary = $summary->get();

            $arr_product = $summary->pluck('product_id')->toArray();

            $stok = $stok->whereIn('product_id', $arr_product);

            $total = [];
            foreach ($stok as $key => $value) {
                $total[] = $value->qty_received - $value->qty_issue;
            }

            if ($principal->multi_level == "Yes") {

                $list = [];
                foreach ($summary as $value) {
                    $list[] = [
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                        "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                        "bqtys" => $value->qtys % $value->uppp % $value->muppp,
                        "pqtyp" => ($value->qtyp  - ($value->qtyp % $value->uppp)) / $value->uppp,
                        "mqtyp" => (($value->qtyp % $value->uppp) - (($value->qtyp % $value->uppp) % $value->muppp)) / $value->muppp,
                        "bqtyp" => $value->qtyp % $value->uppp % $value->muppp,
                        "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                        "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                        "bqtya" => $value->qtya % $value->uppp % $value->muppp,
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "buom" => $value->buom,
                        "status" => 'GOODS',
                    ];
                }
            } else {
                $list = [];
                foreach ($summary as $key => $value) {
                    $plans = [];
                    if ($isVendor && !empty($value->planning)) {
                        $plans = is_array($value->planning)
                            ? $value->planning
                            : json_decode($value->planning, true);
                    }
                    $plans = collect($plans)->sortBy('week')->values()->toArray();
                    $max = 4;
                    for ($i = 1; $i <= $max; $i++) {
                        $value->{"ip_$i"} = '';
                        $value->{"week_$i"} = '';
                    }
                    if ($isVendor) {
                        foreach ($plans as $index => $plan) {
                            if ($index < $max) {
                                $value->{"ip_" . ($index + 1)} = $plan['ip'] ?? '';
                                $value->{"week_" . ($index + 1)} = $plan['week'] ?? '';
                            }
                        }
                    }
                    $dataRow = [
                        "principal_name" => $value->principal_name,
                        "product_code"   => $value->product_code,
                        "product_name"   => $value->product_name,
                        "pqtys"          => $value->qtys,
                        "pqtyp"          => ($value->qtyp  - ($value->qtyp % $value->uppp)) / $value->uppp,
                        "pqtya"          => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                        "puom"           => $value->puom,
                        "status"         => 'GOODS',
                    ];

                    if ($isVendor) {
                        for ($i = 1; $i <= $max; $i++) {
                            $dataRow["IP $i"] = $value->{"ip_$i"};
                            $dataRow["Week $i"] = $value->{"week_$i"};
                        }
                    }
                    $list[] = $dataRow;
                }
            }
        } else {
            $stok_query = 0;
            $stok_bindings = 0;
            $principal_id = $this->principal_id;
            $summary = DB::table("iv_stock_ledger as a")
                ->select(
                    "p.principal_name",
                    "a.job_no",
                    "a.job_date",
                    "a.product_code",
                    "b.product_name",
                    "a.lot_no",
                    "a.mfg_date",
                    "a.exp_date",
                    "c.site_name",
                    "d.area_name",
                    "a.location_code",
                    "a.qtys",
                    "a.qtyp",
                    "a.qtya",
                    "b.puom",
                    "b.muom",
                    "b.buom",
                    "a.freeze_flag",
                    "b.volume",
                    "b.gross_weight",
                    "b.uppp",
                    "b.muppp",
                    DB::raw("CASE WHEN a.status = 'K' THEN 'Quarantine' WHEN a.status = 'B' THEN 'Bad' ELSE 'Goods' END as status_code"),
                    "vh.vehicle_no"
                )
                ->join("iv_principal as p", "p.id", "a.principal_id")
                ->join("iv_product as b", "a.product_id", "b.id")
                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                ->leftJoin("iv_inbound_vehicle as vh", "a.job_no", "vh.job_no")
                ->leftJoin("iv_site_area as d", "a.area_id", "d.id")
                ->join("iv_product_group as e", "b.group_id", "e.id")
                ->join("iv_product_brand as f", "b.brand_id", "f.id")
                ->leftjoin("iv_location as g", "a.location_id", "g.id")
                ->where("a.company_id", $company_id)
                ->when($this->principal_id !== 'ALL', function ($q) use ($principal_id) {
                    $q->where('a.principal_id', $principal_id);
                })
                ->where("a.branch_id", $this->branch_id)
                ->where("a.qtys", ">", 0)
                ->whereBetween("e.group_code", [$this->group_from, $this->group_to])
                ->whereBetween("f.brand_code", [$this->brand_from, $this->brand_to])
                ->whereBetween("b.product_code", [$this->product_from, $this->product_to])
                ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $this->site_id)
                ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $this->area_id)
                ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$this->location_from, $this->location_to])
                ->whereBetween(DB::raw("COALESCE(a.exp_date, now())"), [date($this->exp_from), date($this->exp_to)])
                ->orderBy("b.product_name", "asc");
            $summary_query = $summary->toSql();
            $summary_bindings = $summary->getBindings();
            $summary = $summary->get();


            if ($principal->multi_level == "Yes") {
                $list = [];
                foreach ($summary as $value) {
                    $list[] = [
                        "container_no" => $value->vehicle_no,
                        "job_no" => $value->job_no,
                        "job_date" => $value->job_date,
                        "product_code" => $value->product_code,
                        "product_name" => $value->product_name,
                        "conversi" => $value->muppp,
                        "lot_no" => $value->lot_no,
                        "mfg_date" => $value->mfg_date,
                        "exp_date" => $value->exp_date,
                        "site_name" => $value->site_name,
                        "area_name" => $value->area_name,
                        "location_code" => $value->location_code,
                        "pqtys" => ($value->qtys  - ($value->qtys % $value->uppp)) / $value->uppp,
                        "mqtys" => (($value->qtys % $value->uppp) - (($value->qtys % $value->uppp) % $value->muppp)) / $value->muppp,
                        "quantum_soh" => $value->qtys * $value->muppp,
                        "pqtyp" => ($value->qtyp  - ($value->qtyp % $value->uppp)) / $value->uppp,
                        "mqtyp" => (($value->qtyp % $value->uppp) - (($value->qtyp % $value->uppp) % $value->muppp)) / $value->muppp,
                        "quantum_sob" => $value->qtyp * $value->muppp,
                        "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                        "mqtya" => (($value->qtya % $value->uppp) - (($value->qtya % $value->uppp) % $value->muppp)) / $value->muppp,
                        "quantum_soa" => $value->qtya * $value->muppp,
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "freeze_flag" => $value->freeze_flag,
                        "gross_weight" => $value->gross_weight,
                        "volume" => $value->volume,
                        "status_code" => $value->status_code,
                    ];
                }
            } else {
                $list = [];
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
                        "pqtyp" => ($value->qtyp  - ($value->qtyp % $value->uppp)) / $value->uppp,
                        "pqtya" => ($value->qtya  - ($value->qtya % $value->uppp)) / $value->uppp,
                        "puom" => $value->puom,
                        "freeze_flag" => $value->freeze_flag,
                        "gross_weight" => $value->gross_weight,
                        "volume" => $value->volume,
                        "status_code" => $value->status_code,
                        // "container_no" => $value->vehicle_no,
                    ];
                }
            }
        }
        return new Collection($list);
    }

    public function headings(): array
    {
        $company_id = Auth::user()->company_id;
        if ($this->principal_id === 'ALL') {
            $principal = (object)[
                'principal_name' => 'ALL PRINCIPAL',
                'multi_level' => 'No' // atau default yg paling aman
            ];
        } else {
            $principal = \App\Models\Master\Principal::find($this->principal_id);
        }
        $user = DB::table("users as a")
            ->select(
                "a.*",
                "b.role_name"
            )
            ->join("sm_role as b", "a.role_id", "b.id")
            ->where("a.username", Auth::user()->username)
            ->first();
        $isVendor = ($user->role_name == 'Vendor');

        if ($this->report_type == "summary") {
            if ($principal->multi_level == "Yes") {
                return [
                    "SKU No",
                    "SKU Name",
                    "1st SOH",
                    "2nd SOH",
                    "3rd SOH",
                    "1st SOB",
                    "2nd SOB",
                    "3rd SOB",
                    "1st SOA",
                    "2nd SOA",
                    "3rd SOA",
                    "1st Unit",
                    "2nd Unit",
                    "3rd Unit",
                    "Status",
                ];
            } else {
                $header = [
                    "Principal",
                    "SKU No",
                    "SKU Name",
                    "SOH",
                    "SOB",
                    "SOA",
                    "Unit",
                    "Status",
                ];
                if ($isVendor) {
                    for ($i = 1; $i <= 4; $i++) {
                        $header[] = "IP $i";
                        $header[] = "Week $i";
                    }
                }
            }
            return $header;
        } else {
            if ($principal->multi_level == "Yes") {
                return [
                    "Container No",
                    "Job No",
                    "Job Date",
                    "SKU No",
                    "SKU Name",
                    "Conversi Qty",
                    "Batch No",
                    "Mfg Date",
                    "Exp Date",
                    "Site Name",
                    "Area Name",
                    "Location",
                    "1st SOH",
                    "2nd SOH",
                    "Quantum",
                    "1st SOB",
                    "2nd SOB",
                    "Quantum",
                    "1st SOA",
                    "2nd SOA",
                    "Quantum",
                    "1st Unit",
                    "2nd Unit",
                    "Freeze",
                    "Gross Weight",
                    "Volume",
                    "Status",
                ];
            } else {
                $header = [
                    "Principal",
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
                    "SOH",
                    "SOB",
                    "SOA",
                    "Unit",
                    "Freeze",
                    "Gross Weight",
                    "Volume",
                    "Status",
                ];
            }

            return $header;
        }
    }
}
