<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    // public function handle($request, Closure $next, ...$guards): Response
    // {
    //     try {
    //         $this->authenticate($request, $guards);
    //     } catch (AuthenticationException $e) {
    //         if (!$request->wantsJson()) {
    //             throw $e;
    //         }

    //         if ($response = $this->auth->onceBasic()) {
    //             return $response;
    //         }
    //     }

    //     return $next($request);
    // }
}
