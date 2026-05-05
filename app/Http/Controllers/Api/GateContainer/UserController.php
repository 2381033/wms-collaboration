<?php

namespace App\Http\Controllers\Api\GateContainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(Request $request) {
        $user = DB::table("users as a")
                    ->select(
                        "a.id",
                        "a.name",
                        "a.username",
                        "a.email",
                        "a.password",
                        "b.role_name"
                    )
                    ->join("sm_role as b", "a.role_id", "b.id")
                    ->where("a.username", $request->username)
                    ->first();
        
        if ($user) {
            if (password_verify($request->password, $user->password)) {
                $role_name = 'Admin';
                if ($user->role_name == 'Vendor') {
                    $role_name = 'Export';
                } else if ($user->role_name == 'User') {
                    $role_name = 'Truck';
                }
                return response()->json([
                    "error" => FALSE,
                    "id" => $user->id,
                    "name" => $user->name,
                    "username" => $user->username,
                    "email" => $user->email,
                    "role_id" => $role_name
                ]);
            } 

            return $this->error("Password salah.");
        }

        return $this->error("User tidak ditemukan.");
    }

    private function error($pesan) {
        return response()->json([
            "error" => TRUE,
            "user" => $pesan
        ]);
    }

    public function logout(){
        Auth::logout();
        return response()->json([
            "messages" => 'success',
        ]);
    }
}