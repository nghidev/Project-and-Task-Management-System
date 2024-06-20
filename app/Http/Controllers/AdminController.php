<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        return view('user');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $units = [];
        $users = User::all();

        if (in_array($user->role, [2, 3])) {
            // Lấy các units mà người dùng thuộc về và nạp trước các dự án mà người dùng có quyền truy cập
            $units = $user->units()->with(['projects' => function($query) use ($user) {
                $query->whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }, 'projects.users', 'projects.creator', 'projects.tasks'])->get();
        } else if ($user->role == 1) {
            // Admin có thể xem tất cả các units và các dự án không phân biệt quyền truy cập
            $units = Unit::with(['creator', 'users', 'projects.users', 'projects.creator', 'projects.tasks'])->get();
        }

        // Trả về view với các units và dự án liên quan
        return view('Back_End.home', compact('units', 'users'));
    }
}
