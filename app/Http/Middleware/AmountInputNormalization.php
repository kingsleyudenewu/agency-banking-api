<?php

namespace App\Http\Middleware;

use Closure;

class AmountInputNormalization
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

        $input = $request->all();

        if (isset($input['amount'])) {
            $input['amount'] = round($input['amount'],2);
            $request->replace($input);
        }

        return $next($request);
    }
}
