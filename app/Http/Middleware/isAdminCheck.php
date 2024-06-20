<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isAdminCheck
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            if (in_array($user->role, [1, 2, 3])) {
                return $next($request);
            } else {
                Auth::logout();
                return redirect('/showLoginForm');
            }
        } else {
            return redirect('/showLoginForm');
        }
    }
}
