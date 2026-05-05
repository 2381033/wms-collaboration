<?php

namespace App\Http\Controllers\Master\Fleet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\Fleet\Driver as FleetDriver;

class DriverController extends Controller
{
    public function index (Request $request) {
        $details = FleetDriver::where("branch_id", $request->branch_id)->get();

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
            ->editColumn('sim_date', function ($data)
            {
                return date('d/m/Y', strtotime($data->sim_date) );
            })
            ->addColumn('action', function($data){
                $button = "";
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm edit-driver"><i class="far fa-edit"></i> Edit</a>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request) {
        $messsages = array(
            'branch_driver.required'=>'Branch name cannot be empty.',
            'driver_name.required'=>'Driver name cannot be empty.',
        );

        $rules = array(
            'branch_driver' => 'required',
            'driver_name' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->driver_id;

        $join_date = null;
        if (isset($request->join_date) && !empty($request->join_date)) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->join_date);
            $join_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');
        }

        $sim_date = null;
        if (isset($request->sim_date) && !empty($request->sim_date)) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->sim_date);
            $sim_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');
        }

        FleetDriver::updateOrCreate(['id' => $id],
        [
            'branch_id' => $request->branch_driver,
            'driver_name' => $request->driver_name,
            'phone' => $request->phone,
            'join_date' => $join_date,
            'sim_no' => $request->sim_no,
            'sim_date' => $sim_date,
            'active' => $request->active_driver
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $data  = FleetDriver::where('id', $id)->first();

        return response()->json($data);
    }
}
