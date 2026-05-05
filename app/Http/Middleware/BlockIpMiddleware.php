<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlockIpMiddleware
{
    public $blockIps = ['whitelist-ip-1', 'whitelist-ip-2'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $whitelist = DB::table("iv_api_whitelists")->select('IP')->where('IP', $this->getIp())->where('status', 'Y')->first();
        $str = str_replace('.', '', $this->getIp());
        $md5 = md5($str);
        $result = intval(preg_replace('/[^0-9]+/', '', $md5), 10);
        if (!isset($whitelist->IP)) {
            DB::table('iv_api_whitelists')->insertOrIgnore([
                'IP' => $this->getIp(),
                'remarks' => $result
            ]);
            // abort(503, "You are restricted to access the site.\nPlease Contact Admin with code = " . $result);
        } else {
            if ($request->ip() != $whitelist->IP) {
               // abort(503, "You are restricted to access the site.\nPlease Contact Admin with code = " . $result);
            }
        }
        if ($request->header('Authorization') != 'fWRQU4WNLqNdaqJ') {
            abort(503, "You are restricted to access the site.\nPlease insert Correct Authorization in Header");
        }
        return $next($request);
    }

    public function getIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return the server IP if the client IP is not found using this method.
    }
}
