<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Master\Principal as MasterPrincipal;

class PrincipalSiteController extends Controller
{
    public function index(Request $request) {
        $principal = MasterPrincipal::find($request->principal_id);

        if ($request->ajax()) {
            return datatables()->of($principal->site)
            ->addColumn('action', function($data){
                $button = '<button type="button" name="delete" id="'.$data->pivot->site_id.'" class="delete-site btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request)
    {
        $principal = MasterPrincipal::find($request->principal_site);

        if ($principal->site()->where('site_id', $request->site_id)->exists()) {
            $message = [
                'error' => 'Site already exists'
            ];
        } else {
            $principal->site()->attach($request->site_id);

            $message = [
                'success' => 'Data successfully saved.'
            ];
        }

        return response()->json($message);
    }

    public function destroy($principal_id, $site_id)
    {
        try {
            $principal = MasterPrincipal::find($principal_id);
            $principal->site()->detach($site_id);

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
