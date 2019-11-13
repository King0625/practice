<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
class CheckUser
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
        $api_token = $request->bearerToken();
        $auth_user = User::where('api_token', $api_token)->get();
        // dd($auth_user);
        // dd($auth_user);
        if(!count($auth_user) || is_null($api_token)){
            return response()->json(['message' => 'Authentication error'], 401);
        }
        $request->attributes->set('auth_user', $auth_user);
        return $next($request);
    }
}
