<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckProjectAccess
{
    public function handle(Request $request, Closure $next)
    {
        $project = $request->route('project');
        $user = Auth::user();

        // Kiểm tra xem user có thuộc dự án hay không
        if (!$project->users->contains($user)) {
            return redirect('/unauthorized');
        }

        return $next($request);
    }
}
