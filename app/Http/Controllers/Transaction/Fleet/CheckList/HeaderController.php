<?php

namespace App\Http\Controllers\Transaction\Fleet\CheckList;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Fleet\CheckListHeader as FleetCheckListHeader;
use App\Models\Master\Fleet\InspectionItem as FleetInspectionItem;
use App\Models\Transaction\Fleet\CheckListDetail as FleetCheckListDetail;
use App\Models\Master\Fleet\Driver as FleetDriver;

class HeaderController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $details = FleetCheckListHeader::where('branch_id', $request->branch_id)
                    ->get();

            return datatables()->of($details)
            ->editColumn('job_date', function ($data)
            {
                return date('d/m/Y', strtotime($data->job_date) );
            })
            ->addColumn('job_no', function($data){
                $button = "";
                $button .= '<a href="' . URL("/fleet-checklist/edit/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                return $button;
            })
            ->rawColumns(['job_no'])
            ->addIndexColumn()
            ->make(true);
        }

        return view("transaction.fleet.checklist.index");
    }

    public function add() {
        return view("transaction.fleet.checklist.add");
    }

    public function create(Request $request) {
        $branch_id = $request->branch_id;

        $header = new FleetCheckListHeader();

        $job_no = $this->getJob($branch_id);

        $header->branch_id = $branch_id;
        $header->job_no = $job_no;
        $header->job_date = \Carbon\Carbon::today();
        $header->job_type = $request->job_type;
        $header->save();

        $inspection_list = FleetInspectionItem::where("active", "Yes")->get();

        foreach ($inspection_list as $value) {
            $detail = new FleetCheckListDetail();

            $detail->check_id = $header->id;
            $detail->group_id = $value->group_id;
            $detail->item_id = $value->id;
            $detail->item_type = $value->item_type;
            $detail->save();
        }

        return response()->json(["success"=>url("/fleet-checklist/edit/" . $header->id)]);
    }

    private function getJob($branch_id) {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = FleetCheckListHeader::where('branch_id', $branch_id)
                            ->whereYear('job_date', $year)
                            ->whereMonth('job_date', $month)
                            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 6, 4) + 1;
        }

        $job_no = $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }

    public function edit($id) {
        $header = DB::table("fm_checklist_header as a")
                    ->where("a.id", $id)
                    ->first();

        $detail = DB::table("fm_checklist_detail as a")
                    ->select("a.*", "b.group_name", "c.item_name")
                    ->join("fm_inspection_group as b", "a.group_id", "b.id")
                    ->join("fm_inspection_item as c", "a.item_id", "c.id")
                    ->where("a.check_id", $id)
                    ->orderBy("a.group_id", "asc")
                    ->orderBy("a.item_id", "asc")
                    ->get();

        $size_list = DB::table("iv_container_size as a")->where("a.active", "Yes")->get();
        $type_list = DB::table("iv_container_type as a")->where("a.active", "Yes")->get();
        $driver_list = DB::table("fm_driver as a")->where("a.branch_id", $header->branch_id)->where("a.active", "Yes")->get();
        $vehicle_list = DB::table("fm_vehicle as a")->where("a.branch_id", $header->branch_id)->where("a.active", "Yes")->get();

        $data = [
            "header" => $header,
            "detail" => $detail,
            "size_list" => $size_list,
            "type_list" => $type_list,
            "driver_list" => $driver_list,
            "vehicle_list" => $vehicle_list
        ];

        return view("transaction.fleet.checklist.create", $data);
    }

    public function store(Request $request) {
        $messsages = array(
            "size_id.required"=>"Container size field is required.",
            "type_id.required"=>"Container type field is required.",
            // "container_no.required"=>"Container no field is required.",
            "driver_id.required"=>"Driver name field is required.",
            "phone_no.required"=>"Phone no field is required.",
            "vehicle_no.required"=>"Vehicle no field is required.",
            "km_start.required"=>"Kilometer start field is required.",
            // "seal_no.required"=>"Seal no field is required.",
        );

        $rules = array(
            "size_id" => "required",
            "type_id" => "required",
            // "container_no" => "required",
            "driver_id" => "required",
            "phone_no" => "required",
            "vehicle_no" => "required",
            "km_start" => "required",
            // "seal_no" => "required",
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(["error"=>$validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $header = FleetCheckListHeader::find($request->id);

                $driver = FleetDriver::find($request->driver_id);

                $header->vendor_name = $request->vendor_name;
                $header->size_id = $request->size_id;
                $header->type_id = $request->type_id;
                $header->container_no = $request->container_no;
                $header->driver_id = $request->driver_id;
                $header->driver_name = $driver->driver_name;
                $header->phone_no = $request->phone_no;
                $header->vehicle_no = $request->vehicle_no;
                $header->seal_no = $request->seal_no;
                $header->remarks = $request->remarks_header;
                $header->km_start = $request->km_start;
                $header->km_end = $request->km_end;
                $header->inspection_date = \Carbon\Carbon::now();
                $header->save();

                $item = $request->item_id;
                $remarks = $request->remarks;
                $resultFlag = $request->resultFlag;
                $actionFlag = $request->actionFlag;

                for ($i=0; $i < count($item); $i++) {
                    $detail = FleetCheckListDetail::find($item[$i]);

                    $detail->results_flag = $resultFlag[$i];
                    if ( $detail->item_type == "Remarks" ) {
                        $detail->remarks = $remarks[$i];
                    } else if ( $detail->item_type == "Action" ) {
                        $detail->action_flag = $actionFlag[$i];
                    }
                    $detail->save();
                }

                DB::commit();

                $message = ['success'=>url("/fleet-checklist/add")];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function report($id) {
        $header = DB::table("fm_checklist_header as a")
                    ->select("a.*", "b.size_name", "c.type_name", "d.driver_name")
                    ->join("iv_container_size as b", "a.size_id", "b.id")
                    ->join("iv_container_type as c", "a.type_id", "c.id")
                    ->join("fm_driver as d", "a.driver_id", "d.id")
                    ->where("a.id", $id)
                    ->first();

        $detail = DB::table("fm_checklist_detail as a")
                    ->select("a.*", "b.group_name", "c.item_name")
                    ->join("fm_inspection_group as b", "a.group_id", "b.id")
                    ->join("fm_inspection_item as c", "a.item_id", "c.id")
                    ->where("a.check_id", $id)
                    ->orderBy("a.group_id", "asc")
                    ->orderBy("a.item_id", "asc")
                    ->get();

        $data = [
            "header" => $header,
            "detail" => $detail,
        ];

        return view('transaction.fleet.checklist.report', $data);
    }
}
