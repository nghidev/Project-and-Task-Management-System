<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, $allowedRoles)
    {
        $user = Auth::user();
        $allowedRoles = explode('|', $allowedRoles);

        if ($user && in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        // Nếu không có quyền truy cập, chuyển hướng về trang trước đó
        return redirect()->back()->withErrors(['permission' => 'Bạn không có quyền truy cập.']);
    }
}
