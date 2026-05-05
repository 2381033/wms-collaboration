<?php

namespace App\Http\Controllers\Api\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $user = DB::table("users as a")
            ->where("a.username", $request->username)
            ->first();

        if ($user) {
            $auth = DB::table('auth_group')
                ->where('id', $user->auth_group_id)
                ->get()->pluck('name')->toArray();
            if (password_verify($request->password, $user->password)) {
                return response()->json([
                    "error" => FALSE,
                    "users" => $user,
                    "auth" => $auth,
                ]);
            }

            return $this->error("Password salah.");
        }

        return $this->error("User tidak ditemukan.");
    }

    private function error($pesan)
    {
        return response()->json([
            "error" => TRUE,
            "user" => $pesan
        ]);
    }

    private function myBranch($username)
    {
        $idUser = DB::table('users')
            ->select('id')
            ->where('username', $username)
            ->value('id');
        $data = DB::table('sm_user_branch')
            ->where('user_id', $idUser)
            ->value('branch_id');
        return $data;
    }

    public function submitLogin(Request $request)
    {
        $user = \App\User::where("username", $request->username)->first();

        if ($user) {
            if (password_verify($request->password, $user->password)) {
                $response["error"] = FALSE;
                $response["success"] = "1";
                $response["message"] = "Data Ditemukan";
                $response["logindata"][] = array(
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'password' => $user->password
                );
            } else {
                $response["error"] = TRUE;
                $response["success"] = "0";
                $response["message"] = "Password salah!";
                $response["logindata"][] = array();
            }

            echo json_encode($response);
        } else {
            $response["error"] = TRUE;
            $response["success"] = "0";
            $response["message"] = "Data Kosong!";
            $response["logindata"][] = array();
            echo json_encode($response);
        }
    }
}
