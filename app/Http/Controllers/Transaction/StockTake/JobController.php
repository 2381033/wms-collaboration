<?php

namespace App\Http\Controllers\Transaction\StockTake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\StockTake\Detail as StockTakeDetail;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\StockTake\Job as StockTakeJob;
use App\Models\Master\Location as MasterLocation;
use Hashids\Hashids;
use Illuminate\Support\Carbon;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $details = [];
        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            if (!empty($request->principal_id)) {
                $details = DB::table("iv_stocktake_job as a")
                    ->select("a.*", "c.principal_name")
                    ->join("users_principal as b", "a.principal_id", "b.principal_id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->where("b.user_id", $user_id)
                    ->where("a.company_id", $company_id)
                    ->where("a.principal_id", $request->principal_id)
                    ->whereBetween('a.stocktake_date', [$date_from, $date_to])
                    ->get();
            }

            return datatables()->of($details)
                ->editColumn("stocktake_date", function ($data) {
                    return date("d/m/Y", strtotime($data->stocktake_date));
                })
                ->editColumn("confirmed_flag", function ($data) {
                    if ($data->confirmed_flag == "Yes") {
                        $status = "<div class='btn btn-sm btn-success'>Completed</div>";
                    } else {
                        $status = "<div class='btn btn-sm btn-danger'>Open</div>";
                    }
                    return $status;
                })
                ->addColumn("stocktake_no", function ($data) {
                    $button = "";
                    $button .= "<a href='" . URL("/inventory/stock-take/create/$data->id") . "' class='btn btn-default btn-sm'><i class='far fa-file'></i> " . $data->stocktake_no . "</a>";
                    return $button;
                })
                ->addColumn("buttonStart", function ($data) {

                    if ($data->confirmed_flag == "Yes") {
                        if ($data->start_sto == "Yes") {
                            $status = "<a href='" . URL("/inventory/stock-take/ready/stop/$data->id/$data->principal_id/$data->branch_id") . "' class='btn btn-sm btn-dark'><i class='fas fa-stop'></i> Stop STO</a>";
                        } else {
                            $status = "<a href='" . URL("/inventory/stock-take/ready/start/$data->id/$data->principal_id/$data->branch_id") . "' class='btn btn-sm btn-success'><i class='fas fa-play'></i> Klik For Start STO</a>";
                        }
                    } else {
                        $status = "";
                    }
                    return $status;
                })
                ->rawColumns(["confirmed_flag", "stocktake_no", 'buttonStart'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.stocktake.index");
    }

    public function ready($type, $id, $principal_id, $branch_id)
    {
        DB::transaction(function () use ($type, $id, $principal_id, $branch_id) {

            $flag = $type === 'start' ? 'Yes' : 'No';

            DB::table('iv_stocktake_job')
                ->where('id', $id)
                ->update(['start_sto' => $flag]);

            DB::table('iv_stock_ledger')
                ->where('principal_id', $principal_id)
                ->where('branch_id', $branch_id)
                ->where('qtys', '>', 0)
                ->update(['freeze_flag' => $flag]);
        });

        return redirect()->route('take-job.index');
    }


    public function confirmJob($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $job = DB::table('iv_stocktake_job')
                    ->where('id', $id)
                    ->first();
                DB::table('iv_stocktake_job')
                    ->where('id', $id)
                    ->update([
                        'confirmed_flag' => 'Yes',
                        'confirmed_date' => date('Y-m-d H:i:s'),
                        'confirmed_by' => Auth::user()->username,
                        'user_id' => Auth::user()->username,
                    ]);
                $detail = DB::table('iv_stocktake_detail')
                    ->where('stocktake_id', $id)
                    ->get()
                    ->pluck('serial_id')->toArray();
                DB::table('iv_stock_ledger')
                    ->whereIn('id', $detail)
                    ->update([
                        'freeze_flag' => 'No',
                        'freeze_by' => null,
                        'freeze_date' => null,
                        'freeze_reason' => null
                    ]);
                DB::commit();
                $message = ["success" => 'success'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => [$e->getMessage()]];

                return $message;
            }
        });
        return response()->json($exception);
    }

    public function list(Request $request)
    {
        return view('transaction.stocktake.list');
    }

    public function getList($start, $end)
    {
        $principal_list = Auth::user()->principal;
        $branch         = Auth::user()->branch;
        $data = DB::table("iv_stocktake_detail as a")
            ->select(
                "a.*",
                "b.product_name",
            )
            ->join("iv_product as b", "a.product_id", "b.id")
            ->where("a.scan_flag", "Yes")
            ->where("a.branch_id", $branch->first()->id)
            ->whereDate("a.confirmed_date", ">=", $start)
            ->whereDate("a.confirmed_date", "<=", $end)
            ->whereIn("a.principal_id", $principal_list)
            ->orderBy("variance", "ASC")
            ->get();

        return datatables()->of($data)
            ->editColumn("variance", function ($item) {
                if ($item->variance == "Yes") {
                    $status = "<div class='btn btn-sm btn-danger'><i class='fas fa-exclamation'></i> Variance</div>";
                } else {
                    $status = "<div class='btn btn-sm btn-success'><i class='fas fa-check-circle'></i> Match</div>";
                }
                return $status;
            })
            ->rawColumns(["variance"])
            ->addIndexColumn()
            ->make(true);
    }

    public function getMonitoring($start, $end)
    {
        $principal_list = Auth::user()->principal;
        $branch = Auth::user()->branch;
        $header = StockTakeJob::Where('branch_id', $branch->first()->id)->orderBy('id', 'DESC')->first();
        $site_id = DB::table('iv_principal_site')
            ->where('principal_id', $header->principal_id)
            ->value('site_id');
        $masterLoc = MasterLocation::where('active', 'Yes')
            ->where('site_id', $site_id)
            ->whereNotNull('location_aisle')
            ->get();
        $locations = DB::table("iv_stocktake_detail as a")
            ->select("a.location_code")
            ->whereIn("a.location_code", $masterLoc->pluck('location_code'))
            // ->where("a.scan_flag", "Yes")
            ->where("a.branch_id", $branch->first()->id)
            // ->whereDate("a.confirmed_date", ">=", $start)
            ->whereDate("a.confirmed_date", "<=", $end)
            ->whereIn("a.principal_id", $principal_list)
            ->groupBy("a.location_code")
            ->get();

        $scannedCodes = DB::table("iv_stocktake_detail as a")
            ->select("a.location_code", "a.scan_flag", "a.variance")
            ->where("a.scan_flag", "Yes")
            ->where("a.branch_id", $branch->first()->id)
            ->whereDate("a.confirmed_date", ">=", $start)
            ->whereDate("a.confirmed_date", "<=", $end)
            ->whereIn("a.principal_id", $principal_list)
            ->get()
            ->mapWithKeys(function ($row) {
                return [trim($row->location_code) => [
                    'scan_flag' => $row->scan_flag,
                    'variance' => $row->variance
                ]];
            });

        return response()->json([
            'message' => 'success',
            'data' => $locations,
            'scanned_codes' => $scannedCodes,
        ]);
    }

    public function create($id = "")
    {
        $user_id = Auth::user()->id;

        $job_view = DB::table("iv_stocktake_job as a")
            ->select(
                "a.*"
            )
            ->join("users_principal as b", "a.principal_id", "b.principal_id")
            ->where("b.user_id", $user_id)
            ->where("a.id", $id)->first();


        $data = ["job_view" => $job_view];

        return view("transaction.stocktake.create", $data);
    }

    public function getBlok($principal_id)
    {
        $data = DB::table('iv_stock_ledger')
            ->select(DB::raw("SUBSTRING_INDEX(location_code, '-', 1) AS block"))
            ->where('qtys', '>', 0)
            ->where('principal_id', $principal_id)
            ->groupBy('block')
            ->orderBy('block', 'asc')
            ->get();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ]);
    }


    public function scan()
    {
        return view("transaction.stocktake.scan_sto");
    }

    public function doScan(Request $request)
    {
        try {
            $hashids = new Hashids(config('app.key'), 8);
            $encoded = $request->input('data');

            $decoded = $hashids->decode($encoded);

            if (empty($decoded)) {
                throw new \Exception("Invalid or corrupted QR code.");
            }

            $id = $decoded[0];

            $item = StockTakeDetail::find($id);
            DB::table('iv_stocktake_detail')
                ->where('id', $id)
                ->update([
                    'scan_flag' => 'Yes'
                ]);

            if (!$item) {
                throw new \Exception("Data not found.");
            }

            return response()->json([
                'message' => 'Scan success',
                'data' => [
                    'variance' => $item->variance,
                    'id' => $item->id,
                    'product_code' => $item->product_code,
                    'location_code' => $item->location_code,
                    'qty' => $item->qty,
                    'soa' => $item->soa ?? 0,
                    'sob' => $item->sob ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function variance(Request $request)
    {
        try {
            DB::table('iv_stocktake_detail')
                ->where('id', $request->id)
                ->update([
                    'variance' => 'Yes'
                ]);
            return response()->json([
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    public function store(Request $request)
    {
        $messsages = array(
            "principal_id.required" => "Principal name field is required.",
            "description.required" => "Description field is required.",
            "block.required" => "block field is required.",
        );

        $rules = array(
            "principal_id" => "required",
            "description" => "required",
            "block" => "required",
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            $company_id = Auth::user()->company_id;
            $id = $request->stocktake_id;
            $stocktake_date = \Carbon\Carbon::today();
            $year = $stocktake_date->year;
            $month = $stocktake_date->month;
            $principal_id = $request->principal_id;
            $branch_id = Auth::user()->branch;

            try {
                if (is_null($id) || empty($id)) {
                    $job = StockTakeJob::where("company_id", $company_id)
                        ->whereYear("stocktake_date", $year)
                        ->whereMonth("stocktake_date", $month)->max("stocktake_no");

                    if (is_null($job)) {
                        $increment = 1;
                    } else {
                        $increment = substr($job, 7, 4) + 1;
                    }

                    $stocktake_no = "6" . $year . Str::of($month)->padLeft(2, "0") . Str::of($increment)->padLeft(4, "0");
                } else {
                    $job = StockTakeJob::find($id);

                    $stocktake_no = $job->stocktake_no;
                }
                $stock = DB::table("iv_stock_ledger as a")
                    ->select(
                        'a.*',
                        'b.product_name',
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->where("a.principal_id", $principal_id)
                    ->where("a.qtys", ">", 0)
                    ->whereIn(DB::raw("SUBSTRING_INDEX(a.location_code, '-', 1)"), $request->block)
                    ->distinct()
                    ->get();

                if (!isset($stock) || $stock->count() == 0) {
                    return ["error" => ["Stock not available."]];
                }

                $blocks = $request->block ? implode(",", $request->block) : null;
                $data   =   StockTakeJob::updateOrCreate(
                    ["id" => $id],
                    [
                        "company_id" => $company_id,
                        "branch_id" => $branch_id->first()->id,
                        "principal_id" => $request->principal_id,
                        "stocktake_no" => $stocktake_no,
                        "stocktake_date" => $stocktake_date,
                        "description" => $request->description,
                        "block" => $blocks,
                    ]
                );

                $serial_list = [];
                foreach ($stock as $value) {
                    $detail = [];

                    $detail[] = [
                        "company_id" => $value->company_id,
                        "branch_id" => $branch_id->first()->id,
                        "principal_id" => $value->principal_id,
                        "stocktake_id" => $data->id,
                        "job_no" => $value->job_no,
                        "serial_id" => $value->id,
                        "serial_no" => $value->serial_no,
                        "product_id" => $value->product_id,
                        "product_code" => $value->product_code,
                        "po_number" => $value->po_number,
                        "lot_no" => $value->lot_no,
                        "document_ref" => $value->document_ref,
                        "mfg_date" => $value->mfg_date,
                        "exp_date" => $value->exp_date,
                        "manufactur_id" => $value->manufactur_id,
                        "status_id" => $value->status_id,
                        "site_id" => $value->site_id,
                        "area_id" => $value->area_id,
                        "location_id" => $value->location_id,
                        "location_code" => $value->location_code,
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "buom" => $value->buom,
                        "uppp" => $value->uppp,
                        "muppp" => $value->muppp,
                        "pqty" => $value->pqty,
                        "mqty" => $value->mqty,
                        "bqty" => $value->bqty,
                        "qty" => $value->qtys,
                        "soa" => $value->qtya,
                        "sob" => $value->qtyp,
                        "actual_pqty" => 0,
                        "actual_mqty" => 0,
                        "actual_bqty" => 0,
                        "actual_qty" => 0,
                        "pallet_qty" => $value->pallet_qty,
                        "base_unit" => $value->base_unit,
                        "reference_no" => $stocktake_no,
                        "created_at" => date('Y-m-d H:i:s'),
                    ];

                    StockTakeDetail::insert($detail);

                    $serial_list[] = [
                        "id" => $value->id
                    ];
                }

                StockLedger::whereIn(DB::raw("SUBSTRING_INDEX(location_code, '-', 1)"), $request->block)
                    ->whereIn("id", $serial_list)
                    ->update([
                        "freeze_flag"   => "Yes",
                        "freeze_by"     => Auth::user()->username,
                        "freeze_date"   => date('Y-m-d'),
                        "freeze_reason" => "PRA STO : " . $stocktake_no
                    ]);

                DB::commit();

                $message = ["success" => url("/inventory/stock-take/create/" . $data->id)];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
