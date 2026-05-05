<?php

namespace App\Http\Controllers\Master\Fleet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Fleet\Document as FleetDocument;

class DocumentController extends Controller
{
    public function index (Request $request) {
        $details = FleetDocument::all();

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
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm edit-document"><i class="far fa-edit"></i> Edit</a>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request) {
        $messsages = array(
            'document_name.required'=>'Description cannot be empty.',
        );

        $rules = array(
            'document_name' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $id = $request->document_id;

        FleetDocument::updateOrCreate(['id' => $id],
        [
            'document_name' => $request->document_name,
            'alert_1' => $request->alert_1,
            'alert_2' => $request->alert_2,
            'alert_3' => $request->alert_3,
            'alert_4' => $request->alert_4,
            'active' => $request->document_active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id)
    {
        $data  = FleetDocument::where('id', $id)->first();

        return response()->json($data);
    }
}
