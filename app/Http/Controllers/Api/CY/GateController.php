<?php

namespace App\Http\Controllers\Api\CY;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\User;
use App\Models\Transaction\CY\ChecklistDetail as CYChecklistDetail;
use App\Models\Transaction\CY\ChecklistHeader as CYChecklistHeader;
use App\Models\Transaction\CY\Booking as CYBooking;
use App\Models\Transaction\CY\Gate as CYGate;
use App\Models\Transaction\CY\Checklist as CYChecklist;
use App\Models\Transaction\CY\Inbound as CYInbound;

class GateController extends Controller
{
    public function checkingBooking($user_id, $booking_no) {
        $book = DB::table("cy_booking as a")
                    ->join("sm_user_branch as b", "a.branch_id", "b.branch_id")
                    ->where("b.user_id", $user_id)
                    ->where("booking_no", $booking_no)
                    ->first();

        if ( !isset($book) ) {
            $response["error"] = "true";
            $response["message"] = "Nomor pesanan tidak ada.";
            return response()->json($response, 200);
        }

        if ( $book->status_flag !== 'Open' ) {
            $response["error"] = "true";
            $response["message"] = "Nomor Pesanan sudah diproses.";
            return response()->json($response, 200);
        }

        $header = DB::table("cy_booking as a")
                    ->select("a.*", "b.forwarder_name", "c.type_name", "d.size_name")
                    ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                    ->join("iv_container_type as c", "a.type_id", "c.id")
                    ->join("iv_container_size as d", "a.size_id", "d.id")
                    ->join("sm_user_branch as e", "a.branch_id", "e.branch_id")
                    ->where("e.user_id", $user_id)
                    ->where("a.id", $book->id)
                    ->first();

        if ( $header ) {
            $response["error"] = "false";
            $response["message"] = "";
            $response["id"] = $header->id;
            $response["company_name"] = $header->forwarder_name;
            $response["reference_no"] = $header->reference_no;
            $response["vehicle_no"] = $header->vehicle_no;
            $response["driver_name"] = $header->driver_name;
            $response["container_no"] = $header->container_no;
            $response["container_status"] = $header->container_status;
            $response["size_id"] = $header->size_id;
            $response["size_name"] = $header->size_name;
            $response["type_id"] = $header->type_id;
            $response["type_name"] = $header->type_name;
        } else {
            $response["error"] = "true";
            $response["message"] = "Tidak ada data yang akan diterima.";
        }

        return response()->json($response, 200);
    }

