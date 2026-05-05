<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;

class UserSiteController extends Controller
{
    public function index(Request $request) {
        $user = User::find($request->user_id);

        if ($request->ajax()) {
            return datatables()->of($user->site)
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
        $user = User::find($request->user_site);

        if ($user->site()->where('site_id', $request->site_id)->exists()) {
            $message = [
                'error' => 'Site already exists'
            ];
        } else {
            $user->site()->attach($request->site_id);

            $message = [
                'success' => 'Data successfully saved.'
            ];
        }

        return response()->json($message);
    }

    public function destroy($user_id, $site_id)
    {
        try {
            $user = User::find($user_id);
            $user->site()->detach($site_id);
        } catch (\Illuminate\Database\QueryException $ex){
            $user = [ 'error'=> $ex->getMessage(), 'code' => $ex->getCode() ];
        }

        $data = ['succes' => 'Data successfully deleted.'];

        return response()->json($data);
    }
}
