<?php

namespace App\Http\Controllers\Api\CY;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Transaction\CY\ChecklistDetail as CYChecklistDetail;
use App\Models\Transaction\CY\ChecklistHeader as CYChecklistHeader;

class CheckListController extends Controller
{
    public function inboundList($user_id) {
        $date_from = \Carbon\Carbon::today()->addDay(-1);
        $date_to = \Carbon\Carbon::today()->addDay(1);

        $list_data = DB::table("cy_checklist_header as a")
                        ->select("a.*", "b.forwarder_name", "c.size_name", "d.type_name")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->join("iv_container_size as c", "a.size_id", "c.id")
                        ->join("iv_container_type as d", "a.type_id", "d.id")
                        ->join("sm_user_branch as e", "a.branch_id", "e.branch_id")
                        ->where("e.user_id", $user_id)
                        ->where("a.job_type", "Inbound")
                        ->whereBetween("a.job_date", [$date_from, $date_to])
                        ->where("a.confirmed_flag", "No")
                        ->orderBy("a.job_Date", "ASC")
                        ->get();

        $list = Array();

        foreach ($list_data as $value) {
            $list[] = [
                "id"=>$value->id,
                "company_name"=>$value->forwarder_name,
                "job_no"=>$value->job_no,
                "job_date"=>\Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
                "vehicle_no"=>$value->vehicle_no,
                "driver_name"=>$value->driver_name,
                "container_no"=>$value->container_no,
                "container_status"=>$value->container_status,
                "size_name"=>$value->size_name,
                "type_name"=>$value->type_name,
                "sign_operation_name"=>$value->sign_operation_name == null ? "" : $value->sign_operation_name,
                "sign_operation_path"=>$value->sign_operation_path == null ? "" : $value->sign_operation_path,
                "sign_driver_name"=>$value->sign_driver_name == null ? "" : $value->sign_driver_name,
                "sign_driver_path"=>$value->sign_driver_path == null ? "" : $value->sign_driver_path,
                "sign_security_name"=>$value->sign_security_name == null ? "" : $value->sign_security_name,
                "sign_security_path"=>$value->sign_security_path == null ? "" : $value->sign_security_path,
            ];
        }
        $response = [];

        if ( count($list) == 0 ) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "";
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }

    public function outboundList($user_id) {
        $date_from = \Carbon\Carbon::today()->addDay(-1);
        $date_to = \Carbon\Carbon::today()->addDay(1);

        $list_data = DB::table("cy_checklist_header as a")
                        ->select("a.*", "b.forwarder_name", "c.size_name", "d.type_name")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->join("iv_container_size as c", "a.size_id", "c.id")
                        ->join("iv_container_type as d", "a.type_id", "d.id")
                        ->join("sm_user_branch as e", "a.branch_id", "e.branch_id")
                        ->where("e.user_id", $user_id)
                        ->where("a.job_type", "Outbound")
                        ->whereBetween("a.job_date", [$date_from, $date_to])
                        ->where("a.confirmed_flag", "No")
                        ->orderBy("a.job_Date", "ASC")
                        ->get();

        $list = Array();

        foreach ($list_data as $value) {
            $list[] = [
                "id"=>$value->id,
                "company_name"=>$value->forwarder_name,
                "job_no"=>$value->job_no,
                "job_date"=>\Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
                "vehicle_no"=>$value->vehicle_no,
                "driver_name"=>$value->driver_name,
                "container_no"=>$value->container_no,
                "container_status"=>$value->container_status,
                "size_name"=>$value->size_name,
                "type_name"=>$value->type_name,
                "sign_operation_name"=>$value->sign_operation_name == null ? "" : $value->sign_operation_name,
                "sign_operation_path"=>$value->sign_operation_path == null ? "" : $value->sign_operation_path,
                "sign_driver_name"=>$value->sign_driver_name == null ? "" : $value->sign_driver_name,
                "sign_driver_path"=>$value->sign_driver_path == null ? "" : $value->sign_driver_path,
                "sign_security_name"=>$value->sign_security_name == null ? "" : $value->sign_security_name,
                "sign_security_path"=>$value->sign_security_path == null ? "" : $value->sign_security_path,
            ];
        }
        $response = [];

        if ( count($list) == 0 ) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "";
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }

