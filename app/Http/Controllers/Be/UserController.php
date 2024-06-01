<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use App\Models\MuscleGroup;
use App\Models\User;
use App\Models\Exercise;
use App\Models\Muscle;
use App\Models\ExerciseMuscle;
use App\Models\CoachingSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('be.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Trong controller hoặc service provider
        $Title = 'NGƯỜI DÙNG';
        $roles = [
            1 => 'Quản trị',
            2 => 'Huấn luyện viên',
            0 => 'Người dùng',
        ];

        // $coaches = User::where('role', 2)->pluck('name', 'id');

        return view('be.users.form', compact('roles', 'Title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validatedData = [
            'name' => 'required|max:255|unique:muscles,name',
            // 'role' => 'required|in:1,2,3',
            'email' => 'required|email|unique:users,email', // Thêm quy tắc kiểm tra email
            'password' => 'required|min:6', // Thêm quy tắc kiểm tra mật khẩu
        ];

        $messages = [
            'name.required' => 'Tên người dùng là bắt buộc.',
            'name.max' => 'Tên không thể dài hơn 255 ký tự.',
            'name.unique' => 'Tên nhóm cơ này đã tồn tại.',
            // 'role.in' => 'Vai trò không hợp lệ.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ];

        $validator = Validator::make($request->all(), $validatedData, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password); // Mã hóa mật khẩu trước khi lưu
        $user->role = $request->role;

        // Lưu thông tin người dùng
        $user->save();

        // Gửi thông báo thành công và chuyển hướng
        Session::flash('message', 'Người dùng mới đã được tạo thành công!');
        return redirect()->route('profile.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Title = 'NGƯỜI DÙNG';
        $userEdit = User::find($id);
        return view('be.users.form', compact('userEdit', 'Title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validatedData = [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:6', // Cho phép mật khẩu rỗng hoặc ít nhất 6 ký tự
        ];

        $messages = [
            'name.required' => 'Tên người dùng là bắt buộc.',
            'name.max' => 'Tên không thể dài hơn 255 ký tự.',
            'name.unique' => 'Tên người dùng này đã tồn tại.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ];

        $validator = Validator::make($request->all(), $validatedData, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        // Lấy thông tin người dùng từ ID
        $user = User::find($request->id);

        // Cập nhật thông tin người dùng
        $user->name = $request->name;
        $user->email = $request->email;

        // Nếu có mật khẩu mới, thì cập nhật mật khẩu
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Cập nhật vai trò
        $user->role = $request->role;

        // Lưu thông tin người dùng đã cập nhật
        $user->save();

        // Gửi thông báo thành công và chuyển hướng
        Session::flash('message', 'Thông tin người dùng đã được cập nhật thành công!');
        return redirect()->route('profile.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function assignmentIndex()
    {

        $users = User::all()->where('role', 3);
        
        return view('be.assignment.index', compact('users'));
    }

    public function assignment($id)
    {
        $temp = CoachingSession::where('client_id', $id)
        ->first();

        // dd($temp);
        // dd($temp->coach_id);
        // $coachingSessions = DB::table('coaching_sessions')
        //     ->select('coaching_sessions.*', 'coach.name as coach_name', 'client.name as client_name')
        //     ->join('users as coach', 'coach.id', '=', 'coaching_sessions.coach_id')
        //     ->join('users as client', 'client.id', '=', 'coaching_sessions.client_id')
        //     ->where('coaching_sessions.coach_id', '=', 4)
        //     ->get();
        // $coach_id = 0;
        // // dd($coachingSessions[0]->coach_id);
        // foreach ($coachingSessions as $key => $value) {
        //     $coach_id = $value->coach_id;
        // }
    //    dd($coach_id);
        $coach = [];

        $client = User::find($id);
        $users = User::all()->where('role', 2);
        $user = User::find($id);
     
        // dd($coachedSessions);

        foreach ($users as $user) {
            // Gán từng giá trị vào mảng $arr
            $coach[$user->id] = $user->name;
        }
        // dd($client->coachedSessions->coach_id);
        return view('be.assignment.form', compact('coach', 'client', 'temp'));
    }

    public function assignmentUpdate(Request $request)
    {
         // Chuyển đổi giá trị coach_id và client_id sang kiểu số
    $coachId = intval($request->coach_id);
    $clientId = intval($request->client_id);

    // Tìm CoachingSession dựa trên client_id và coach_id
    $coachingSession = CoachingSession::where('client_id', $clientId)
        ->first();

        if ($coachingSession) {
            // Nếu đã có bản ghi, cập nhật người huấn luyện
            $coachingSession->coach_id = $request->coach_id;
            $coachingSession->client_id = $request->client_id;
            $coachingSession->save();
        } else {
            // Nếu chưa có bản ghi, thêm mới
            $createSession = new CoachingSession();
            $createSession->coach_id = $request->coach_id;
            $createSession->client_id = $request->client_id;
            $createSession->save();
        }
        // $coachingSession->coach_id = $coachId;
        // $coachingSession->client_id = $clientId;
        // $coachingSession->save();
        
        return redirect()->route('profile.assignmentindex');
    }


    public function ListUsers(Request $request)
    {
        $coach_id = Auth::user()->id;
        $coachingSessions = CoachingSession::where('coach_id', $coach_id)->get();

        // Khởi tạo một mảng để lưu trữ người dùng đã được huấn luyện
        $trainedUsers = [];

        // Duyệt qua các phiên huấn luyện và lấy thông tin khách hàng
        foreach ($coachingSessions as $coachingSession) {
            // Giả sử bạn đã định nghĩa mối quan hệ 'client' trong model CoachingSession
            $client = $coachingSession->client;

            // Kiểm tra nếu khách hàng tồn tại và chưa có trong mảng
            if ($client && !in_array($client, $trainedUsers, true)) {
                $trainedUsers[] = $client;
            }
        }
        // dd($trainedUsers);
        return view('be.coach.listuser', compact('trainedUsers'));
    }
}
