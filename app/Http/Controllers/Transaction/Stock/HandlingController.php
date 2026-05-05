<?php

namespace App\Http\Controllers\Transaction\Stock;

use App\Exports\HandlingExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Master\Principal as MasterPrincipal;
use App\Models\Master\Storage as MasterStorage;
use App\Models\Master\Handling as MasterHandling;

class HandlingController extends Controller
{
    public function index() {
        return view("report.handling-report.index");
    }

    public function report(Request $request) {
        $company_id = Auth::user()->company_id;
        $GroupOn = $request->GroupOn;

        $date_from = '1990-01-01';
        $date_to = '2999-12-31';

        if (!empty($request->date_from) && !empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
        } else if (!empty($request->date_from) && empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = '2999-12-31';
        }

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_from);
        $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_to);
        $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $datediff = \Carbon\Carbon::parse($date_from)->diffInDays(\Carbon\Carbon::parse($date_to));

        if ( $request->principal_id == "2" ) {
            $stock_before = DB::table("iv_stock_transaction as a")
                                ->select(
                                    DB::raw("sum(CASE WHEN a.job_type IN ('IMP', 'TFRI', 'ADJ+') THEN a.qty * b.volume ELSE -1 * a.qty * b.volume END ) as qty_open"),
                                )
                                ->join("iv_product as b", "a.product_id", "b.id")
                                ->where("a.company_id", $company_id)
                                ->where("a.principal_id", $request->principal_id)
                                ->where("a.job_date", "<", date($date_from))
                                ->first();

            $list = DB::table("iv_stock_transaction as a")
                            ->select(
                                "a.job_date",
                                DB::raw("sum(CASE WHEN a.job_type IN ('IMP', 'TFRI', 'ADJ+') THEN a.qty * b.volume ELSE -1 * a.qty * b.volume END ) as qty"),
                                DB::raw("sum(CASE WHEN a.job_type IN ('IMP') THEN a.qty * b.volume ELSE 0 END ) as qty_inbound"),
                                DB::raw("sum(CASE WHEN a.job_type IN ('EXP') THEN a.qty * b.volume ELSE 0 END ) as qty_outbound")
                            )
                            ->join('iv_product as b', 'a.product_id', 'b.id')
                            ->where('a.company_id', $company_id)
                            ->where('a.principal_id', $request->principal_id)
                            ->whereBetween('a.job_date', [date($date_from), date($date_to)])
                            ->groupBy("a.job_date")
                            ->orderBy('a.job_date', 'asc')
                            ->get();

            $storage = [];
            $storage_list = [];
            $hand_in = [];
            $hand_out = [];

            if ( isset($stock_before) ) {
                if ( $stock_before->qty_open > 0 ) {
                    $open_qty = $stock_before->qty_open;
                } else {
                    $open_qty = 0;
                }
            } else {
                $open_qty = 0;
            }

            $balance = $open_qty;

            for ($i=0; $i <= $datediff; $i++) {
                $date = \Carbon\Carbon::parse($date_from)->addDays($i);

                $data = $list->where("job_date", $date)->first();

                $handling_in = 0;
                $handling_out = 0;

                if ( isset($data) ) {
                    $balance = $balance + $data->qty;
                    $handling_in = $data->qty_inbound;
                    $handling_out = $data->qty_outbound;
                } else {
                    $balance = $balance;
                    $handling_in = 0;
                    $handling_out = 0;
                }

                if ( $date > \Carbon\Carbon::today() ) {
                    $balance = 0;
                    $handling_in = 0;
                    $handling_out = 0;
                }

                $storage_list[] = [
                    "date" => $date->format("Y-m-d"),
                    "qty_storage" => $balance,
                    "handling_in" => $handling_in,
                    "handling_out" => $handling_out
                ];

                if ( $balance > 0 ) {
                    $storage[] = $balance;
                }

                $hand_in[] = $handling_in;
                $hand_out[] = $handling_out;
            }

            $qty_storage = array_sum($storage) / count($storage);
            $qty_inbound = array_sum($hand_in);
            $qty_outbound = array_sum($hand_out);

            $storage_master = MasterStorage::where("principal_id", $request->principal_id)->first();

            if ( isset($storage_master) ) {
                $cpu_storage = $qty_storage >= $storage_master->quota ? $qty_storage : $storage_master->quota;

                $amount_storage = $cpu_storage * $storage_master->cpu;
            } else {
                $amount_storage = 0;
            }

            $handling_master = MasterHandling::where("principal_id", $request->principal_id)->get();

            if ( isset($handling_master) ) {
                $inbound_handling = $handling_master->where("job_type", "IMP")->first();

                if ( isset($inbound_handling) ) {
                    $cpu_inbound = $qty_inbound >= $inbound_handling->cpu_middle ? $qty_inbound : $inbound_handling->cpu_middle;

                    $amount_inbound = $cpu_inbound * $inbound_handling->cpu;
                } else {
                    $amount_inbound = 0;
                }

                $outbound_handling = $handling_master->where("job_type", "EXP")->first();

                if ( isset($outbound_handling) ) {
                    $cpu_outbound = $qty_outbound >= $outbound_handling->cpu_middle ? $qty_outbound : $outbound_handling->cpu_middle;

                    $amount_outbound = $cpu_outbound * $outbound_handling->cpu;
                } else {
                    $amount_outbound = 0;
                }
            } else {
                $amount_inbound = 0;
                $amount_outbound = 0;
            }

            switch ($GroupOn) {
                case 'summary':
                    $principal = MasterPrincipal::find($request->principal_id);

                    $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$principal->principal_name</td></tr>";
                    $headerOne .= "<tr><td>Period</td><td>:</td><td>" . \Carbon\Carbon::parse($date_from)->format("d/m/Y") . " - " . \Carbon\Carbon::parse($date_to)->format("d/m/Y") . "</td></tr>";

                    $headOne = collect([
                        [ "name"=>"Quantity", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Amount", "rowspan"=>"1", "colspan"=>"3" ],
                    ]);

                    $headTwo = collect([
                        [ "name"=>"In" ],
                        [ "name"=>"Out" ],
                        [ "name"=>"Storage" ],
                        [ "name"=>"In" ],
                        [ "name"=>"Out" ],
                        [ "name"=>"Storage" ],
                    ]);

                    $bodyOne = collect([
                        [ "name"=>"Handling In", "field_name"=>"handling_in", "class"=>"right" ],
                        [ "name"=>"Handling Out", "field_name"=>"handling_out", "class"=>"right" ],
                        [ "name"=>"Storage", "field_name"=>"storage", "class"=>"right" ],
                        [ "name"=>"Handling In", "field_name"=>"amount_inbound", "class"=>"right" ],
                        [ "name"=>"Handling Out", "field_name"=>"amount_outbound", "class"=>"right" ],
                        [ "name"=>"Storage", "field_name"=>"amount_storage", "class"=>"right" ],
                    ]);

                    $listData[] = [
                        "storage"=>number_format($qty_storage, 2, ".", ","),
                        "handling_in"=>number_format($qty_inbound, 2, ".", ","),
                        "handling_out"=>number_format($qty_outbound, 2, ".", ","),
                        "amount_storage"=>number_format($amount_storage, 2, ".", ","),
                        "amount_inbound"=>number_format($amount_inbound, 2, ".", ","),
                        "amount_outbound"=>number_format($amount_outbound, 2, ".", ","),
                    ];

                    $data = [
                        "title"=>"Handling Report ( Summary )",
                        "css"=>"portrait",
                        "headerOne"=>$headerOne,
                        "headOne"=>$headOne->toArray(),
                        "headTwo"=>$headTwo->toArray(),
                        "bodyOne"=>$bodyOne->toArray(),
                        "listData"=>$listData,
                        "columnCount"=>6
                    ];

                    return view("report", $data);
                    break;
                case 'detail':
                    $principal = MasterPrincipal::find($request->principal_id);

                    $data = [
                        "title"=>"Handling Report ( Detail )",
                        "css"=>"portrait",
                        "principal_name"=>$principal->principal_name,
                        "periode"=>\Carbon\Carbon::parse($date_from)->format("d/m/Y") . " - " . \Carbon\Carbon::parse($date_to)->format("d/m/Y"),
                        "qty_storage"=>$qty_storage,
                        "qty_inbound"=>$qty_inbound,
                        "qty_outbound"=>$qty_outbound,
                        "amount_storage"=>$amount_storage,
                        "amount_inbound"=>$amount_inbound,
                        "amount_outbound"=>$amount_outbound,
                        "list"=>$storage_list,
                        "columnCount"=>11
                    ];

                    return view("report.handling-report.detail", $data);
                    break;
            }
        } else if ( $request->principal_id == "1" ) {
            $list = DB::table("iv_stock_transaction as a")
                            ->select(
                                "a.job_date",
                                "a.job_no",
                                "a.product_code",
                                "a.job_type",
                                DB::raw("count(*) as jumlah")
                            )
                            ->join('iv_product as b', 'a.product_id', 'b.id')
                            ->where('a.company_id', $company_id)
                            ->where('a.principal_id', $request->principal_id)
                            ->whereBetween('a.job_date', [date($date_from), date($date_to)])
                            ->groupBy(
                                "a.job_date",
                                "a.job_no",
                                "a.product_code",
                                "a.job_type",
                            )
                            ->orderBy('a.job_date', 'asc')
                            ->get();

            $hand_in = [];
            $hand_out = [];

            for ($i=0; $i <= $datediff; $i++) {
                $date = \Carbon\Carbon::parse($date_from)->addDays($i);

                $handling_in = $list->where("job_date", $date)->where("job_type", "IMP")->sum("jumlah");
                $handling_out = $list->where("job_date", $date)->where("job_type", "EXP")->sum("jumlah");

                $hand_in[] = $handling_in;
                $hand_out[] = $handling_out;
            }

            $qty_inbound = array_sum($hand_in);
            $qty_outbound = array_sum($hand_out);

            $handling_master = MasterHandling::where("principal_id", $request->principal_id)->get();

            if ( isset($handling_master) ) {
                $inbound_handling = $handling_master->where("job_type", "IMP")->first();

                if ( isset($inbound_handling) ) {
                    $cpu_inbound = $qty_inbound >= $inbound_handling->cpu_middle ? $qty_inbound : $inbound_handling->cpu_middle;

                    $amount_inbound = $cpu_inbound * $inbound_handling->cpu;
                } else {
                    $amount_inbound = 0;
                }

                $outbound_handling = $handling_master->where("job_type", "EXP")->first();

                if ( isset($outbound_handling) ) {
                    $cpu_outbound = $qty_outbound >= $outbound_handling->cpu_middle ? $qty_outbound : $outbound_handling->cpu_middle;

                    $amount_outbound = $cpu_outbound * $outbound_handling->cpu;
                } else {
                    $amount_outbound = 0;
                }
            } else {
                $amount_inbound = 0;
                $amount_outbound = 0;
            }
        } else if ( $request->principal_id == "8" ) {
            $stock_before = DB::table("iv_stock_transaction as a")
                                ->select(
                                    DB::raw("count(a.location_code) as qty_open"),
                                )
                                ->join("iv_product as b", "a.product_id", "b.id")
                                ->where("a.company_id", $company_id)
                                ->where("a.principal_id", $request->principal_id)
                                ->where("a.job_date", "<", date($date_from))
                                ->groupBy("a.location_code")
                                ->first();

            $storage = [];
            $storage_list = [];
            $hand_in = [];
            $hand_out = [];

            if ( isset($stock_before) ) {
                if ( $stock_before->qty_open > 0 ) {
                    $open_qty = $stock_before->qty_open;
                } else {
                    $open_qty = 0;
                }
            } else {
                $open_qty = 0;
            }

            $balance = $open_qty;

            for ($i=0; $i <= $datediff; $i++) {
                $date = \Carbon\Carbon::parse($date_from)->addDays($i);

                $qty_inbound = DB::table("iv_stock_transaction as a")
                                ->where('a.company_id', $company_id)
                                ->where('a.principal_id', $request->principal_id)
                                ->where("a.job_date", $date)
                                ->where("a.job_type", "IMP")
                                ->count();

                $qty_outbound = DB::table("iv_stock_transaction as a")
                                ->where('a.company_id', $company_id)
                                ->where('a.principal_id', $request->principal_id)
                                ->where("a.job_date", $date)
                                ->where("a.job_type", "EXP")
                                ->count();

                $qty = DB::table("iv_stock_transaction as a")
                                ->where('a.company_id', $company_id)
                                ->where('a.principal_id', $request->principal_id)
                                ->where("a.job_date", $date)
                                ->where("a.job_type", "IMP")
                                ->count();

                $handling_in = 0;
                $handling_out = 0;

                $balance = $balance + $qty;
                $handling_in = $qty_inbound;
                $handling_out = $qty_outbound;

                if ( $date > \Carbon\Carbon::today() ) {
                    $balance = 0;
                    $handling_in = 0;
                    $handling_out = 0;
                }

                $storage_list[] = [
                    "date" => $date->format("Y-m-d"),
                    "qty_storage" => $balance,
                    "handling_in" => $handling_in,
                    "handling_out" => $handling_out
                ];

                if ( $balance > 0 ) {
                    $storage[] = $balance;
                }

                $hand_in[] = $handling_in;
                $hand_out[] = $handling_out;
            }

            $qty_storage = array_sum($storage) / count($storage);
            $qty_inbound = array_sum($hand_in);
            $qty_outbound = array_sum($hand_out);

            $storage_master = MasterStorage::where("principal_id", $request->principal_id)->first();

            if ( isset($storage_master) ) {
                $cpu_storage = $qty_storage >= $storage_master->quota ? $qty_storage : $storage_master->quota;

                $amount_storage = $cpu_storage * $storage_master->cpu;
            } else {
                $amount_storage = 0;
            }

            $handling_master = MasterHandling::where("principal_id", $request->principal_id)->get();

            if ( isset($handling_master) ) {
                $inbound_handling = $handling_master->where("job_type", "IMP")->first();

                if ( isset($inbound_handling) ) {
                    $cpu_inbound = $qty_inbound >= $inbound_handling->cpu_middle ? $qty_inbound : $inbound_handling->cpu_middle;

                    $amount_inbound = $cpu_inbound * $inbound_handling->cpu;
                } else {
                    $amount_inbound = 0;
                }

                $outbound_handling = $handling_master->where("job_type", "EXP")->first();

                if ( isset($outbound_handling) ) {
                    $cpu_outbound = $qty_outbound >= $outbound_handling->cpu_middle ? $qty_outbound : $outbound_handling->cpu_middle;

                    $amount_outbound = $cpu_outbound * $outbound_handling->cpu;
                } else {
                    $amount_outbound = 0;
                }
            } else {
                $amount_inbound = 0;
                $amount_outbound = 0;
            }

            switch ($GroupOn) {
                case 'summary':
                    $principal = MasterPrincipal::find($request->principal_id);

                    $headerOne = "<tr><td>Principal Name</td><td>:</td><td>$principal->principal_name</td></tr>";
                    $headerOne .= "<tr><td>Period</td><td>:</td><td>" . \Carbon\Carbon::parse($date_from)->format("d/m/Y") . " - " . \Carbon\Carbon::parse($date_to)->format("d/m/Y") . "</td></tr>";

                    $headOne = collect([
                        [ "name"=>"Quantity", "rowspan"=>"1", "colspan"=>"3" ],
                        [ "name"=>"Amount", "rowspan"=>"1", "colspan"=>"3" ],
                    ]);

                    $headTwo = collect([
                        [ "name"=>"In" ],
                        [ "name"=>"Out" ],
                        [ "name"=>"Storage" ],
                        [ "name"=>"In" ],
                        [ "name"=>"Out" ],
                        [ "name"=>"Storage" ],
                    ]);

                    $bodyOne = collect([
                        [ "name"=>"Handling In", "field_name"=>"handling_in", "class"=>"right" ],
                        [ "name"=>"Handling Out", "field_name"=>"handling_out", "class"=>"right" ],
                        [ "name"=>"Storage", "field_name"=>"storage", "class"=>"right" ],
                        [ "name"=>"Handling In", "field_name"=>"amount_inbound", "class"=>"right" ],
                        [ "name"=>"Handling Out", "field_name"=>"amount_outbound", "class"=>"right" ],
                        [ "name"=>"Storage", "field_name"=>"amount_storage", "class"=>"right" ],
                    ]);

                    $listData[] = [
                        "storage"=>number_format($qty_storage, 2, ".", ","),
                        "handling_in"=>number_format($qty_inbound, 2, ".", ","),
                        "handling_out"=>number_format($qty_outbound, 2, ".", ","),
                        "amount_storage"=>number_format($amount_storage, 2, ".", ","),
                        "amount_inbound"=>number_format($amount_inbound, 2, ".", ","),
                        "amount_outbound"=>number_format($amount_outbound, 2, ".", ","),
                    ];

                    $data = [
                        "title"=>"Handling Report ( Summary )",
                        "css"=>"portrait",
                        "headerOne"=>$headerOne,
                        "headOne"=>$headOne->toArray(),
                        "headTwo"=>$headTwo->toArray(),
                        "bodyOne"=>$bodyOne->toArray(),
                        "listData"=>$listData,
                        "columnCount"=>6
                    ];

                    return view("report", $data);
                    break;
                case 'detail':
                    $principal = MasterPrincipal::find($request->principal_id);

                    $data = [
                        "title"=>"Handling Report ( Detail )",
                        "css"=>"portrait",
                        "principal_name"=>$principal->principal_name,
                        "periode"=>\Carbon\Carbon::parse($date_from)->format("d/m/Y") . " - " . \Carbon\Carbon::parse($date_to)->format("d/m/Y"),
                        "qty_storage"=>$qty_storage,
                        "qty_inbound"=>$qty_inbound,
                        "qty_outbound"=>$qty_outbound,
                        "amount_storage"=>$amount_storage,
                        "amount_inbound"=>$amount_inbound,
                        "amount_outbound"=>$amount_outbound,
                        "list"=>$storage_list,
                        "columnCount"=>11
                    ];

                    return view("report.handling-report.detail", $data);
                    break;
            }
        }
    }

    public function export(Request $request) {
        $time = \Carbon\Carbon::now()->format("dmy.His");

        $date_from = '1990-01-01';
        $date_to = '2999-12-31';

        if (!empty($request->date_from) && !empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
        } else if (!empty($request->date_from) && empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = '2999-12-31';
        }

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_from);
        $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_to);
        $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $date_diff = \Carbon\Carbon::parse($date_from)->diffInDays(\Carbon\Carbon::parse($date_to));

        return Excel::download(new HandlingExport($request->principal_id, $date_from, $date_to, $date_diff), "handling-$time.xlsx");
    }
}
