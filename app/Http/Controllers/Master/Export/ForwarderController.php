<?php

namespace App\Http\Controllers\Master\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Master\Export\Forwarder as ExportForwarder;
use Illuminate\Support\Facades\DB;

class ForwarderController extends Controller
{
    public function index(Request $request)
    {
        $details = ExportForwarder::all();
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
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit-forwarder btn btn-info btn-sm edit-group"><i class="far fa-edit"></i> Edit</a>';
                    $button .= "&nbsp;";
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip" id="' . $data->id . '" data-original-title="Edit" class="edit-service btn btn-warning btn-sm edit-group"><i class="fa fa-cogs"></i> Service</a>';
                    $button .= "&nbsp;";
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip" id="' . $data->id . '" data-original-title="Edit" class="edit-size btn btn-danger btn-sm edit-group"><i class="fa fa-car"></i> Container Size</a>';
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
            'forwarder_name.required' => 'Description cannot be empty.',
        );

        $rules = array(
            'forwarder_name' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $id = $request->forwarder_id;

        ExportForwarder::updateOrCreate(
            ['id' => $id],
            [
                'branch_id' => $request->branch_id,
                'forwarder_name' => $request->forwarder_name,
                'storage_amount' => $request->storage_amount,
                'adm_amount' => $request->adm_amount,
                'active' => $request->forwarder_active
            ]
        );

        return response()->json(['success' => 'Added new records.']);
    }

    public function edit($id)
    {
        $data  = ExportForwarder::where('id', $id)->first();

        return response()->json($data);
    }
}
