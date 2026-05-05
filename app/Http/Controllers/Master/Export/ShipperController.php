<?php

namespace App\Http\Controllers\Master\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Master\Export\Shipper as ExportShipper;
use Illuminate\Support\Facades\DB;

class ShipperController extends Controller
{
    public function index(Request $request)
    {
        $details = ExportShipper::all();
        $details->map(function ($value) {
            $value->branch_name = DB::table('mt_branch')->where('id', $value->branch_id)->value('branch_name');
        });

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
                ->addColumn('action', function ($data) {
                    $button = "";
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit-shipper btn btn-info btn-sm edit-group"><i class="far fa-edit"></i> Edit</a>';
                    return $button;
                })
                ->rawColumns(['active', 'action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $messsages = array(
            'description.required' => 'Description cannot be empty.',
        );

        $rules = array(
            'description' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $id = $request->id;

        ExportShipper::updateOrCreate(
            ['id' => $id],
            [
                'branch_id' => $request->branch_id,
                'shipper_name' => $request->description,
                'active' => $request->active
            ]
        );

        return response()->json(['success' => 'Added new records.']);
    }

    public function edit($id)
    {
        $data  = ExportShipper::where('id', $id)->first();

        return response()->json($data);
    }
}
