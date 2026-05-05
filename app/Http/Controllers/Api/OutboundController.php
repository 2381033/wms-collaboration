<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Outbound\Batch as OutboundBatch;
use App\Models\Master\Location as MasterLocation;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Stock\Transaction as StockTransaction;
use App\Models\Transaction\Outbound\Order as OutboundOrder;
use App\Models\Transaction\Outbound\Detail as OutboundDetail;
use App\Models\Transaction\Outbound\Job as OutboundJob;

class OutboundController extends Controller
{
    public function index($user_id) {
        $list = DB::table("iv_outbound_job as a")
                    ->select( "a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "description", "reference_no", "reference_other", "etd", "remarks" )
                    ->join("users_principal as b", "a.principal_id", "b.principal_id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_job_class as d", "a.class_id", "d.id")
                    ->join("iv_mode as e", "a.mode_id", "e.id")
                    ->where("a.class_id", "<>", "3")
                    ->where("b.user_id", $user_id)
                    ->where("a.allocated_flag", "Yes")
                    ->where("a.confirmed_flag", "No")
                    ->orderBy("a.job_date", "asc")
                    ->get();

        $response = Array();

        foreach ($list as $value) {
            $response[] = [
                "id"=>$value->id,
                "principal_name"=>"Principal Name : " . $value->principal_name,
                "class_name"=>"Job Class : " .$value->class_name,
                "mode_name"=>"Moda Name : " .$value->mode_name,
                "job_no"=>"Job No : " . $value->job_no,
                "job_date"=>"Job Date : " . \Carbon\Carbon::parse($value->job_date)->format("d/m/Y"),
                "description"=>"Description : " . $value->description,
                "reference_no"=>$value->reference_no,
                "reference_other"=>$value->reference_other == null ? "" : $value->reference_other ,
                "etd"=>\Carbon\Carbon::parse($value->etd)->format("d/m/Y"),
                "remarks"=>$value->remarks == null ? "" : $value->remarks
            ];
        }

        return response()->json(["pesan" => "Berhasil", "job" => $response], 200);
    }

    public function search($user_id, $param) {
        $list = DB::table("iv_outbound_job as a")
                    ->select( "a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "description", "reference_no", "reference_other", "etd", "remarks" )
                    ->join("users_principal as b", "a.principal_id", "b.principal_id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_job_class as d", "a.class_id", "d.id")
                    ->join("iv_mode as e", "a.mode_id", "e.id")
                    ->where("a.class_id", "<>", "3")
                    ->where("b.user_id", $user_id)
                    ->where("a.job_no", "like", "%{$param}%")
                    ->where("a.allocated_flag", "Yes")
                    ->where("a.confirmed_flag", "No")
                    ->orderBy("a.job_date", "asc")
                    ->get();

        if (!isset($list)) {
        return $this->error("Data kosong.");
        }

        $response = Array();

        foreach ($list as $value) {
            $response[] = [
                "id"=>$value->id,
                "principal_name"=>"Principal Name : " . $value->principal_name,
                "class_name"=>"Job Class : " .$value->class_name,
                "mode_name"=>"Moda Name : " .$value->mode_name,
                "job_no"=>"Job No : " . $value->job_no,
                "job_date"=>"Job Date : " . \Carbon\Carbon::parse($value->job_date)->format("d/m/Y"),
                "description"=>"Description : " . $value->description,
                "reference_no"=>$value->reference_no,
                "reference_other"=>$value->reference_other == null ? "" : $value->reference_other ,
                "etd"=>\Carbon\Carbon::parse($value->etd)->format("d/m/Y"),
                "remarks"=>$value->remarks == null ? "" : $value->remarks
            ];
        }

        return response()->json(["pesan" => "Berhasil", "job" => $response], 200);
    }

    public function order ($user_id, $outbound_id) {
        $value = DB::table("iv_outbound_job as a")
                    ->select( "a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "description", "reference_no", "reference_other", "etd", "remarks" )
                    ->join("users_principal as b", "a.principal_id", "b.principal_id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_job_class as d", "a.class_id", "d.id")
                    ->join("iv_mode as e", "a.mode_id", "e.id")
                    ->where("a.class_id", "<>", "3")
                    ->where("b.user_id", $user_id)
                    ->where("a.id", $outbound_id)
                    ->first();

        $order_list = DB::table("iv_outbound_order as a")
                    ->select("a.*", "b.customer_name")
                    ->join("iv_customer as b", "a.customer_id", "b.id")
                    ->where("a.outbound_id", $outbound_id)
                    ->where("a.confirmed_flag", "Yes")
                    ->get();

        $job = Array();
        $order = Array();

        $job[] = [
            "id"=>$value->id,
            "principal_name"=>"Principal Name : " . $value->principal_name,
            "class_name"=>$value->class_name,
            "mode_name"=>$value->mode_name,
            "job_no"=>"Job No : " . $value->job_no,
            "job_date"=>"Job Date : " . \Carbon\Carbon::parse($value->job_date)->format("d/m/Y"),
            "description"=>"Description : " . $value->description,
            "reference_no"=>$value->reference_no,
            "reference_other"=>$value->reference_other == null ? "" : $value->reference_other ,
            "etd"=>\Carbon\Carbon::parse($value->etd)->format("d/m/Y"),
            "remarks"=>$value->remarks == null ? "" : $value->remarks
        ];

        foreach ($order_list as $value) {
            $order[] = [
                "id"=>$value->id,
                "outbound_id"=>$value->outbound_id,
                "customer_name"=>"Customer Name : " . $value->customer_name,
                "order_no"=>"Order No : " . $value->order_no,
                "order_date"=>$value->order_date == null ? "Order Date : " : "Order Date : " . \Carbon\Carbon::parse($value->order_date)->format("d/m/Y"),
                "due_date"=>$value->due_date == null ? "Due Date : " : "Due Date : " .  \Carbon\Carbon::parse($value->due_date)->format("d/m/Y"),
                "po_number"=>$value->po_number == null ? "PO No : " : "PO No : " .  $value->po_number
            ];
        }

        return response()->json(["job" => $job, "product" => $order]);

    }

    public function detail($user_id, $outbound_id, $order_id) {
        $value = DB::table("iv_outbound_job as a")
                    ->select( "a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "description", "reference_no", "reference_other", "etd", "remarks" )
                    ->join("users_principal as b", "a.principal_id", "b.principal_id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("iv_job_class as d", "a.class_id", "d.id")
                    ->join("iv_mode as e", "a.mode_id", "e.id")
                    ->where("a.class_id", "<>", "3")
                    ->where("b.user_id", $user_id)
                    ->where("a.id", $outbound_id)
                    ->first();

        $order_no = OutboundOrder::find($order_id)->order_no;

        $detail = DB::table("iv_outbound_batch as a")
                    ->select("a.outbound_id", "a.id", "a.serial_no", "a.product_code", "b.product_name", "a.lot_no", "a.exp_date", "a.pqty", "a.mqty", "a.bqty", "b.puom", "b.muom", "b.buom", "c.site_name", "d.area_name", "a.location_code")
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->where("a.outbound_id", $outbound_id)
                    ->where("a.order_no", $order_no)
                    ->where("a.confirmed_flag", "No")
                    ->get();

        $job = Array();
        $product = Array();

        $job[] = [
            "id"=>$value->id,
            "principal_name"=>"Principal Name : " . $value->principal_name,
            "class_name"=>$value->class_name,
            "mode_name"=>$value->mode_name,
            "job_no"=>"Job No : " . $value->job_no,
            "job_date"=>"Job Date : " . \Carbon\Carbon::parse($value->job_date)->format("d/m/Y"),
            "description"=>"Description : " . $value->description,
            "reference_no"=>$value->reference_no,
            "reference_other"=>$value->reference_other == null ? "" : $value->reference_other ,
            "etd"=>\Carbon\Carbon::parse($value->etd)->format("d/m/Y"),
            "remarks"=>$value->remarks == null ? "" : $value->remarks
        ];

        foreach ($detail as $value) {
            $product[] = [
                "id"=>$value->id,
                "outbound_id"=>$value->outbound_id,
                "serial_no"=>"SN : $value->serial_no",
                "product_code"=>"SKU No : $value->product_code",
                "product_name"=>"SKU Name : $value->product_name",
                "exp_date"=>$value->exp_date == null ? "Exp Date : " : "Exp Date : " . \Carbon\Carbon::parse($value->exp_date)->format("d/m/Y"),
                "lot_no"=>$value->lot_no == null ? "Batch : " : "Batch : " . $value->lot_no,
                "pqty"=>"1st : " . number_format($value->pqty, 0, ",", ".") . " " . $value->puom,
                "mqty"=>"2nd : " . number_format($value->mqty, 0, ",", ".") . " " . $value->muom,
                "bqty"=>"3rd : " . number_format($value->bqty, 0, ",", ".") . " " . $value->buom,
                "site_name"=>"Site : ". $value->site_name,
                "area_name"=>"Area : ". $value->area_name,
                "location_code"=>"Location : ". $value->location_code
            ];
        }

        return response()->json(["job" => $job, "product" => $product]);
    }

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $user = \App\User::find($request->user_id);
            $confirmed_by = $user->username;
            $confirmed_date = \Carbon\Carbon::now();
            $job_date = \Carbon\Carbon::today();

            try {
                $order_no = OutboundOrder::where("outbound_id", $request->outbound_id)->where("id", $request->order_id)->first()->order_no;

                $batchin_count = OutboundBatch::where("outbound_id", $request->outbound_id)->where("order_no", $order_no)->where("serial_no", $request->serial_no)->count();

                if ( $batchin_count == 0 ) {
                    DB::rollback();
                    $response["error"] = "true";
                    $response["message"] = "Data tidak ditemukan.";

                    return $response;
                }

                $batch = OutboundBatch::where("outbound_id", $request->outbound_id)->where("order_no", $order_no)->where("serial_no", $request->serial_no)->first();

                if ( $batch->confirmed_flag !== "No" ) {
                    DB::rollback();
                    $response["error"] = "true";
                    $response["message"] = "Item sudah keluar dari dalam lokasi.";

                    return $response;
                }

                $site = DB::table("iv_site as a")
                            ->select("a.*", "b.type_name")
                            ->join("iv_site_type as b", "a.type_id", "b.id")
                            ->join('users_site as c', 'a.id', 'c.site_id')
                            ->where('c.user_id', $request->user_id)
                            ->where("a.id", $batch->site_id)
                            ->first();

                if ( $site->type_name == "Bulk" ) {
                } else {
                    if ( Str::lower($request->location_code) !== Str::lower($batch->location_code) ) {
                        DB::rollback();
                        $response["error"] = "true";
                        $response["message"] = "Lokasi tidak sesuai.";

                        return $response;
                    }

                    $location = MasterLocation::find($batch->location_id);

                }

                $detail = OutboundDetail::find($batch->picking_id);
                $serial = StockLedger::find($batch->serial_id);

                $serial->qtys = $serial->qtys - $batch->qty;
                $serial->qtyp = $serial->qtyp - $batch->qty;
                $serial->save();

                if ($serial->qtys == 0) {
                    $location->status_code = "E";
                    $location->save();
                }

                $transaction = [];

                $transaction[] = [
                    "company_id" => $batch->company_id,
                    "principal_id" => $batch->principal_id,
                    "serial_no" => $batch->serial_no,
                    "srno" => $batch->serial_no,
                    "line_no" => $batch->id,
                    "job_no" => $serial->job_no,
                    "job_date" => $job_date,
                    "job_type" => "EXP",
                    "product_id" => $batch->product_id,
                    "product_code" => $batch->product_code,
                    "po_number" => $batch->po_number,
                    "lot_no" => $batch->lot_no,
                    "document_ref" => $batch->document_ref,
                    "mfg_date" => $batch->mfg_date,
                    "exp_date" => $batch->exp_date,
                    "manufactur_id" => $serial->manufactur_id,
                    "status_id" => $serial->status_id,
                    "site_id" => $batch->site_id,
                    "area_id" => $batch->area_id,
                    "location_id" => $batch->location_id,
                    "location_code" => $batch->location_code,
                    "puom" => $batch->puom,
                    "muom" => $batch->muom,
                    "buom" => $batch->buom,
                    "uppp" => $batch->uppp,
                    "muppp" => $batch->muppp,
                    "pqty" => $batch->pqty,
                    "mqty" => $batch->mqty,
                    "bqty" => $batch->bqty,
                    "qty" => $batch->qty,
                    "base_unit" => $batch->base_unit,
                    "reference_no" => $batch->job_no
                ];

                StockTransaction::insert($transaction);

                $detail->confirmed_flag = "Yes";
                $detail->confirmed_by = $confirmed_by;
                $detail->confirmed_date = $confirmed_date;
                $detail->save();

                $batch->confirmed_flag = "Yes";
                $batch->confirmed_by = $confirmed_by;
                $batch->confirmed_date = $confirmed_date;
                $batch->save();

                $job = OutboundJob::find($request->outbound_id);

                $batch_count = OutboundBatch::where("id", $request->outbound_id)
                            ->where("confirmed_flag", "No")
                            ->get();

                if (is_null($batch_count)) {
                    $count = 0;
                } else {
                    $count = $batch_count->count();
                }

                if ($count == 0) {
                    $job->confirmed_flag = "Yes";
                    $job->confirmed_by = $confirmed_by;
                    $job->confirmed_date = $confirmed_date;
                    $job->save();
                }

                DB::commit();

                $message = ["error"=>false, "message"=>"Data successfully proccessed."];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ["error"=>true, "message"=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
