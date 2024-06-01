<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {  
        if(Auth::user()){
            return redirect('/admin');
        }else{

            return view('be.auth.login');
        }
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
       
        // Kiểm tra thông tin đăng nhập
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Đăng nhập thành công, chuyển hướng hoặc thực hiện các công việc cần thiết
            return redirect('/admin');
        }

        // Đăng nhập thất bại, quay lại form đăng nhập với thông báo lỗi
        return back()->withErrors(['email' => 'Tài khoản hoặc mật khẩu không chính xác']);
    }

    // Đăng xuất
    public function logout()
    {
        Auth::logout();

        return redirect('/showLoginForm');
    }
}
