<?php

namespace App\Http\Controllers\NewUpdated\MNRManagement\Master;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Stock\Ledger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Session;
use DataTables;
use Illuminate\Support\Carbon;

class authenticateNewController extends Controller
{
    private function getAuth()
    {
        $auth = DB::table('auth_group')->get();
        return $auth;
    }

    private function getPermission()
    {
        $menu = DB::table('auth_permission')->get();
        return $menu;
    }

    private function getListMapping()
    {
        $data = DB::table('auth_group_permission')->get();
        return $data;
    }

    public function index()
    {
        $auth = $this->getAuth();
        $menu = $this->getPermission();
        $users = DB::table('users')->where('active', 'Yes')->get();
        return view("new.authenticate.index", compact('auth', 'menu', 'users'));
    }

    public function storeMappingUsers(Request $request)
    {
        DB::table('users')->where('id', $request->id_user)
            ->update([
                'auth_group_id' => $request->auth_group_id,
            ]);

        Session::flash('success', 'Berhasil di simpan..');
        return back();
    }

    public function storeMapping(Request $request)
    {
        if ($request->has('id_permission')) {
            DB::table('auth_group_permission')->where('auth_group_id', $request->auth_group_id)->delete();
            for ($i = 0; $i < count($request->id_permission); $i++) {
                DB::table('auth_group_permission')->insert([
                    'auth_group_id' => $request->auth_group_id,
                    'auth_permission_id' => $request->id_permission[$i],
                ]);
            }
            Session::flash('success', 'Berhasil di simpan..');
            return back();
        } else {
            Session::flash('error', 'Please Choise Permission..');
            return back();
        }
    }

    public function storeAuth($name)
    {
        DB::table('auth_group')->insert([
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Berhasil di simpan..');
        return back();
    }

    public function storePermission($name)
    {
        DB::table('auth_permission')->insert([
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Berhasil di simpan..');
        return back();
    }

    public function deleteAuth($id)
    {
        DB::table('auth_group')->where('id', $id)->delete();
        DB::table('auth_group_permission')->where('auth_group_id', $id)->delete();

        Session::flash('success', 'Berhasil di hapus..');
        return back();
    }

    public function deletePermission($id)
    {
        DB::table('auth_permission')->where('id', $id)->delete();
        DB::table('auth_group_permission')->where('auth_permission_id', $id)->delete();

        Session::flash('success', 'Berhasil di hapus..');
        return back();
    }

    public function detailAuth($id)
    {
        $master = $this->getListMapping()->where('auth_group_id', $id)->pluck('auth_permission_id')->toArray();

        $access = $this->getPermission()->whereIn('id', $master);
        $unaccess = $this->getPermission()->whereNotIn('id', $master);
        $list = [
            'access' => $access,
            'unaccess' => $unaccess
        ];
        return response()->json($list);
    }

    public function detailUsers($id)
    {
        $users = DB::table('users')->where('id', $id)->value('auth_group_id');
        $now = $this->getAuth()->where('id', $users)->first()->name ?? 'Not found';

        return response()->json($now);
    }
}
