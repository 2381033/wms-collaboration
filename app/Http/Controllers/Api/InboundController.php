<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Inbound\Job as InboundJob;
use App\Models\Transaction\Inbound\Detail as InboundDetail;
use App\Models\Transaction\Inbound\Batch as InboundBatch;
use App\Models\Master\Location as MasterLocation;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Stock\Transaction as StockTransaction;

class InboundController extends Controller
{
    public function index($user_id)
    {
        $job_list = DB::table("iv_inbound_job as a")
            ->select("a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "a.description", "a.eta")
            ->join("users_principal as b", "a.principal_id", "b.principal_id")
            ->join("iv_principal as c", "a.principal_id", "c.id")
            ->join("iv_job_class as d", "a.class_id", "d.id")
            ->join("iv_mode as e", "a.mode_id", "e.id")
            ->where("a.class_id", "<>", "3")
            ->where("b.user_id", $user_id)
            ->orderBy("a.job_no", "desc")
            ->where("a.received_flag", "Yes")
            ->where("a.allocated_flag", "Yes")
            ->where("a.confirmed_flag", "No")
            ->get();

        $list = array();

        foreach ($job_list as $value) {
            $list[] = [
                "id" => $value->id,
                "principal_name" => $value->principal_name,
                "job_no" => $value->job_no,
                "job_date" => \Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
                "class_name" => $value->class_name,
                "mode_name" => $value->mode_name,
                "description" => $value->description,
                "eta" => \Carbon\Carbon::parse($value->eta)->format('d/m/Y')
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

    public function search($user_id, $param = '')
    {
        $job_list = DB::table("iv_inbound_job as a")
            ->select("a.id", "c.principal_name", "d.class_name", "e.mode_name", "a.job_no", "a.job_date", "description", "reference_no", "reference_other", "eta", "remarks")
            ->join("users_principal as b", "a.principal_id", "b.principal_id")
            ->join("iv_principal as c", "a.principal_id", "c.id")
            ->join("iv_job_class as d", "a.class_id", "d.id")
            ->join("iv_mode as e", "a.mode_id", "e.id")
            ->where("a.class_id", "<>", "3")
            ->where("b.user_id", $user_id)
            ->where('a.job_no', 'like', "%{$param}%")
            ->where("a.received_flag", "Yes")
            ->where("a.allocated_flag", "Yes")
            ->where("a.confirmed_flag", "No")
            ->orderBy("a.job_no", "desc")
            ->get();

        $list = array();

        foreach ($job_list as $value) {
            $list[] = [
                "id" => $value->id,
                "principal_name" => $value->principal_name,
                "class_name" => $value->class_name,
                "mode_name" => $value->mode_name,
                "job_no" => $value->job_no,
                "job_date" => \Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
                "description" => $value->description,
                "reference_no" => $value->reference_no,
                "reference_other" => $value->reference_other == null ? "" : $value->reference_other,
                "eta" => \Carbon\Carbon::parse($value->eta)->format('d/m/Y'),
                "remarks" => $value->remarks == null ? "" : $value->remarks
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
            "principal_name" => $job_list->principal_name,
            "job_no" => $job_list->job_no,
            "job_date" => \Carbon\Carbon::parse($job_list->job_date)->format('d/m/Y'),
            "class_name" => $job_list->class_name,
            "mode_name" => $job_list->mode_name,
            "description" => $job_list->description,
            "eta" => \Carbon\Carbon::parse($job_list->eta)->format('d/m/Y')
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
                "size_name" => $value->size_name,
                "type_name" => $value->type_name,
                "transporter_name" => $value->transporter_name,
                "driver_name" => $value->driver_name,
                "container_no" => $value->container_no == null ? "" : $value->container_no,
                "seal_no" => $value->seal_no == null ? "" : $value->seal_no,
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

        $detail = DB::table("iv_inbound_batch as a")
            ->select(
                "a.*",
                "b.product_name",
                "c.manufactur_name",
                "d.site_name",
                "e.area_name"
            )
            ->join("iv_product as b", "a.product_id", "b.id")
            ->leftjoin("iv_manufactur as c", "a.manufactur_id", "c.id")
            ->join("iv_site as d", "a.site_id", "d.id")
            ->leftjoin("iv_site_area as e", "a.area_id", "e.id")
            ->where("a.inbound_id", $inbound_id)
            ->where("a.vehicle_no", $vehicle_no)
            ->where("a.confirmed_flag", "No")
            ->get();

        $list = array();

        foreach ($detail as $value) {
            $list[] = [
                "job_id" => $value->inbound_id,
                "id" => $value->id,
                "serial_no" => $value->serial_no,
                "vehicle_no" => $value->vehicle_no,
                "product_code" => $value->product_code,
                "product_name" => $value->product_name,
                "lot_no" => $value->lot_no == null ? "" : $value->lot_no,
                "document_ref" => $value->document_ref == null ? "" : $value->document_ref,
                "mfg_date" => $value->mfg_date == null ? "" : \Carbon\Carbon::parse($value->mfg_date)->format('d/m/Y'),
                "exp_date" => $value->exp_date == null ? "" : \Carbon\Carbon::parse($value->exp_date)->format('d/m/Y'),
                "manufactur_name" => $value->manufactur_name == null ? "" : $value->manufactur_name,
                "site_name" => $value->site_name,
                "area_name" => $value->area_name,
                "location_code" => $value->location_code,
                "pqty" => $value->pqty . " " . $value->puom,
                "mqty" => $value->mqty . " " . $value->muom,
                "bqty" => $value->bqty . " " . $value->buom,
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

    public function submit(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $user = \App\User::find($request->user_id);
            $confirmed_by = $user->username;
            $confirmed_date = \Carbon\Carbon::now();
            $job_date = \Carbon\Carbon::today();

            try {
                $batchin_count = InboundBatch::where("inbound_id", $request->inbound_id)->where("serial_no", $request->serial_no)->count();

                if ($batchin_count == 0) {
                    DB::rollback();
                    $response["error"] = "true";
                    $response["message"] = "Data tidak ditemukan.";

                    return $response;
                }

                $batch = InboundBatch::where("inbound_id", $request->inbound_id)->where("serial_no", $request->serial_no)->first();

                if ($batch->confirmed_flag !== "No") {
                    DB::rollback();
                    $response["error"] = "true";
                    $response["message"] = "Item sudah tersimpan ke dalam lokasi.";

                    return $response;
                }

                $job = InboundJob::find($batch->inbound_id);


                $site = DB::table("iv_site as a")
                    ->select("a.*", "b.type_name")
                    ->join("iv_site_type as b", "a.type_id", "b.id")
                    ->join('users_site as c', 'a.id', 'c.site_id')
                    ->where('c.user_id', $request->user_id)
                    ->where("a.id", $batch->site_id)
                    ->first();

                if ($site->type_name == "Bulk") {
                } else {
                    if ($request->location_code !== $batch->location_code) {
                        DB::rollback();
                        $response["error"] = "true";
                        $response["message"] = "Lokasi tidak sesuai.";

                        return $response;
                    }

                    $location = MasterLocation::find($batch->location_id);

                    if (isset($location) && $location->status_code == "R") {
                        $location->status_code = 'F';
                        $location->save();
                    }
                }

                $stock_ledger = [];
                $stock_ledger[] = [
                    'company_id' => $batch->company_id,
                    'principal_id' => $batch->principal_id,
                    'serial_no' => $batch->serial_no,
                    'srno' => $batch->serial_no,
                    'line_no' => $batch->id,
                    'job_no' => $batch->job_no,
                    'job_date' => $job_date,
                    'vehicle_no' => $batch->vehicle_no,
                    'product_id' => $batch->product_id,
                    'product_code' => $batch->product_code,
                    'po_number' => $batch->po_number,
                    'lot_no' => $batch->lot_no,
                    'document_ref' => $batch->document_ref,
                    'mfg_date' => $batch->mfg_date,
                    'exp_date' => $batch->exp_date,
                    'manufactur_id' => $batch->manufactur_id,
                    'status_id' => $batch->status_id,
                    'site_id' => $batch->site_id,
                    'area_id' => $batch->area_id,
                    'location_id' => $batch->location_id,
                    'location_code' => $batch->location_code,
                    'puom' => $batch->puom,
                    'muom' => $batch->muom,
                    'buom' => $batch->buom,
                    'uppp' => $batch->uppp,
                    'muppp' => $batch->muppp,
                    'pqty' => $batch->pqty,
                    'mqty' => $batch->mqty,
                    'bqty' => $batch->bqty,
                    'qtyr' => $batch->qty,
                    'qtys' => $batch->qty,
                    'qtya' => $batch->qty,
                    'qtyp' => 0,
                    'pallet_qty' => $batch->pallet_qty,
                    'base_unit' => $batch->base_unit,
                    'reference_no' => $batch->job_no
                ];

                $transaction = [];
                $transaction[] = [
                    'company_id' => $batch->company_id,
                    'principal_id' => $batch->principal_id,
                    'serial_no' => $batch->serial_no,
                    'srno' => $batch->serial_no,
                    'line_no' => $batch->id,
                    'job_no' => $batch->job_no,
                    'job_date' => $job_date,
                    'job_type' => 'IMP',
                    'product_id' => $batch->product_id,
                    'product_code' => $batch->product_code,
                    'po_number' => $batch->po_number,
                    'lot_no' => $batch->lot_no,
                    'document_ref' => $batch->document_ref,
                    'mfg_date' => $batch->mfg_date,
                    'exp_date' => $batch->exp_date,
                    'manufactur_id' => $batch->manufactur_id,
                    'status_id' => $batch->status_id,
                    'site_id' => $batch->site_id,
                    'area_id' => $batch->area_id,
                    'location_id' => $batch->location_id,
                    'location_code' => $batch->location_code,
                    'puom' => $batch->puom,
                    'muom' => $batch->muom,
                    'buom' => $batch->buom,
                    'uppp' => $batch->uppp,
                    'muppp' => $batch->muppp,
                    'pqty' => $batch->pqty,
                    'mqty' => $batch->mqty,
                    'bqty' => $batch->bqty,
                    'qty' => $batch->qty,
                    'base_unit' => $batch->base_unit,
                    'reference_no' => $batch->job_no
                ];

                StockLedger::insert($stock_ledger);
                StockTransaction::insert($transaction);

                $inboundDetail = InboundDetail::find($batch->packing_id);

                $inboundDetail->confirmed_flag = 'Yes';
                $inboundDetail->confirmed_by = $confirmed_by;
                $inboundDetail->confirmed_date = $confirmed_date;
                $inboundDetail->save();

                $batch->confirmed_flag = 'Yes';
                $batch->confirmed_by = $confirmed_by;
                $batch->confirmed_date = $confirmed_date;
                $batch->save();

                $detail = InboundDetail::where('inbound_id', $batch->inbound_id)->where('confirmed_flag', 'No')->get();

                if ($detail->count() == 0) {
                    $batchin = InboundBatch::where('inbound_id', $batch->inbound_id)->where('confirmed_flag', 'No')->get();

                    if ($batchin->count() == 0) {
                        $job = InboundJob::find($batch->inbound_id);

                        $job->confirmed_flag = 'Yes';
                        $job->confirmed_by = $confirmed_by;
                        $job->confirmed_date = $confirmed_date;
                        $job->save();
                    }
                }

                DB::commit();

                $response["error"] = "false";
                $response["message"] = "Tidak ada data yang akan diterima.";

                return $response;
            } catch (\Exception $e) {
                DB::rollBack();

                $response["error"] = "true";
                $response["message"] = $e->getMessage();

                return $response;
            }
        });

        return response()->json($exception);
    }
}
