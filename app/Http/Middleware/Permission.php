<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Permission
{
    /**
     * Authentication (role based) for api calls
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user()->first();

        if ( $user->is_admin == 0) {

            return response()->json(['error' => [
                'message' => 'Access denied. You may not have the appropriate permissions to access.',
                'type' => 'access_denied',
                'error_details' => [
                    'error_title' => 'Access denied',
                    'error_feild' => NULL
                ],
            ]], 401);
        }

        return $next($request);
    }
}
