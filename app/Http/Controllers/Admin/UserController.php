<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RegisterUserEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\User;
use App\Role;
use App\Menu;
use App\Models\Master\Site as MasterSite;
use App\Models\Master\Principal as MasterPrincipal;
use App\Models\Master\Branch as MasterBranch;

class UserController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;

        $details = User::all();

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
                $button .= "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='$data->id' data-original-title='Edit' class='edit btn btn-info btn-sm edit-data'><i class='far fa-edit'></i> Edit</a>";
                $button .= "&nbsp;";
                $button .= "<button type='button' name='delete' id='$data->id' class='delete btn btn-danger btn-sm'><i class='far fa-trash-alt'></i> Hapus</button>";
                $button .= "&nbsp;";
                $button .= "<button type='button' name='principal' id='$data->id' class='principal btn btn-warning btn-sm'><i class='fas fa-warehouse'></i> Principal</button>";
                $button .= "&nbsp;";
                $button .= "<button type='button' name='site' id='$data->id' class='site btn-sm btn btn-warning btn-sm'><i class='fas fa-warehouse'></i> Site</button>";
                $button .= "&nbsp;";
                $button .= "<button type='button' name='branch' id='$data->id' class='branch btn btn-warning btn-sm'><i class='fas fa-key'></i> Branch</button>";
                $button .= "&nbsp;";
                $button .= "<button type='button' name='menu' id='$data->id' class='menu btn btn-warning btn-sm'><i class='fas fa-key'></i> Menu</button>";
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }

        $role_list = Role::where('active', 'Yes')->get();
        $site_list = MasterSite::where('company_id', $company_id)
                    ->where('active', 'Yes')
                    ->get();

        $principal_list = MasterPrincipal::where('company_id', $company_id)
                    ->where('active', 'Yes')
                    ->get();

        $branch_list = MasterBranch::where('active', 'Yes')
                    ->get();

        $menu_list = Menu::where('active', 'Yes')->get();

        $user_list = DB::table("users as a")
                        ->select("a.id", "a.name")
                        ->join("sm_role as b", "a.role_id", "b.id")
                        ->where("b.role_name", "<>", "Admin")
                        ->where('a.active', 'Yes')
                        ->get();

        $data = [
            'role_list' => $role_list,
            'site_list' => $site_list,
            'principal_list' => $principal_list,
            'branch_list' => $branch_list,
            'menu_list' => $menu_list,
            'user_list' => $user_list
        ];

        return view('admin.user', $data);
    }

    public function store(Request $request)
    {
        $messsages = array(
            'name.required'=>'Name cannot be empty.',
            'username.required'=>'User name cannot be empty.',
            'role_id.required'=>'Role name cannot be empty.',
            'email.required'=>'Email cannot be empty.',
        );

        $rules = array(
            'name' => 'required',
            'username' => 'required',
            'role_id' => 'required',
            'email' => 'required|email'
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $company_id = Auth::user()->company_id;

                $id = $request->id;

                $user = User::find($id);

                $password = rand(100000, 999999);

                if (isset($user)) {
                    $user->company_id = $company_id;
                    $user->name = $request->name;
                    $user->username = $request->username;
                    $user->role_id = $request->role_id;
                    $user->email = $request->email;
                    $user->active = $request->active;
                    $user->save();
                } else {
                    $user = new User();

                    $user->company_id = $company_id;
                    $user->name = $request->name;
                    $user->username = $request->username;
                    $user->email = $request->email;
                    $user->role_id = $request->role_id;
                    $user->password = bcrypt($password);
                    $user->active = $request->active;
                    $user->save();

                    if (isset($request->user_akses)) {
                        $user_akses = User::find($request->user_akses);
                        $menu_list = $user_akses->menu;

                        foreach ($menu_list as $value) {
                            if ($user->menu()->where('menu_id', $value->id)->exists()) {

                            } else {
                                $user->menu()->attach($value->id, ['akses'=>$value->pivot->akses, 'tambah'=>$value->pivot->tambah, 'edit'=>$value->pivot->edit, 'hapus'=>$value->pivot->hapus, 'cetak'=>$value->pivot->cetak ]);
                            }
                        }
                    }

                    $respon = [
                        "name" => $request->name,
                        "username" => $request->username,
                        "password" => $password
                    ];

                    $data["email"] = $request->email;

                    Mail::to($request->email)
                        ->send(new RegisterUserEmail($respon));
                }

                DB::commit();

                $message = ["success"=>"Sukses"];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ["error"=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $data  = User::where($where)->first();

        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        try {
            $data = User::where('id',$request->id)->delete();
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        $data = ['succes' => 'Data successfully deleted.'];

        return response()->json($data);
    }
}
