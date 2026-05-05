<?php

namespace App\Http\Controllers\Master\Fleet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Master\Fleet\InspectionItem as FleetInspectionItem;

class InspectionItemController extends Controller
{
    public function index (Request $request) {
        $details = FleetInspectionItem::where("group_id", $request->group_id)->get();

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
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm edit-item"><i class="far fa-edit"></i> Edit</a>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request) {
        $messsages = array(
            'group_id.required'=>'Group name cannot be empty.',
            'item_name.required'=>'Item name cannot be empty.',
        );

        $rules = array(
            'group_id' => 'required',
            'item_name' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->item_id;

        FleetInspectionItem::updateOrCreate(['id' => $id],
        [
            'group_id' => $request->group_id,
            'item_name' => $request->item_name,
            'item_type' => $request->item_type,
            'active' => $request->active_item
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $data  = FleetInspectionItem::where('id', $id)->first();

        return response()->json($data);
    }
}
