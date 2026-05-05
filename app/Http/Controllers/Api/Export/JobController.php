<?php

namespace App\Http\Controllers\Api\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function userChecking($id)
    {
        $user = \App\User::find($id)->username;

        $count = DB::table("ex_outbound_header as a")
            ->join("sm_user_branch as b", "a.branch_id", "b.branch_id")
            ->where("b.user_id", $id)
            ->where("a.user_process", $user)
            ->where("a.status_flag", "Open")
            ->count();

        $response = [];

        if ($count > 0) {
            $job = DB::table("ex_outbound_header as a")
                ->join("sm_user_branch as b", "a.branch_id", "b.branch_id")
                ->where("b.user_id", $id)
                ->where("a.user_process", $user)
                ->where("a.status_flag", "Open")
                ->first();

            $response["error"] = TRUE;
            $response["message"] = $job->id;
        } else {
            $response["error"] = FALSE;
            $response["message"] = "";
        }

        return response()->json($response, 200);
    }
    
    public function index($user_id, $param = "")
    {
        $search = $param == null ? '' : '%' . $param . '%';

        $list = DB::table("ex_outbound_header as a")
            ->select("a.*", "b.forwarder_name")
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("sm_user_branch as c", "a.branch_id", "c.branch_id")
            ->where(function ($query) use ($search) {
                $query->where("a.container_no", "LIKE", $search);
            })
            ->where("a.status_flag", "Open")
            ->where("c.user_id", $user_id)
            ->get();

        $job = array();

        foreach ($list as $value) {
            $job[] = [
                "id" => $value->id,
                "job_no" => $value->job_no,
                "job_date" => \Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
                "forwarder_name" => $value->forwarder_name,
                "container_no" => $value->container_no,
                "surveyor_name" => $value->surveyor_name == "" ? "" : $value->surveyor_name,
                "destination" => $value->destination,
                "qty_cargo" => $value->qty_cargo,
                "cbm" => $value->cbm,
                "weight" => $value->weight,
                "total_pallet" => $value->total_pallet,

            ];
        }
        $response = [];

        if (count($list) == 0) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "";
            $response["job"] = $job;
        }

        return response()->json($response, 200);
    }
}
