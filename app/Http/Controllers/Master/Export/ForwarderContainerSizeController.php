<?php

namespace App\Http\Controllers\Master\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Master\Export\Forwarder as ExportForwarder;

class ForwarderContainerSizeController extends Controller
{
    public function index(Request $request) {
        $forwarder = ExportForwarder::find($request->forwarder_id);

        if ($request->ajax()) {
            return datatables()->of($forwarder->container_size)
            ->addColumn('action', function($data){
                $button = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit-forwarder btn btn-info btn-sm edit-group"><i class="far fa-edit"></i> Edit</a>';
                $button .= "&nbsp;";
                $button .= '<button type="button" name="delete" id="'.$data->pivot->size_id.'" class="delete-size btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request)
    {
        $forwarder = ExportForwarder::find($request->forwarder_id_size);

        if ($forwarder->container_size()->where('size_id', $request->size_id)->exists()) {
            $message = [
                'error' => 'Container size already exists'
            ];
        } else {
            $forwarder->container_size()->attach($request->size_id, [
                'rate_amount'=>$request->rate_amount
            ]);

            $message = [
                'success' => 'Data successfully saved.'
            ];
        }

        return response()->json($message);
    }

    public function destroy($forwarder_id, $size_id)
    {
        try {
            $data = ExportForwarder::find($forwarder_id);
            $data->container_size()->detach($size_id);

            $data = ['succes' => 'Data successfully deleted.'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> $ex->getMessage(), 'code' => $ex->getCode() ];
        }


        return response()->json($data);
    }
}
