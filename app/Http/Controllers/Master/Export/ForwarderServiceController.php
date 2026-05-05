<?php

namespace App\Http\Controllers\Master\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Master\Export\Forwarder as ExportForwarder;

class ForwarderServiceController extends Controller
{
    public function index(Request $request) {
        $forwarder = ExportForwarder::find($request->forwarder_id);

        if ($request->ajax()) {
            return datatables()->of($forwarder->service)
            ->addColumn('action', function($data){
                $button = '<button type="button" name="delete" id="'.$data->pivot->service_id.'" class="delete-service btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request)
    {
        $forwarder = ExportForwarder::find($request->forwarder_id_service);

        if ($forwarder->service()->where('service_id', $request->service_id)->exists()) {
            $message = [
                'error' => 'Service already exists'
            ];
        } else {
            $forwarder->service()->attach($request->service_id);

            $message = [
                'success' => 'Data successfully saved.'
            ];
        }

        return response()->json($message);
    }

    public function destroy($forwarder_id, $service_id)
    {
        try {
            $data = ExportForwarder::find($forwarder_id);
            $data->service()->detach($service_id);

            $data = ['succes' => 'Data successfully deleted.'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> $ex->getMessage(), 'code' => $ex->getCode() ];
        }


        return response()->json($data);
    }
}
