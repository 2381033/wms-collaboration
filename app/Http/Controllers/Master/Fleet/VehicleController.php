<?php

namespace App\Http\Controllers\Master\Fleet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Fleet\Vehicle as FleetVehicle;

class VehicleController extends Controller
{
    public function index (Request $request) {
        $details = DB::table("fm_vehicle as a")
                        ->select("a.*", "b.description", "c.driver_name")
                        ->join("fm_vehicle_type as b", "a.type_id", "b.id")
                        ->leftjoin("fm_driver as c", "a.driver_id", "c.id")
                        ->where("a.branch_id", $request->branch_id)
                        ->get();

        if ($request->ajax()) {
            return datatables()->of($details)
            ->editColumn('active', function ($data) {
                if ($data->active == 'Yes') {
                    $status = '<div class="btn btn-sm btn-success"><i class="fas fa-check"></i><span> ' . $data->active . '</span></div>';
                } else {
                    $status = '<div class="btn btn-sm btn-warning"><i class="fas fa-trash"></i><span> ' . $data->active . '</span></div>';
                }
                return $status;
            })
            ->addColumn('action', function($data){
                $button = "";
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm edit-vehicle"><i class="far fa-edit"></i> Edit</a>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request) {
        $messsages = array(
            'branch_vehicle.required'=>'Branch name cannot be empty.',
            'vehicle_type_id.required'=>'Vehicle type cannot be empty.',
            'vehicle_code.required'=>'Vehicle code cannot be empty.',
            'vehicle_no.required'=>'Vehicle no cannot be empty.',
        );

        $rules = array(
            'branch_vehicle' => 'required',
            'vehicle_type_id' => 'required',
            'vehicle_code' => 'required',
            'vehicle_no' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->vehicle_id;

        FleetVehicle::updateOrCreate(['id' => $id],
        [
            'branch_id' => $request->branch_vehicle,
            'type_id' => $request->vehicle_type_id,
            'vehicle_code' => $request->vehicle_code,
            'vehicle_no' => $request->vehicle_no,
            'active' => $request->active_vehicle
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $data  = FleetVehicle::where('id', $id)->first();

        return response()->json($data);
    }
}
