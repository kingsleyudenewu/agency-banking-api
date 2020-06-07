<?php

namespace App\Http\Middleware;

use App\Koloo\User;
use Closure;

class CheckAccountState
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
        if($request->user())
        {
            $user = new User($request->user());
            if( ($user->isAgent() || $user->isSuperAgent()) && !$user->isApproved() )
            {
                return error_response('Your account must be approved to continue', null, 403);
            }

        }
        return $next($request);
    }
}
