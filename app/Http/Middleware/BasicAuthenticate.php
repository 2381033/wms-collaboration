<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class BasicAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next)
    {
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $timexp = date("13:00:00");//ubah jika ingin membuat token
        $password = $date.';'.$timexp;

        $hash_variable_salt = password_hash($password, PASSWORD_BCRYPT, array('cost' => 10));
        // print_r($password.' , '.$hash_variable_salt);die();
        $verify = password_verify($password, $request->header('Authorization'));
        $split = explode(";", $password);
        
        if(null==$request->header('Authorization')){  

            return Response::json(array('error'=>'Please set custom header'));  

        } 

        if($split[0] >= $date && $split[1] > $time){
            // Response::json(array('Authorized'));
            return $next($request); 
        } else {
            return Response::json(array('Unauthorized'));
        }
    }
}
