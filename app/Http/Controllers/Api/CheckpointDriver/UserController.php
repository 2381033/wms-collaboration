<?php

namespace App\Http\Controllers\Api\CheckpointDriver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(Request $request) {
        $user = DB::table("users as a")
                    ->where("a.username", $request->username)
                    ->first();
        
        if ($user) {
            $auth = DB::table('auth_group')
                    ->where('id', $user->auth_group_id)
                    ->value('name') ?? 'Driver';
            if (password_verify($request->password, $user->password)) {
                $countJob = DB::table('cp_driver_job')
                ->whereDate('created_at', date('Y-m-d'))
                ->where('user_id', $user->id) 
                ->where('confirmed_flag', 'Yes') 
                ->count();

                return response()->json([
                    "error" => FALSE,
                    "id" => $user->id,
                    "name" => $user->name,
                    "username" => $user->username,
                    "email" => $user->email,
                    "role" => $auth,
                    "userDetails" => $user,
                    'countJob' => $countJob
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