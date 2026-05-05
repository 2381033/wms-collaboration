<?php

namespace App\Http\Controllers\Master\Fleet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Master\Fleet\VehicleType as FleetVehicleType;

class VehicleTypeController extends Controller
{
    public function index (Request $request) {
        $details = FleetVehicleType::all();

        if ($request->ajax()) {
            return datatables()->of($details)
            ->editColumn('cbm', function ($data)
            {
                return number_format($data->cbm, 0);
            })
            ->editColumn('weight', function ($data)
            {
                return number_format($data->weight, 0);
            })
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
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm edit-type"><i class="far fa-edit"></i> Edit</a>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request) {
        $messsages = array(
            'vehicle_type.required'=>'Vehicle type cannot be empty.',
            'type_name.required'=>'Description cannot be empty.',
        );

        $rules = array(
            'vehicle_type' => 'required',
            'type_name' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->type_id;

        FleetVehicleType::updateOrCreate(['id' => $id],
        [
            'vehicle_type' => $request->vehicle_type,
            'description' => $request->type_name,
            'cbm' => $request->cbm,
            'weight' => $request->weight,
            'pallet_count' => $request->pallet_count,
            'active' => $request->active_type
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id) {
        $data  = FleetVehicleType::where('id', $id)->first();

        return response()->json($data);
    }
}
