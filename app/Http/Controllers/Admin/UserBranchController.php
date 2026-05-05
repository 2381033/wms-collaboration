<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;

class UserBranchController extends Controller
{
    public function index(Request $request) {
        $user = User::find($request->user_id);

        if ($request->ajax()) {
            return datatables()->of($user->branch)
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
        $user = User::find($request->user_branch);

        if ($user->branch()->where('branch_id', $request->branch_id)->exists()) {
            $message = [
                'error' => 'Branch already exists'
            ];
        } else {
            $user->branch()->attach($request->branch_id);

            $message = [
                'success' => 'Data successfully saved.'
            ];
        }

        return response()->json($message);
    }

    public function destroy($user_id, $branch_id)
    {
        try {
            $user = User::find($user_id);
            $user->branch()->detach($branch_id);
        } catch (\Illuminate\Database\QueryException $ex){
            $user = [ 'error'=> $ex->getMessage(), 'code' => $ex->getCode() ];
        }

        $data = ['succes' => 'Data successfully deleted.'];

        return response()->json($data);
    }
}
