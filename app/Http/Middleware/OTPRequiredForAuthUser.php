<?php

namespace App\Http\Middleware;

use App\Koloo\User;
use Closure;

class OTPRequiredForAuthUser
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

        try {
            User::otpRequiredToContinue($request, new User($authUser));
        } catch (\Exception $e)
        {
            return response(['message' => $e->getMessage(), 'otp_required' => true], 401);
        }
        return $next($request);
    }
}
