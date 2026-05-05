<?php

namespace App\Http\Controllers\Transaction\Adjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Adjustment\Job as AdjustmentJob;

class JobController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $details = DB::table("iv_adjustment_job as a")
                            ->select("a.*", "b.type_name")
                            ->join("iv_adjustment_type as b", "a.type_id", "b.id")
                            ->where("a.company_id", $company_id)
                            ->where("a.branch_id", $request->branch_id)
                            ->whereBetween('a.adjust_date', [$date_from, $date_to])
                            ->where("a.confirmed_flag", $request->confirmed_flag)
                            ->get();

            return datatables()->of($details)
            ->editColumn("adjust_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->adjust_date) );
            })
            ->editColumn("confirmed_flag", function ($data) {
                if ($data->confirmed_flag == "Yes") {
                    $status = "<div class='btn btn-sm btn-success'>Completed</div>";
                } else {
                    $status = "<div class='btn btn-sm btn-danger'>Open</div>";
                }
                return $status;
            })
            ->addColumn("adjust_no", function($data){
                $button = "";
                $button .= "<a href='" . URL("/inventory/stock-adjustment/create/$data->id") . "' class='btn btn-default btn-sm'><i class='far fa-file'></i> " . $data->adjust_no . "</a>";
                return $button;
            })
            ->rawColumns(["confirmed_flag", "adjust_no"])
            ->addIndexColumn()
            ->make(true);
        }

        return view("transaction.adjustment.index");
    }

    public function create($id = "") {
        $job_view = DB::table("iv_adjustment_job as a")
                        ->select("a.*", "b.type_name")
                        ->join("iv_adjustment_type as b", "a.type_id", "b.id")
                        ->where("a.id", $id)
                        ->first();

        $adjustment_type = DB::table("iv_adjustment_type")->where("active", "Yes")->get();

        $data = [
            "job_view" => $job_view,
            "type_list" => $adjustment_type
        ];

        return view("transaction.adjustment.create", $data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            "branch_id.required"=>"Branch name field is required.",
            "type_id.required"=>"Adjustment type field is required.",
            "description.required"=>"Description field is required.",
        );

        $rules = array(
            "branch_id" => "required",
            "type_id" => "required",
            "description" => "required",
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(["error"=>$validator->errors()->all()]);
        }

        $id = $request->adjust_id;
        $company_id = Auth::user()->company_id;
        $entry_date = \Carbon\Carbon::now();
        $adjust_date = \Carbon\Carbon::today();

        $year = $adjust_date->year;
        $month = $adjust_date->month;

        $job = AdjustmentJob::where("company_id", $company_id)
                         ->whereYear("adjust_date", $year)
                         ->whereMonth("adjust_date", $month)->max("adjust_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $adjust_no = "4" . $year . Str::of($month)->padLeft(2, "0") . Str::of($increment)->padLeft(4, "0");

        $data   =   AdjustmentJob::updateOrCreate(["id" => $id],
                    [
                        "company_id"=>$company_id,
                        "adjust_no" => $adjust_no,
                        "adjust_date" => $adjust_date,
                        "branch_id" => $request->branch_id,
                        "type_id" => $request->type_id,
                        "description" => $request->description,
                        "entry_date" => $entry_date
                    ]);

        return response()->json(["success"=>url("/inventory/stock-adjustment/create/" . $data->id)]);
    }
}
