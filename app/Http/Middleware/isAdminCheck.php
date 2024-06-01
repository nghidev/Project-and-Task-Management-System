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
            if ($user->role === 1 || $user->role === 2) {
                // if($user->role === 1 ){
                    return $next($request);
                // }
            }
            else{
                Auth::logout();
                return redirect()->back();
            }
        }
        else{
            return redirect('/showLoginForm');
        }
        
    }
}
