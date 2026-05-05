<?php

namespace App\Http\Controllers\Transaction\CY;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\CY\Booking as CYBooking;
use App\Models\Transaction\CY\Gate as CYGate;
use App\Models\Transaction\CY\ChecklistHeader as CYChecklistHeader;
use App\Models\Transaction\CY\Checklist as CYChecklist;
use App\Models\Transaction\CY\ChecklistDetail as CYChecklistDetail;
use App\Models\Transaction\CY\Inbound as CYInbound;

class GateController extends Controller
{
    public $menu_name = "cy/gate-in";

    public function index(Request $request) {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        return view("transaction.cy.gate.index");
    }

    public function view($booking_no) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $book = CYBooking::where("booking_no", $booking_no)->first();

        if ( !isset($book) ) {
            return ['error'=>["Booking not found"]];
        }

        if ( $book->status_flag !== 'Open' ) {
            return ['error'=>["Booking already process"]];
        }

        $header = DB::table("cy_booking as a")
                        ->select("a.*", "b.forwarder_name")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->join("sm_user_branch as c", "a.branch_id", "c.branch_id")
                        ->where("c.user_id", $user_id)
                        ->where("a.id", $book->id)
                        ->first();

        if ( !isset($header) ) {
            return ['error'=>["Booking maybe different branch"]];
        }

        $invoice_list = DB::table("cy_invoice_type")->where("company_id", $company_id)->where("active", "Yes")->get();
        $size_list = DB::table("iv_container_size")->where("company_id", $company_id)->where("active", "Yes")->get();
        $type_list = DB::table("iv_container_type")->where("company_id", $company_id)->where("active", "Yes")->get();

        $data = [
            "header" => $header,
            "invoice_list" => $invoice_list,
            "size_list" => $size_list,
            "type_list" => $type_list
        ];

        return response()->json($data);
    }

    public function inboundGateIn(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $user_name = Auth::user()->username;

            try {

                $book = CYBooking::where("booking_no", $request->booking_no)->first();

                if ( !isset($book) ) {
                    return ['error'=>["Booking not found"]];
                }

                if ( $book->status_flag !== 'Open' ) {
                    return ['error'=>["Booking already process"]];
                }

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

                $message = ["success"=>"Sukses"];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ["error"=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function inboundGateOut(Request $request) {
        $gate_count = CYGate::where("booking_no", $request->booking_no)->count();

        if ( $gate_count == 1 ) {
            $gate = CYGate::where("booking_no", $request->booking_no)->first();

            $gate->gate_out = \Carbon\Carbon::now();
            $gate->save();

            return ['success'=>["Sukses"]];
        } else {
            return ['error'=>["Booking not found"]];
        }
    }

    public function outboundGateIn(Request $request) {
        $gate = new CYGate();

        $gate->gate_type = "Out";
        $gate->branch_id = $request->branch_id;
        $gate->vehicle_no = $request->vehicle_no_out;
        $gate->driver_name = $request->driver_name_out;
        $gate->container_no = $request->container_no_out;
        $gate->gate_date = \Carbon\Carbon::today();
        $gate->gate_in = \Carbon\Carbon::now();
        $gate->save();

        return ['success'=>["Sukses"]];
    }

    public function outboundGateOut(Request $request) {
        $gate = CYGate::find($request->id);

        $gate->gate_out = \Carbon\Carbon::now();
        $gate->save();

        return ['success'=>["Sukses"]];
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

    public function outboundList(Request $request) {
        if ($request->ajax()) {
            $date_from = \Carbon\Carbon::today()->addDay(-7);
            $date_to = \Carbon\Carbon::today()->addDay(1);

            $list_data = DB::table("cy_gate as a")
                            ->where("a.gate_type", "Out")
                            ->where("a.gate_out", null)
                            ->whereBetween("a.gate_date", [$date_from, $date_to])
                            ->orderBy("a.gate_out", "ASC")
                            ->get();

            return datatables()->of($list_data)
                ->editColumn('gate_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->gate_date) );
                })
                ->editColumn('gate_in', function ($data)
                {
                    return date('d/m/Y H:i:s', strtotime($data->gate_in) );
                })
                ->editColumn('gate_out', function ($data)
                {
                    return $data->gate_out == null ? "" : date('d/m/Y H:i:s', strtotime($data->gate_out) );
                })
                ->addColumn('action', function($data){
                    $button = "";
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="gate" class="gate-out btn btn-info btn-sm edit-data"><i class="far fa-save"></i> Gate Out</a>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
