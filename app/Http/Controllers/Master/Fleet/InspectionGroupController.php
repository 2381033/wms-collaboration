<?php

namespace App\Http\Controllers\Master\Fleet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Master\Fleet\InspectionGroup as FleetInspectionGroup;

class InspectionGroupController extends Controller
{
    public function index (Request $request) {
        $details = FleetInspectionGroup::all();

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
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm edit-group"><i class="far fa-edit"></i> Edit</a>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request) {
        $messsages = array(
            'description.required'=>'Description cannot be empty.',
        );

        $rules = array(
            'description' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->id;

        FleetInspectionGroup::updateOrCreate(['id' => $id],
        [
            'group_name' => $request->description,
            'active' => $request->active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $data  = FleetInspectionGroup::where('id', $id)->first();

        return response()->json($data);
    }
}
