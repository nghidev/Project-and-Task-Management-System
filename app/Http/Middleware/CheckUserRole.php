<?php

// app/Http/Middleware/CheckUserRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        // dd($roles);
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        
        if (!in_array($user->role, $roles)) {
            return redirect('/unauthorized');
        }

        return $next($request);
    }
}