    public function inboundGateIn(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $user_name = User::find($request->user_id)->username;

            try {
                $book_check = DB::table("cy_booking as a")
                                ->select("a.*")
                                ->join("sm_user_branch as b", "a.branch_id", "b.branch_id")
                                ->where("b.user_id", $request->user_id)
                                ->where("a.booking_no", $request->booking_no)
                                ->first();

                if ( !isset($book_check) ) {
                    $response["error"] = "true";
                    $response["message"] = "Nomor pesanan tidak ada.";
                    return $response;
                }

                if ( $book_check->status_flag !== 'Open' ) {
                    $response["error"] = "true";
                    $response["message"] = "Nomor pesanan sudah diproses.";
                    return $response;
                }

                $book = CYBooking::where("branch_id", $book_check->branch_id)
                            ->where("booking_no", $request->booking_no)
                            ->first();

                $job_no = $this->getJob($book->branch_id);

                $gate = new CYGate();

                $gate->gate_type = "In";
                $gate->branch_id = $book->branch_id;
                $gate->vehicle_no = $request->vehicle_no;
                $gate->driver_name = $request->driver_name;
                $gate->booking_no = $book->booking_no;
                $gate->container_no = $request->container_no;
                $gate->gate_date = \Carbon\Carbon::today();
                $gate->gate_in = \Carbon\Carbon::now();
                $gate->save();

                $check_no = $this->getCheckList($book->branch_id);

                $check_header = new CYChecklistHeader();
                $check_header->branch_id = $book->branch_id;
                $check_header->forwarder_id = $book->forwarder_id;
                $check_header->job_no = $check_no;
                $check_header->job_date = \Carbon\Carbon::today();
                $check_header->job_type = "Inbound";
                $check_header->driver_name = $request->driver_name;
                $check_header->vehicle_no = $request->vehicle_no;
                $check_header->size_id = $request->size_id;
                $check_header->type_id = $request->type_id;
                $check_header->container_status = $request->container_status;
                $check_header->container_no = $request->container_no;
                $check_header->save();

                $check_list = CYChecklist::where("active", "Yes")->get();

                foreach ($check_list as $value) {
                    $check_detail = new CYChecklistDetail();
                    $check_detail->checklist_id = $check_header->id;
                    $check_detail->check_id = $value->id;
                    $check_detail->save();
                }

                $inbound_header = new CYInbound();

                $inbound_header->branch_id = $book->branch_id;
                $inbound_header->forwarder_id = $book->forwarder_id;
                $inbound_header->booking_id = $book->id;
                $inbound_header->job_no = $job_no;
                $inbound_header->job_date = \Carbon\Carbon::today();
                $inbound_header->checklist_no = $check_no;
                $inbound_header->booking_no = $book->booking_no;
                $inbound_header->reference_no = $book->reference_no;
                $inbound_header->invoice_type = $book->invoice_type;
                $inbound_header->book_driver_name = $book->driver_name;
                $inbound_header->book_vehicle_no = $book->vehicle_no;
                $inbound_header->book_size_id = $book->size_id;
                $inbound_header->book_type_id = $book->type_id;
                $inbound_header->book_container_status = $book->container_status;
                $inbound_header->book_container_no = $book->container_no;
                $inbound_header->driver_name = $request->driver_name;
                $inbound_header->vehicle_no = $request->vehicle_no;
                $inbound_header->size_id = $request->size_id;
                $inbound_header->type_id = $request->type_id;
                $inbound_header->container_status = $request->container_status;
                $inbound_header->container_no = $request->container_no;
                $inbound_header->user_id = $user_name;
                $inbound_header->save();

                $book->status_flag = "Confirmed";
                $book->save();

                DB::commit();

                $response["error"] = "false";
                $response["message"] = "Data sudah tersimpan.";

                return $response;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ["error"=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception, 200);
    }

    public function inboundGateOutList($user_id) {
        $date_from = \Carbon\Carbon::today()->addDay(-1);
        $date_to = \Carbon\Carbon::today()->addDay(1);

        $list_data = DB::table("cy_gate as a")
                        ->join("sm_user_branch as b", "a.branch_id", "b.branch_id")
                        ->where("b.user_id", $user_id)
                        ->where("a.gate_type", "In")
                        ->where("a.gate_out", null)
                        ->whereBetween("a.gate_date", [$date_from, $date_to])
                        ->orderBy("a.gate_out", "ASC")
                        ->get();

        $list = Array();

        foreach ($list_data as $value) {
            $list[] = [
                "id"=>$value->id,
                "vehicle_no"=>$value->vehicle_no,
                "driver_name"=>$value->driver_name,
                "container_no"=>$value->container_no,
                "gate_date"=>\Carbon\Carbon::parse($value->gate_date)->format('d/m/Y'),
                "gate_in"=>\Carbon\Carbon::parse($value->gate_in)->format('d/m/Y H:i:s'),
                "gate_out"=>$value->gate_out == null ? "" : \Carbon\Carbon::parse($value->gate_out)->format('d/m/Y H:i:s'),
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

    public function outboundGateIn(Request $request) {
        $gate = new CYGate();

        $user = User::find($request->user_id);

        $branch_id = $user->branch->first()->id;

        $gate->gate_type = "Out";
        $gate->branch_id = $branch_id;
        $gate->vehicle_no = $request->vehicle_no;
        $gate->driver_name = $request->driver_name;
        $gate->container_no = $request->container_no;
        $gate->gate_date = \Carbon\Carbon::today();
        $gate->gate_in = \Carbon\Carbon::now();
        $gate->save();

        $response["error"] = "false";
        $response["message"] = "Data berhasil disimpan.";

        return response()->json($response, 200);
    }

    public function outboundGateOutList($id) {
        $date_from = \Carbon\Carbon::today()->addDay(-1);
        $date_to = \Carbon\Carbon::today()->addDay(1);

        $list_data = DB::table("cy_gate as a")
                        ->join("sm_user_branch as b", "a.branch_id", "b.branch_id")
                        ->where("b.user_id", $id)
                        ->where("a.gate_type", "Out")
                        ->where("a.gate_out", null)
                        ->whereBetween("a.gate_date", [$date_from, $date_to])
                        ->orderBy("a.gate_out", "ASC")
                        ->get();

        $list = Array();

        foreach ($list_data as $value) {
            $list[] = [
                "id"=>$value->id,
                "vehicle_no"=>$value->vehicle_no,
                "driver_name"=>$value->driver_name,
                "container_no"=>$value->container_no,
                "gate_date"=>\Carbon\Carbon::parse($value->gate_date)->format('d/m/Y'),
                "gate_in"=>\Carbon\Carbon::parse($value->gate_in)->format('d/m/Y H:i:s'),
                "gate_out"=>$value->gate_out == null ? "" : \Carbon\Carbon::parse($value->gate_out)->format('d/m/Y H:i:s'),
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

    public function gateOut(Request $request) {
        $gate = CYGate::find($request->id);

        $gate->gate_out = \Carbon\Carbon::now();
        $gate->save();

        $response["error"] = "false";
        $response["message"] = "Data berhasil disimpan.";

        return response()->json($response, 200);
    }

    private function getJob($branch_id) {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = CYInbound::where('branch_id', $branch_id)
                            ->whereYear('job_date', $year)
                            ->whereMonth('job_date', $month)
                            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 6, 3) + 1;
        }

        $job_no = $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');

        return $job_no;
    }

    private function getCheckList($branch_id) {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = CYChecklistHeader::where('branch_id', $branch_id)
                            ->whereYear('job_date', $year)
                            ->whereMonth('job_date', $month)
                            ->where("job_type", "Inbound")
                            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $job_no = 'I' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }
}
