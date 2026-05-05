<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use DB;

class UserController extends Controller
{
    public $menu_name = "";

    public function index(Request $request)
    {
        $this->menu_name = 'User';
        $this->authorize('akses-gate', $this->menu_name);

        $bank_id = Auth::user()->bank_id;
        $details = User::where('bank_id', '=', $bank_id);

        if ($request->ajax()) {
            return datatables()->of($details)
                ->addColumn('action', function ($data) {
                    $button = "";
                    if (Gate::allows('edit-gate', $this->menu_name)) {
                        $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit btn btn-info btn-sm edit-data"><i class="far fa-edit"></i> Edit</a>';
                    }
                    $button .= '&nbsp;&nbsp;';
                    if (Gate::allows('hapus-gate', $this->menu_name)) {
                        $button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Hapus</button>';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('user', compact('details'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'Nama lengkap tidak boleh kosong'
        ]);

        $id = $request->id;
        $bank_id = Auth::user()->bank_id;

        if ($id == null || $id == "") {
            $post   =   User::updateOrCreate(
                ['id' => $id],
                [
                    'bank_id' => $bank_id,
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => bcrypt('password'),
                    'aktif' => $request->aktif
                ]
            );
        } else {
            $post   =   User::updateOrCreate(
                ['id' => $id],
                [
                    'bank_id' => $bank_id,
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'aktif' => $request->aktif
                ]
            );
        }

        return response()->json($post);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $post  = User::where($where)->first();

        return response()->json($post);
    }

    public function destroy($id)
    {
        try {
            $post = User::where('id', $id)->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            $post = ['message' => 'Tidak dapat dihapus, data ini sudah digunakan.', 'code' => $ex->getCode()];
        }

        return response()->json($post);
    }

    public function changePassword()
    {
        $data = User::Where('active', 'Yes')->get();

        // $data->map(function ($value) {
        //     $value->user_branch = DB::table('sm_user_branch')->where('user_id', $value->id)->first()->user_id ?? '-';
        //     $value->user_branch = DB::table('sm_user_branch')->where('user_id', $value->id)->first()->user_id ?? '-';
        // });

        return view('admin.change-password', compact('data'));
    }

    public function ubahPassword(Request $request)
    {
        // dd($request->all());
        $validate = $request->password != $request->password_konfirm;
        if ($validate) {
            return response()->json([
                'status' => 'gagal',
            ]);
        } else if (strlen($request->password) < 6) {
            return response()->json([
                'status' => 'kurang',
            ]);
        } else {
            DB::table('users')->where('id', $request->id_user)->update([
                'password' => bcrypt($request->password),
                'updated_at' => date('Y-m-d H:i:s'),
                // 'updated_by' => Auth::user()->name,
            ]);
            if (Auth::user()->id == $request->id) {
                Auth::logout();
            }
            return response()->json([
                'status' => 'ok',
            ]);
            // return back();
        }
    }
}
