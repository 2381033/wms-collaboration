<?php

namespace App\Http\Controllers\Api\Truck;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Master\Principal as MasterPrincipal;
use App\Models\Master\Transport\Gate as TransportGate;

class GateProcessController extends Controller
{
    public function gateList($user_id) {
        $job_list = DB::table("tm_gate as a")
                    ->select( "a.*", "c.principal_name", "d.vendor_name", "e.size_name", "f.type_name" )
                    ->join("users_principal as b", "a.principal_id", "b.principal_id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("tm_vendor as d", "a.vendor_id", "d.id")
                    ->join("iv_container_size as e", "a.size_id", "e.id")
                    ->join("iv_container_type as f", "a.type_id", "f.id")
                    ->where("b.user_id", $user_id)
                    ->where("status_flag", "No")
                    ->get();

        $list = Array();

        foreach ($job_list as $value) {
            $list[] = [
                "id"=>$value->id,
                "job_no"=>$value->job_no,
                "job_date"=>\Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
                "gate_type"=>$value->gate_type,
                "principal_id"=>$value->principal_id,
                "principal_name"=>$value->principal_name,
                "vendor_id"=>$value->vendor_id,
                "vendor_name"=>$value->vendor_name,
                "size_id"=>$value->size_id,
                "size_name"=>$value->size_name,
                "type_id"=>$value->type_id,
                "type_name"=>$value->type_name,
                "vehicle_no"=>$value->vehicle_no,
                "container_no"=>$value->container_no,
                "seal_no"=>$value->seal_no,
                "driver_name"=>$value->driver_name,
                "phone"=>$value->phone,
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

    public function gateJobList($user_id) {
        $job_list = DB::table("tm_gate as a")
                    ->select(
                        "a.id",
                        "a.job_no",
                        "a.job_date",
                        "a.gate_type",
                        "a.principal_id",
                        "a.vendor_id",
                        "a.size_id",
                        "a.type_id",
                        "a.vehicle_no",
                        "a.container_no",
                        "a.seal_no",
                        "a.driver_name",
                        "a.phone",
                        "c.principal_name",
                        "d.vendor_name",
                        "e.size_name",
                        "f.type_name"
                    )
                    ->join("users_principal as b", "a.principal_id", "b.principal_id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("tm_vendor as d", "a.vendor_id", "d.id")
                    ->join("iv_container_size as e", "a.size_id", "e.id")
                    ->join("iv_container_type as f", "a.type_id", "f.id")
                    ->join("tm_gate_process as g", "a.id", "g.gate_id")
                    ->where("b.user_id", $user_id)
                    ->where("g.check_flag", "No")
                    ->where("a.status_flag", "No")
                    ->groupBy(
                        "a.id",
                        "a.job_no",
                        "a.job_date",
                        "a.gate_type",
                        "a.principal_id",
                        "a.vendor_id",
                        "a.size_id",
                        "a.type_id",
                        "a.vehicle_no",
                        "a.container_no",
                        "a.seal_no",
                        "a.driver_name",
                        "a.phone",
                        "c.principal_name",
                        "d.vendor_name",
                        "e.size_name",
                        "f.type_name"
                    )
                    ->get();

        $list = Array();

        foreach ($job_list as $value) {
            $list[] = [
                "id"=>$value->id,
                "job_no"=>$value->job_no,
                "job_date"=>\Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
                "gate_type"=>$value->gate_type,
                "principal_id"=>$value->principal_id,
                "principal_name"=>$value->principal_name,
                "vendor_id"=>$value->vendor_id,
                "vendor_name"=>$value->vendor_name,
                "size_id"=>$value->size_id,
                "size_name"=>$value->size_name,
                "type_id"=>$value->type_id,
                "type_name"=>$value->type_name,
                "vehicle_no"=>$value->vehicle_no,
                "container_no"=>$value->container_no,
                "seal_no"=>$value->seal_no,
                "driver_name"=>$value->driver_name,
                "phone"=>$value->phone,
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

    public function gateView($gate_id) {
        $value = DB::table("tm_gate as a")
                    ->select( "a.*", "c.principal_name", "d.vendor_name", "e.size_name", "f.type_name" )
                    ->join("users_principal as b", "a.principal_id", "b.principal_id")
                    ->join("iv_principal as c", "a.principal_id", "c.id")
                    ->join("tm_vendor as d", "a.vendor_id", "d.id")
                    ->join("iv_container_size as e", "a.size_id", "e.id")
                    ->join("iv_container_type as f", "a.type_id", "f.id")
                    ->where("a.id", $gate_id)
                    ->where("status_flag", "No")
                    ->first();

        $job[] = [
            "id"=>$value->id,
            "job_no"=>$value->job_no,
            "job_date"=>\Carbon\Carbon::parse($value->job_date)->format('d/m/Y'),
            "gate_type"=>$value->gate_type,
            "principal_id"=>$value->principal_id,
            "principal_name"=>$value->principal_name,
            "vendor_id"=>$value->vendor_id,
            "vendor_name"=>$value->vendor_name,
            "size_id"=>$value->size_id,
            "size_name"=>$value->size_name,
            "type_id"=>$value->type_id,
            "type_name"=>$value->type_name,
            "vehicle_no"=>$value->vehicle_no,
            "container_no"=>$value->container_no,
            "seal_no"=>$value->seal_no,
            "driver_name"=>$value->driver_name,
            "phone"=>$value->phone,
        ];

        $response = [];

        if ( count($job) == 0 ) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "";
            $response["list"] = $job;
        }

        return response()->json($response, 200);
    }

    public function gateEntry(Request $request) {
        $exception = DB::transaction(function () use ($request) {

            try {
                $gate = TransportGate::find($request->gate_id);

                if (!isset($gate)) {
                    $gate = new TransportGate();

                    $gate->job_no = $this->getJob($request->gate_type);
                    $gate->job_date = \Carbon\Carbon::today();
                }

                $user = \App\User::find($request->user_id);

                $gate->gate_type = $request->gate_type;
                $gate->principal_id = $request->principal_id;
                $gate->vendor_id = $request->vendor_id;
                $gate->size_id = $request->size_id;
                $gate->type_id = $request->type_id;
                $gate->vehicle_no = $request->vehicle_no;
                $gate->driver_name = $request->driver_name;
                $gate->container_no = $request->container_no == null ? "" : $request->container_no;
                $gate->seal_no = $request->seal_no == null ? "" : $request->seal_no;
                $gate->phone = $request->phone;
                $gate->pick_flag = "Multi";
                $gate->user_id = $user->username;
                $gate->save();

                $process_count = TransportGateProcess::where("gate_id", $gate->id)->count();

                if ($process_count == 0) {
                    foreach ($user->site as $value) {
                        $gate_process = new TransportGateProcess();

                        $gate_process->gate_id = $gate->id;
                        $gate_process->site_id  = $value->id;
                        $gate_process->save();
                    }
                }

                // $checklist_count = TransportGateChecklist::where("gate_id", $gate->id)->count();

                // $checklist = DB::table("fm_inspection_group as a")
                //                     ->select("b.group_id", "b.id", "b.item_type")
                //                     ->join("fm_inspection_item as b", "a.id", "b.group_id")
                //                     ->where("a.active", "Yes")
                //                     ->where("b.active", "Yes")
                //                     ->get();

                // if ($checklist_count == 0) {
                //     foreach ($checklist as $value) {
                //         $gate_check = new TransportGateChecklist();

                //         $gate_check->gate_id = $gate->id;
                //         $gate_check->group_id  = $value->group_id;
                //         $gate_check->item_id  = $value->id;
                //         $gate_check->item_type  = $value->item_type;
                //         $gate_check->save();
                //     }
                // }

                DB::commit();

                $response["error"] = "false";
                $response["message"] = $gate->id;

                return $response;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $response["error"] = "true";
                $response["message"] = $e->getMessage();

                return $response;
            }
        });

        return response()->json($exception, 200);
    }

    public function gateUpdate(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $user_name = \App\User::find($request->user_id)->username;

            try {
                $gate = TransportGate::find($request->gate_id);

                $gate->document_no = $request->document_no;
                $gate->dispatch_date = \Carbon\Carbon::now();

                $gate->status_flag = "Yes";
                $gate->status_by = $user_name;
                $gate->status_date = \Carbon\Carbon::now();
                $gate->save();

                DB::commit();

                $response["error"] = "false";
                $response["message"] = $gate->id;

                return $response;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $response["error"] = "true";
                $response["message"] = $e->getMessage();

                return $response;
            }
        });

        return response()->json($exception, 200);
    }

    public function gateProcessList($gate_id, $type_name) {
        if ($type_name == "GateIn") {
            $gate_list = DB::table("tm_gate as a")
                        ->select( "b.id", "b.gate_id", "b.site_id", "c.site_name", "b.gate_in", "b.gate_out", "b.process_start", "b.process_finish" )
                        ->join("tm_gate_process as b", "a.id", "b.gate_id")
                        ->join("iv_site as c", "b.site_id", "c.id")
                        ->where("a.id", $gate_id)
                        ->whereNull("b.gate_in")
                        ->get();
        } else if ($type_name == "ProcessStart") {
            $gate_list = DB::table("tm_gate as a")
                        ->select( "b.id", "b.gate_id", "b.site_id", "c.site_name", "b.gate_in", "b.gate_out", "b.process_start", "b.process_finish" )
                        ->join("tm_gate_process as b", "a.id", "b.gate_id")
                        ->join("iv_site as c", "b.site_id", "c.id")
                        ->where("a.id", $gate_id)
                        ->whereNotNull("b.gate_in")
                        ->whereNull("b.process_start")
                        ->where("b.check_flag", "Yes")
                        ->get();
        } else if ($type_name == "ProcessFinish") {
            $gate_list = DB::table("tm_gate as a")
                        ->select( "b.id", "b.gate_id", "b.site_id", "c.site_name", "b.gate_in", "b.gate_out", "b.process_start", "b.process_finish" )
                        ->join("tm_gate_process as b", "a.id", "b.gate_id")
                        ->join("iv_site as c", "b.site_id", "c.id")
                        ->where("a.id", $gate_id)
                        ->whereNotNull("b.gate_in")
                        ->whereNotNull("b.process_start")
                        ->whereNull("b.process_finish")
                        ->where("b.check_flag", "Yes")
                        ->get();
        } else if ($type_name == "GateOut") {
            $gate_list = DB::table("tm_gate as a")
                        ->select( "b.id", "b.gate_id", "b.site_id", "c.site_name", "b.gate_in", "b.gate_out", "b.process_start", "b.process_finish" )
                        ->join("tm_gate_process as b", "a.id", "b.gate_id")
                        ->join("iv_site as c", "b.site_id", "c.id")
                        ->where("a.id", $gate_id)
                        ->whereNotNull("b.gate_in")
                        ->whereNotNull("b.process_start")
                        ->whereNotNull("b.process_finish")
                        ->whereNull("b.gate_out")
                        ->where("b.check_flag", "Yes")
                        ->get();
        }

        $list = Array();

        foreach ($gate_list as $value) {
            if ($type_name == "GateIn") {
                $gate_date = \Carbon\Carbon::parse($value->gate_in)->format('d/m/Y H:i:s');
            } elseif ($type_name == "GateOut") {
                $gate_date = \Carbon\Carbon::parse($value->gate_out)->format('d/m/Y H:i:s');
            } elseif ($type_name == "ProcessStart") {
                $gate_date = \Carbon\Carbon::parse($value->process_start)->format('d/m/Y H:i:s');
            } elseif ($type_name == "ProcessFinish") {
                $gate_date = \Carbon\Carbon::parse($value->process_finish)->format('d/m/Y H:i:s');
            }

            $list[] = [
                "id"=>$value->id,
                "gate_id"=>$value->gate_id,
                "site_id"=>$value->site_id,
                "site_name"=>$value->site_name,
                "gate_date"=>$gate_date
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

    public function gateProcessUpdate(Request $request) {
        $type_name = $request->type_name;
        $user_name = \App\User::find($request->user_id)->username;
        $gate_process = TransportGateProcess::find($request->process_id);

        if ($type_name == "GateIn") {
            $check_count = TransportGateProcess::where("gate_id", $gate_process->gate_id)
                            ->whereNotNull("gate_in")
                            ->whereNull("gate_out")
                            ->whereNotIn("id", [$request->process_id])
                            ->count();

            if ($check_count > 0) {
                $response["error"] = "true";
                $response["message"] = "Ada proses yang belum diselesaikan.";

                return json_encode($response);
            }

            $gate = TransportGate::find($gate_process->gate_id);
            $principal = MasterPrincipal::find($gate->principal_id);

            $gate_process->gate_in = \Carbon\Carbon::now();
            $gate_process->gate_in_by = $user_name;

            $checklist = DB::table("fm_inspection_group as a")
                            ->select("b.group_id", "b.id", "b.item_type")
                            ->join("fm_inspection_item as b", "a.id", "b.group_id")
                            ->where("a.active", "Yes")
                            ->where("b.active", "Yes")
                            ->get();

            if ($principal->multi_checklist == "Yes") {
                $checklist_count = TransportGateChecklist::where("process_id", $gate_process->id)->count();

                if ($checklist_count == 0) {
                    foreach ($checklist as $value) {
                        $gate_check = new TransportGateChecklist();

                        $gate_check->gate_id = $gate_process->gate_id;
                        $gate_check->process_id = $gate_process->id;
                        $gate_check->group_id  = $value->group_id;
                        $gate_check->item_id  = $value->id;
                        $gate_check->item_type  = $value->item_type;
                        $gate_check->save();
                    }
                }
            } else {
                $checklist_count = TransportGateChecklist::where("gate_id", $gate_process->gate_id)->count();

                if ($checklist_count == 0) {
                    foreach ($checklist as $value) {
                        $gate_check = new TransportGateChecklist();

                        $gate_check->gate_id = $gate_process->gate_id;
                        $gate_check->process_id = null;
                        $gate_check->group_id  = $value->group_id;
                        $gate_check->item_id  = $value->id;
                        $gate_check->item_type  = $value->item_type;
                        $gate_check->save();
                    }
                }
            }


        } else if ($type_name == "ProcessStart") {
            $gate_process->process_start = \Carbon\Carbon::now();
            $gate_process->process_start_by = $user_name;
        } else if ($type_name == "ProcessFinish") {
            $gate_process->process_finish = \Carbon\Carbon::now();
            $gate_process->process_finish_by = $user_name;
        } else if ($type_name == "GateOut") {
            $gate_process->gate_out = \Carbon\Carbon::now();
            $gate_process->gate_out_by = $user_name;
        }

        $gate_process->save();

        $response["error"] = "false";
        $response["message"] = "Berhasil disimpan";

        return json_encode($response);
    }

    private function getJob($gate_type) {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = TransportGate::where('gate_type', $gate_type)
                            ->whereYear('job_date', $year)
                            ->whereMonth('job_date', $month)
                            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $job_no = substr($gate_type, 0, 1) . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }

    public function checkList($gate_id) {
        $job_list = DB::table("tm_gate_checklist as a")
                    ->select( "a.*", "b.group_name", "c.item_name" )
                    ->join("fm_inspection_group as b", "a.group_id", "b.id")
                    ->join("fm_inspection_item as c", "a.item_id", "c.id")
                    ->where("a.gate_id", $gate_id)
                    ->get();

        $list = Array();

        foreach ($job_list as $value) {
            $list[] = [
                "id"=>$value->id,
                "group_name"=>$value->group_name,
                "item_name"=>$value->item_name,
                "item_type"=>$value->item_type,
                "results_flag"=>$value->results_flag == null ? "Yes" : $value->results_flag,
                "action_flag"=>$value->action_flag == null ? "Proper" : $value->action_flag,
                "remarks"=>$value->remarks == null ? "" : $value->remarks,
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

    public function gateCheckList($gate_id) {
        $checklist_count = TransportGateChecklist::where("gate_id", $gate_id)->count();

        $checklist = DB::table("fm_inspection_group as a")
                            ->select("b.group_id", "b.id", "b.item_type")
                            ->join("fm_inspection_item as b", "a.id", "b.group_id")
                            ->where("a.active", "Yes")
                            ->where("b.active", "Yes")
                            ->get();

        if ($checklist_count == 0) {
            foreach ($checklist as $value) {
                $gate_check = new TransportGateChecklist();

                $gate_check->gate_id = $gate->id;
                $gate_check->group_id  = $value->group_id;
                $gate_check->item_id  = $value->id;
                $gate_check->item_type  = $value->item_type;
                $gate_check->save();
            }
        }

        $check_list = DB::table("tm_gate_checklist as a")
                    ->select( "a.group_id", "b.group_name" )
                    ->join("fm_inspection_group as b", "a.group_id", "b.id")
                    ->where("a.gate_id", $gate_id)
                    ->groupBy( "a.group_id", "b.group_name" )
                    ->get();

        $job_list = DB::table("tm_gate_checklist as a")
                    ->select( "a.*", "b.item_name" )
                    ->join("fm_inspection_item as b", "a.item_id", "b.id")
                    ->where("a.gate_id", $gate_id)
                    ->get();

        $list = Array();

        foreach ($job_list as $value) {
            $list[] = [
                "id"=>$value->id,
                "group_id"=>$value->group_id,
                "item_name"=>$value->item_name,
                "item_type"=>$value->item_type,
                "results_flag"=>$value->results_flag == null ? "Yes" : $value->results_flag,
                "action_flag"=>$value->action_flag == null ? "Proper" : $value->action_flag,
                "remarks"=>$value->remarks == null ? "" : $value->remarks,
            ];
        }

        $response = [];

        if ( count($list) == 0 ) {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        } else {
            $response["error"] = "false";
            $response["message"] = "";
            $response["header"] = $check_list;
            $response["detail"] = $list;
        }

        return response()->json($response, 200);
    }

    public function gateCheckListUpdate(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $user_name = \App\User::find($request->user_id)->user_name;

            try {
                $json_array = json_decode($request->data);

                foreach ($json_array as $value) {
                    $checklist = TransportGateChecklist::find($value->id);

                    $gate_id = $checklist->gate_id;
                    $process_id = $checklist->process_id;

                    $checklist->results_flag = $value->results_flag;
                    $checklist->action_flag = $value->action_flag;
                    $checklist->remarks = $value->remarks;
                    $checklist->status_flag = 'Yes';
                    $checklist->user_id = $user_name;
                    $checklist->save();
                }

                if ( isset($gate_id) ) {
                    $gate = TransportGate::find($gate_id);
                    $principal = MasterPrincipal::find($gate->principal_id);

                    if ($principal->multi_checklist == "Yes") {
                        $process = TransportGateProcess::find($process_id);

                        if ($process->check_flag == "No") {
                            $process->check_flag = "Yes";
                            $process->check_date = \Carbon\Carbon::now();
                            $process->check_by = $user_name;
                            $process->save();
                        }
                    } else {
                        $process_list = TransportGateProcess::where("gate_id", $gate_id)->get();

                        foreach ($process_list as $value) {
                            $process = TransportGateProcess::find($value->id);

                            if ($process->check_flag == "No") {
                                $process->check_flag = "Yes";
                                $process->check_date = \Carbon\Carbon::now();
                                $process->check_by = $user_name;
                                $process->save();
                            }
                        }
                    }
                }

                DB::commit();

                $response["error"] = "false";
                $response["message"] = "Berhasil disimpan";

                return $response;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $response["error"] = "true";
                $response["message"] = $e->getMessage();

                return $response;
            }
        });

        return response()->json($exception, 200);
    }
}
