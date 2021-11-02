<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Config;

class VerifyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->role_id == Config::get('constants.GET_ROLE_ID.Admin')) {
            return $next($request);
        } else {
            return \response()->json(
                [
                    "code"      => 401,
                    "message"   => 'Bạn phải là admin để vào trang này! '
                ]
            );
        }
    }
}
