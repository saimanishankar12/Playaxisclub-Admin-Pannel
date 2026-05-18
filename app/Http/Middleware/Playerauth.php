<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PlayerAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! session()->has('player_id')) {
            return redirect()->route('user-login')
                ->with('error', 'Please login to continue.');
        }

        return $next($request);
    }
}