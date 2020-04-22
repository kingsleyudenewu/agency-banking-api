<?php

namespace App\Http\Middleware;

use App\Koloo\User;
use Closure;

class AdminCheck
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
        $authUser = $request->user();

        if(!$authUser) return response(['message' => 'Unauthenticated'], 401);

        $user = User::find($authUser->id);

        if(!$user->isAdmin()) return response(['message' => 'Access denied'], 401);


        return $next($request);
    }
}
