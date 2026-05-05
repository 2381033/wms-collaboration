<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Inbound\Job as InboundJob;
use App\Models\Transaction\Inbound\Vehicle as InboundVehicle;
use App\Models\Transaction\Inbound\Detail as InboundDetail;
use App\Models\Master\Product as MasterProduct;

class GrnController extends Controller
{
    public function index($user_id)
    {
        $job_list = DB::table("iv_inbound_job as a")
            ->select("a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "description", "eta")
            ->join("users_principal as b", "a.principal_id", "b.principal_id")
            ->join("iv_principal as c", "a.principal_id", "c.id")
            ->join("iv_job_class as d", "a.class_id", "d.id")
            ->join("iv_mode as e", "a.mode_id", "e.id")
            ->where("b.user_id", $user_id)
            ->orderBy("a.job_no", "desc")
            ->where("a.received_flag", "No")
            ->get();

        $list = array();

        foreach ($job_list as $value) {
            $list[] = [
                "id" => $value->id,
                "principal_name" => "Principal Name : " . $value->principal_name,
                "job_no" => "Job No : " . $value->job_no,
                "job_date" => "Job Date : " . \Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
                "class_name" => "Job Class : " . $value->class_name,
                "mode_name" => "Moda Name : " . $value->mode_name,
                "description" => "Description : " . $value->description,
                "eta" => "ETA : " . \Carbon\Carbon::parse($value->eta)->format('d/m/Y')
            ];
        }

        $response = [];

        if (count($list) == 0) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "";
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }

    public function search($user_id, $param)
    {
        $search = $param == null ? '%' : '%' . $param . '%';

        $job_list = DB::table("iv_inbound_job as a")
            ->select("a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "description", "eta")
            ->join("users_principal as b", "a.principal_id", "b.principal_id")
            ->join("iv_principal as c", "a.principal_id", "c.id")
            ->join("iv_job_class as d", "a.class_id", "d.id")
            ->join("iv_mode as e", "a.mode_id", "e.id")
            ->where("b.user_id", $user_id)
            ->where("a.received_flag", "No")
            ->where('a.job_no', 'like', "{$search}")
            ->orderBy("a.job_no", "desc")
            ->get();

        $list = array();

        foreach ($job_list as $value) {
            $list[] = [
                "id" => $value->id,
                "principal_name" => "Principal Name : " . $value->principal_name,
                "job_no" => "Job No : " . $value->job_no,
                "job_date" => "Job Date : " . \Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
                "class_name" => "Job Class : " . $value->class_name,
                "mode_name" => "Moda Name : " . $value->mode_name,
                "description" => "Description : " . $value->description,
                "eta" => "ETA : " . \Carbon\Carbon::parse($value->eta)->format('d/m/Y')
            ];
        }

        $response = [];

        if (count($list) == 0) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "";
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }

    public function vehicle($inbound_id)
    {
        $job_list = DB::table("iv_inbound_job as a")
            ->select(
                "a.*",
                "b.principal_name",
                "c.class_name",
                "d.mode_name"
            )
            ->join("iv_principal as b", "a.principal_id", "b.id")
            ->join("iv_job_class as c", "a.class_id", "c.id")
            ->join("iv_mode as d", "a.mode_id", "d.id")
            ->where("a.id", $inbound_id)
            ->first();

        $job[] = [
            "id" => $job_list->id,
            "principal_name" => "Principal Name : " . $job_list->principal_name,
            "job_no" => "Job No : " . $job_list->job_no,
            "job_date" => "Job Date : " . \Carbon\Carbon::parse($job_list->job_date)->format('d/m/Y'),
            "class_name" => "Job Class : " . $job_list->class_name,
            "mode_name" => "Moda Name : " . $job_list->mode_name,
            "description" => "Description : " . $job_list->description,
            "eta" => "ETA : " . \Carbon\Carbon::parse($job_list->eta)->format('d/m/Y')
        ];

        $detail = DB::table("iv_inbound_vehicle as a")
            ->select(
                "a.*",
                "b.size_name",
                "c.type_name"
            )
            ->join("iv_container_size as b", "a.size_id", "b.id")
            ->leftjoin("iv_container_type as c", "a.type_id", "c.id")
            ->where("a.inbound_id", $inbound_id)
            ->get();

        $list = array();

        foreach ($detail as $value) {
            $list[] = [
                "id" => $value->id,
                "inbound_id" => $value->inbound_id,
                "vehicle_no" => $value->vehicle_no,
                "size_name" => "Container Size : " . $value->size_name,
                "type_name" => "Container Type : " . $value->type_name,
                "transporter_name" => "Transporter Name : " . $value->transporter_name,
                "driver_name" => "Driver Name : " . $value->driver_name,
                "container_no" => $value->container_no == null ? "Container No : " : "Container No : " . $value->container_no,
                "seal_no" => $value->seal_no == null ? "Seal No : " : "Seal No : " . $value->seal_no,
                "awb_no" => $value->awb_no == null ? "" : $value->awb_no,
                "ata" => $value->ata == null ? "" : \Carbon\Carbon::parse($value->ata)->format('d/m/Y H:i'),
                "unloading_start" => $value->unloading_start == null ? "" : \Carbon\Carbon::parse($value->unloading_start)->format('d/m/Y H:i'),
                "unloading_finish" => $value->unloading_finish == null ? "" : \Carbon\Carbon::parse($value->unloading_finish)->format('d/m/Y H:i')
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
            $response["product"] = $list;
        }

        return response()->json($response, 200);
    }

    public function start(Request $request)
    {
        $job = InboundJob::find($request->inbound_id);

        $vehicle = InboundVehicle::where("inbound_id", $request->inbound_id)->where("vehicle_no", $request->vehicle_no)->first();

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->ata);
        $ata = \Carbon\Carbon::parse($dateObject)->format("Y-m-d H:i");

        if (empty($job->unloading_start)) {
            $job->ata = $ata;
            $job->unloading_start = \Carbon\Carbon::now();
            $job->save();
        }

        if (empty($vehicle->unloading_start)) {
            $vehicle->ata = $ata;
            $vehicle->unloading_start = \Carbon\Carbon::now();
            $vehicle->save();

            $response["error"] = "false";
            $response["message"] = "";
        } else {
            $response["error"] = "true";
            $response["message"] = "Please finish the process unloading!!!";
        }

        return json_encode($response);
    }

    public function finish(Request $request)
    {
        $job = InboundJob::find($request->inbound_id);

        $vehicle = InboundVehicle::where("inbound_id", $request->inbound_id)->where("vehicle_no", $request->vehicle_no)->first();

        if (empty($vehicle->unloading_finish)) {
            $vehicle->unloading_finish = \Carbon\Carbon::now();
            $vehicle->save();

            $response["error"] = "false";
            $response["message"] = "";
        } else {
            $response["error"] = "true";
            $response["message"] = "Please finish the process unloading!!!";
        }

        $vehicle_count = InboundVehicle::where("inbound_id", $request->inbound_id)->where("unloading_finish", null)->count();

        if ($vehicle_count == 0) {
            $job->unloading_finish = \Carbon\Carbon::now();
            $job->save();
        }

        return json_encode($response);
    }

    public function detail($inbound_id, $vehicle_no)
    {
        $job_list = DB::table("iv_inbound_job as a")
            ->select(
                "a.*",
                "b.principal_name",
                "c.class_name",
                "d.mode_name"
            )
            ->join("iv_principal as b", "a.principal_id", "b.id")
            ->join("iv_job_class as c", "a.class_id", "c.id")
            ->join("iv_mode as d", "a.mode_id", "d.id")
            ->where("a.id", $inbound_id)
            ->first();

        $job[] = [
            "id" => $job_list->id,
            "principal_name" => $job_list->principal_name,
            "job_no" => $job_list->job_no,
            "job_date" => \Carbon\Carbon::parse($job_list->job_date)->format('d/m/Y'),
            "class_name" => $job_list->class_name,
            "mode_name" => $job_list->mode_name,
            "description" => $job_list->description,
            "eta" => \Carbon\Carbon::parse($job_list->eta)->format('d/m/Y')
        ];

        $detail = DB::table("iv_inbound_detail as a")
            ->select(
                "a.*",
                "b.product_name",
                "c.manufactur_name"
            )
            ->join("iv_product as b", "a.product_id", "b.id")
            ->leftjoin("iv_manufactur as c", "a.manufactur_id", "c.id")
            ->where("a.inbound_id", $inbound_id)
            ->where("a.vehicle_no", $vehicle_no)
            ->where("a.received_flag", "No")
            ->get();

        $list = array();

        foreach ($detail as $value) {
            $qty = $value->qty;

            $pqty = ($qty - ($qty % $value->uppp)) / $value->uppp;
            $mqty = (($qty % $value->uppp) - ($qty % $value->uppp % $value->muppp)) / $value->muppp;
            $bqty = $qty % $value->uppp % $value->muppp;

            $list[] = [
                "id" => $value->id,
                "inbound_id" => $value->inbound_id,
                "vehicle_no" => "Vehicle No : " . $value->vehicle_no,
                "product_code" => "SKU No : " . $value->product_code,
                "product_name" => "SKU Name : " . $value->product_name,
                "lot_no" => $value->lot_no == null ? "Batch No : " : "Batch No" . $value->lot_no,
                "document_ref" => $value->document_ref == null ? "" : $value->document_ref,
                "mfg_date" => $value->mfg_date == null ? "" : \Carbon\Carbon::parse($value->mfg_date)->format('d/m/Y'),
                "exp_date" => $value->exp_date == null ? "Exp Date : " : "Exp Date : " . \Carbon\Carbon::parse($value->exp_date)->format('d/m/Y'),
                "manufactur_name" => $value->manufactur_name,
                "pqty" => $pqty . " " . $value->puom,
                "mqty" => $mqty . " " . $value->muom,
                "bqty" => $bqty . " " . $value->buom,
                "actual_pqty" => $value->actual_pqty,
                "actual_mqty" => $value->actual_mqty,
                "actual_bqty" => $value->actual_bqty,
                "damage_pqty" => $value->discrepancy_pqty,
                "damage_mqty" => $value->discrepancy_mqty,
                "damage_bqty" => $value->discrepancy_bqty,
                "puom" => $value->puom,
                "muom" => $value->muom,
                "buom" => $value->buom,
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
            $response["product"] = $list;
        }

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $message = "";
        $response = [];

        $job = InboundJob::find($request->inbound_id);

        if (empty($job->unloading_start) || $job->unloading_start == null || empty($job->unloading_finish) || $job->unloading_finish == null) {
            $response["error"] = "true";
            $response["message"] = "Silakan lakukan proses bongkar armada sampai selesai!!!";

            return json_encode($response);
        }

        $detail = InboundDetail::find($request->detail_id);

        $product = MasterProduct::find($detail->product_id);

        $actual_qty = ($request->actual_pqty * $product->uppp) + ($request->actual_mqty * $product->muppp) + $request->actual_bqty;
        $damage_qty = ($request->damage_pqty * $product->uppp) + ($request->damage_mqty * $product->muppp) + $request->damage_bqty;

        $detail->actual_pqty = $request->actual_pqty;
        $detail->actual_mqty = $request->actual_mqty;
        $detail->actual_bqty = $request->actual_bqty;
        $detail->actual_qty = $actual_qty;
        $detail->discrepancy_pqty = $request->damage_pqty;
        $detail->discrepancy_mqty = $request->damage_mqty;
        $detail->discrepancy_bqty = $request->damage_bqty;
        $detail->discrepancy_qty = $damage_qty;
        $detail->save();

        if ($message == "") {
            $response["error"] = "false";
            $response["message"] = "";
        } else {
            $response["error"] = "true";
            $response["message"] = "";
        }

        return json_encode($response);
    }

    public function submit(Request $request)
    {
        $user = \App\User::find($request->user_id);
        $received_date = \Carbon\Carbon::now();

        try {
            $job = InboundJob::find($request->inbound_id);

            if (empty($job->unloading_start) || $job->unloading_start == null || empty($job->unloading_finish) || $job->unloading_finish == null) {
                $response["error"] = "true";
                $response["message"] = "Silakan lakukan proses bongkar armada sampai selesai!!!";

                return json_encode($response);
            }

            $detail = InboundDetail::find($request->detail_id);

            if ($detail->received_flag == "No") {
                $detail->received_flag = "Yes";
                $detail->received_by = $user->username;
                $detail->received_date = $received_date;
                $detail->save();
            }

            $received = DB::table("iv_inbound_detail")
                ->where("inbound_id", $detail->inbound_id)
                ->where("id", "<>", $request->detail_id)
                ->where("received_flag", "No")
                ->count();

            if ($received == 0) {
                $job->received_flag = "Yes";
                $job->received_by = $user->username;
                $job->received_date = $received_date;
                $job->save();
            }

            $message = ["error" => false, "message" => "Data Successfully Saved"];

            return $message;
        } catch (\Exception $e) {
            $message = ["error" => true, "message" => $e->getMessage()];

            return $message;
        }

        return response()->json($message);
    }
}
