<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;

class UserPrincipalController extends Controller
{
    public function index(Request $request) {
        $user = User::find($request->user_id);

        if ($request->ajax()) {
            return datatables()->of($user->principal)
            ->addColumn('action', function($data){
                $button = '<button type="button" name="delete" id="'.$data->pivot->principal_id.'" class="delete-principal btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request)
    {
        $user = User::find($request->user_principal);

        if ($user->principal()->where('principal_id', $request->principal_id)->exists()) {
            $message = [
                'error' => 'Principal already exists'
            ];
        } else {
            $user->principal()->attach($request->principal_id);

            $message = [
                'success' => 'Data successfully saved.'
            ];
        }

        return response()->json($message);
    }

    public function destroy($user_id, $principal_id)
    {
        try {
            $user = User::find($user_id);
            $user->principal()->detach($principal_id);
        } catch (\Illuminate\Database\QueryException $ex){
            $user = [ 'error'=> $ex->getMessage(), 'code' => $ex->getCode() ];
        }

        $data = ['succes' => 'Data successfully deleted.'];

        return response()->json($data);
    }
}
