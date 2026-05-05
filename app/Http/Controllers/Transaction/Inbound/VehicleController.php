<?php

namespace App\Http\Controllers\Transaction\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Models\Transaction\Inbound\Vehicle as inboundVehicle;
use App\Models\Transaction\Inbound\Job as inboundJob;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if ($request->ajax()) {
            $list_data = inboundVehicle::from('iv_inbound_vehicle as a')
                ->select('a.*')
                ->join('iv_principal as b', 'a.principal_id', 'b.id')
                ->join('users_principal as c', 'a.principal_id', 'c.principal_id')
                ->where('a.company_id', $company_id)
                ->where('c.user_id', $user_id)
                ->where('a.inbound_id', $request->inbound_id)
                ->get();

            return datatables()->of($list_data)
                ->addColumn('action', function ($data) {
                    $button = "";
                    if ($data->confirmed_flag == 'No') {
                        if (Gate::allows('gate-access', "warehouse/inbound")) {
                            $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit-vehicle btn btn-info btn-sm"><i class="far fa-edit"></i></a>';
                            $button .= '&nbsp;&nbsp;';
                            $button .= '<button type="button" name="delete-vehicle" id="' . $data->id . '" class="delete-vehicle btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                        }
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $data = inboundVehicle::find($request->id);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->username;
        $inbound_id = $request->inbound_vehicle;

        if ($inbound_id > 0) {
            $job_status = inboundJob::find($inbound_id);

            if ($job_status->received_flag == 'Yes') {
                return response()->json(['error' => ['Job Received.']]);
            }
        }

        $messsages = array(
            'vehicle_no.required' => 'Vehicle no field is required.',
            'driver_name.required' => 'Driver name field is required.',
            'transporter_name.required' => 'Transporter name field is required.',
        );

        $rules = array(
            'vehicle_no' => 'required',
            'driver_name' => 'required',
            'transporter_name' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $id = $request->vehicle_id;
        $company_id = Auth::user()->company_id;

        $job = inboundJob::find($inbound_id);

        $vehicle = inboundVehicle::where('inbound_id', $inbound_id)
            ->where('vehicle_no', 'like', "%$request->vehicle_no%")
            ->count();

        if ($vehicle > 0) {
            return response()->json(['error' => ['Vehicle already entry!!!']]);
        }

        $data   =   inboundVehicle::updateOrCreate(
            ['id' => $id],
            [
                'inbound_id' => $inbound_id,
                'company_id' => $company_id,
                'principal_id' => $job->principal_id,
                'job_no' => $job->job_no,
                'vehicle_no' => $request->vehicle_no,
                'transporter_name' => $request->transporter_name,
                'driver_name' => $request->driver_name,
                'container_no' => $request->container_no,
                'seal_no' => $request->seal_no,
                'awb_no' => $request->awb_no,
                'type_id' => $request->type_id,
                'size_id' => $request->size_id,
                'user_id' => $user_id
            ]
        );

        return response()->json(['success' => $data]);
    }

    public function destroy(Request $request)
    {
        try {
            inboundVehicle::where('id', $request->id)->delete();

            $data = ['success' => 'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex) {
            $data = ['error' => 'Cannot be deleted, this data is already used.'];
        }

        return response()->json($data);
    }
}
