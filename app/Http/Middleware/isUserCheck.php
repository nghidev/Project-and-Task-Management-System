<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isUserCheck
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user) {
            if ($user->role === 1 or $user->role === 2) {
                return $next($request);
            }
        }
        return redirect('/admin');
    }
}