    public function checkListView($id) {
        $header_first = DB::table("cy_checklist_header as a")
                        ->select("a.*", "b.forwarder_name", "c.size_name", "d.type_name")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->join("iv_container_size as c", "a.size_id", "c.id")
                        ->join("iv_container_type as d", "a.type_id", "d.id")
                        ->where("a.id", $id)
                        ->first();

        $detail = DB::table("cy_checklist_detail as a")
                        ->select(
                            "a.*",
                            "b.check_name"
                        )
                        ->join("cy_checklist as b", "a.check_id", "b.id")
                        ->where("a.checklist_id", $id)
                        ->get();

        $header[] = [
            "id"=>$header_first->id,
            "company_name"=>$header_first->forwarder_name,
            "job_no"=>"Job No : " . $header_first->job_no,
            "job_date"=>"Job Date : " . \Carbon\Carbon::parse($header_first->job_date)->format('d/m/Y'),
            "vehicle_no"=>"Vehicle No : " . $header_first->vehicle_no,
            "driver_name"=>"Driver Name : " . $header_first->driver_name,
            "container_no"=>"Cont. No : " . $header_first->container_no,
            "container_status"=>"Cont. Status : " . $header_first->container_status,
            "size_name"=>"Cont. Size : " . $header_first->size_name,
            "type_name"=>"Cont. Type : " . $header_first->type_name,
            "sign_operation_name"=>$header_first->sign_operation_name == null ? "" : $header_first->sign_operation_name,
            "sign_operation_path"=>$header_first->sign_operation_path == null ? "" : $header_first->sign_operation_path,
            "sign_driver_name"=>$header_first->sign_driver_name == null ? "" : $header_first->sign_driver_name,
            "sign_driver_path"=>$header_first->sign_driver_path == null ? "" : $header_first->sign_driver_path,
            "sign_security_name"=>$header_first->sign_security_name == null ? "" : $header_first->sign_security_name,
            "sign_security_path"=>$header_first->sign_security_path == null ? "" : $header_first->sign_security_path,
        ];

        $list = Array();

        foreach ($detail as $value) {
            $path = $value->filename == null ? "" : $value->path . "/" . $value->filename;

            $list[] = [
                "id"=>$value->id,
                "checklist_id"=>$value->checklist_id,
                "check_id"=>$value->check_id,
                "check_name"=>$value->check_name,
                "remarks"=>$value->remarks == null ? "" : $value->remarks,
                "path"=>$path,
                "filename"=>$value->filename == null ? "" : $value->filename
            ];
        }

        $response = [];

        if ( count($list) == 0 ) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "";
            $response["header"] = $header;
            $response["list"] = $list;
        }

        return response()->json($response, 200);
    }

    public function uploadCheckList(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $detail = CYChecklistDetail::find($request->id);

            $header = CYChecklistHeader::find($detail->checklist_id);

            try {
                $storage_path = "uploads/cy/$header->job_type/$header->job_no";

                $image = $request->filename;
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = $header->job_no . "-" . Str::of($detail->check_id)->padLeft(2, '0') . '.jpg';
                $filePath = "public/" . $storage_path . '/' . $imageName;

                Storage::put($filePath, base64_decode($image));

                $detail->path = $storage_path;
                $detail->filename = $imageName;
                $detail->remarks = $request->remarks;
                $detail->save();

                DB::commit();

                $response["error"] = "false";
                $response["message"] = "Upload data berhasil.";

                return $response;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $response["error"] = "true";
                $response["message"] = $e->getMessage();

                return $response;
            }
        });

        return response()->json($exception);
    }

    public function uploadSignature(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $header = CYChecklistHeader::find($request->id);

                $storage_path = "uploads/cy/$header->job_type/$header->job_no";

                $image = $request->filename;
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);

                if ( $request->job_type == "operation" ) {
                    $imageName = $header->job_no . "_$request->job_type" . '.jpg';
                    $filePath = "public/" . $storage_path . '/' . $imageName;

                    $header->sign_operation_path = $storage_path . "/" . $imageName;
                    $header->sign_operation_name = $request->name;
                } else if ( $request->job_type == "driver" ) {
                    $imageName = $header->job_no . "_$request->job_type" . '.jpg';
                    $filePath = "public/" . $storage_path . '/' . $imageName;

                    $header->sign_driver_path = $storage_path . "/" . $imageName;
                    $header->sign_driver_name = $request->name;
                } else if ( $request->job_type == "security" ) {
                    $imageName = $header->job_no . "_sec" . '.jpg';
                    $filePath = "public/" . $storage_path . '/' . $imageName;

                    $header->sign_security_path = $storage_path . "/" . $imageName;
                    $header->sign_security_name = $request->name;
                }

                Storage::put($filePath, base64_decode($image));

                $header->save();

                DB::commit();

                $response["error"] = "false";
                $response["message"] = "Upload data berhasil.";

                return $response;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $response["error"] = "true";
                $response["message"] = $e->getMessage();

                return $response;
            }
        });

        return response()->json($exception);
    }
}
