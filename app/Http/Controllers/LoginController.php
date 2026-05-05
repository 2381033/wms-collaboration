<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class LoginController extends Controller
{
    public function index() {
        return view('login');
    }

    public function postLogin(Request $request) {
        $messsages = array(
            
        );
    
        $rules = array(            
            "username" => "required",
            "password" => "required",
        );

        $niceNames = array(
            "username" => \Lang::get("user.username"),
            "password" => \Lang::get("user.password")
        );

        $validator = \Validator::make($request->all(), $rules, $messsages);
        
        $validator->setAttributeNames($niceNames); 
        
        if ($validator->fails()) {    
            return response()->json(["error"=>$validator->errors()->all()]);
        }
        
        $user = \App\User::where("username", $request->username)->first();
        
        if (!isset($user)) {
            return response()->json(["error"=>["User anda tidak terdaftar."]]);
        }
    
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $login = new \App\Login();
            $login->user_id = Auth::user()->id;
            $login->login = \Carbon\Carbon::now();
            $login->save();

            return response()->json(["success"=>route("home"), "message"=>"Anda berhasil login."]);
        }

        return response()->json(["error"=>["Anda memasukan data yang tidak valid."]]);
    }
     
    public function logout() {        
        $user_id = Auth::user()->id;
        $login = \App\Login::where("user_id", $user_id)->where("logout", null)->orderBy("login", "desc")->first();
        
        $login->logout = \Carbon\Carbon::now();
        $login->save();

        Session::flush();
        Auth::logout();
        return Redirect('login');
    }
}