<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Master\Principal as MasterPrincipal;

class PrincipalBranchController extends Controller
{
    public function index(Request $request) {
        $principal = MasterPrincipal::find($request->principal_id);

        if ($request->ajax()) {
            return datatables()->of($principal->branch)
            ->addColumn('action', function($data){
                $button = '<button type="button" name="delete" id="'.$data->pivot->branch_id.'" class="delete-branch btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request)
    {
        $principal = MasterPrincipal::find($request->principal_branch);

        if ($principal->branch()->where('branch_id', $request->branch_id)->exists()) {
            $message = [
                'error' => 'branch already exists'
            ];
        } else {
            $principal->branch()->attach($request->branch_id);

            $message = [
                'success' => 'Data successfully saved.'
            ];
        }

        return response()->json($message);
    }

    public function destroy($principal_id, $branch_id)
    {
        try {
            $principal = MasterPrincipal::find($principal_id);
            $principal->branch()->detach($branch_id);

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
