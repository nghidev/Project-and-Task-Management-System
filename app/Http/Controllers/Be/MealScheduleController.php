<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use App\Models\MuscleGroup;
use App\Models\User;
use App\Models\Exercise;
use App\Models\Muscle;
use App\Models\ExerciseMuscle;
use App\Models\CoachingSession;
use App\Models\WorkoutSchedule;
use App\Models\MealSchedule;
use App\Models\Food;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;


class MealScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $coach_id = Auth::user()->id;
        $coachingSessions = CoachingSession::where('coach_id', $coach_id)->get();
    
        // Khởi tạo một mảng để lưu trữ người dùng đã được huấn luyện
        $clients = [];
    
        // Duyệt qua các phiên huấn luyện và lấy thông tin khách hàng
        foreach ($coachingSessions as $coachingSession) {
            // Giả sử bạn đã định nghĩa mối quan hệ 'client' trong model CoachingSession
            $client = $coachingSession->client;
    
            // Kiểm tra nếu khách hàng tồn tại và chưa có trong mảng
            if ($client && !in_array($client, $clients, true)) {
                $clients[$client->id] = $client->name;
            }
        }
    
        $coaches = User::where('role', 'coach')->pluck('name', 'id');
        $foods = Food::all();
        // Các biến muscles và exercises có thể không cần thiết nếu không sử dụng trong form
    
        return view('be.coach.footform', compact('coaches', 'clients', 'foods'));
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    // Lặp qua mảng các món ăn được chọn
    foreach ($request->food_ids as $key => $foodId) {
        // Tạo mới một instance của MealSchedule
        $mealSchedule = new MealSchedule;

        // Đặt các giá trị cho lịch ăn
        $mealSchedule->coach_id = Auth::user()->id;
        $mealSchedule->client_id = $request->input('client_id');
        $mealSchedule->event_date = $request->input('event_date');
        $mealSchedule->food_id = $foodId;

        // Lưu trữ lịch ăn
        $mealSchedule->save();
    }

    // Chuyển hướng hoặc phản hồi theo cách cần thiết
    return redirect()->route('coach.listusers')
        ->with('success', 'Lịch Ăn được tạo thành công');
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
    public function edit($id, $date)
    {
        $coach_id = Auth::user()->id;
    
        // Lấy thông tin CoachingSessions của huấn luyện viên
        $coachingSessions = CoachingSession::where('coach_id', $coach_id)->get();
    
        // Khởi tạo một mảng để lưu trữ thông tin khách hàng
        $clients = [];
    
        // Duyệt qua các phiên huấn luyện và lấy thông tin khách hàng
        foreach ($coachingSessions as $coachingSession) {
            $client = $coachingSession->client;
    
            if ($client && !in_array($client, $clients, true)) {
                $clients[$client->id] = $client->name;
            }
        }
    
        // Lấy thông tin MealSchedule cần chỉnh sửa
        $mealScheduleEdit = MealSchedule::where('client_id', $id)
            ->whereDate('event_date', $date)
            ->get();
    
        $foodIdsSelected = $mealScheduleEdit->pluck('food_id')->toArray();
        $dateSelected = '';
        $clientId = '';
    
        foreach ($mealScheduleEdit as $key => $value) {
            $foodId = $value->food_id;
            $foodsSelected[$foodId] = $value->food->name;
    
            $client = $value->client->id;
        }
    
        $dateSelected = $date;
    
        $foods = Food::all();
    
        // Truyền dữ liệu đến view để hiển thị trong form
        return view('be.coach.footform', compact('mealScheduleEdit', 'foods', 'client', 'foodsSelected', 'clients', 'dateSelected', 'foodIdsSelected'));
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
    // Lấy thông tin lịch ăn cần cập nhật
    $mealSchedules = MealSchedule::where('client_id', $request->id)
        ->whereDate('event_date', $request->date)
        ->get();

    // Lặp qua mảng các món ăn trong CSDL
    foreach ($mealSchedules as $mealSchedule) {
        $foodId = $mealSchedule->food_id;

        // Kiểm tra xem món ăn có trong danh sách món ăn được chọn từ form hay không
        if (!in_array($foodId, $request->food_ids)) {
            // Nếu không có, xóa bản ghi
            $mealSchedule->delete();
        }
    }

    // Lặp qua mảng các món ăn được chọn từ form
    foreach ($request->food_ids as $foodId) {
        // Kiểm tra xem đã có lịch ăn cho món ăn này chưa
        $existingMealSchedule = $mealSchedules->where('food_id', $foodId)->first();

        // Nếu chưa có, tạo mới một instance của MealSchedule
        if (!$existingMealSchedule) {
            $mealSchedule = new MealSchedule;
            $mealSchedule->coach_id = Auth::user()->id;
            $mealSchedule->client_id = $request->id;
            $mealSchedule->event_date = $request->date;
            $mealSchedule->food_id = $foodId;

            // Lưu trữ lịch ăn
            $mealSchedule->save();
        }
    }

    // Chuyển hướng hoặc phản hồi theo cách cần thiết
    return redirect()->route('coach.listusers')
        ->with('success', 'Lịch Ăn được cập nhật thành công');
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

    public function displaySchedule($client_id)
{
    // Lấy ID của huấn luyện viên đang đăng nhập
    $coach_id = Auth::user()->id;

    // Lấy danh sách phiên huấn luyện của huấn luyện viên
    $coachingSessions = CoachingSession::where('coach_id', $coach_id)->get();

    // Khởi tạo một mảng để lưu trữ thông tin khách hàng
    $clients = [];

    // Duyệt qua các phiên huấn luyện và lấy thông tin khách hàng
    foreach ($coachingSessions as $coachingSession) {
        // Giả sử mối quan hệ 'client' đã được định nghĩa trong model CoachingSession
        $client = $coachingSession->client;

        // Kiểm tra nếu khách hàng tồn tại và chưa có trong mảng
        if ($client && !in_array($client, $clients, true)) {
            $clients[$client->id] = $client->name;
        }
    }

    // Lấy danh sách các ngày có lịch ăn cho khách hàng cụ thể
    $dateList = MealSchedule::where('client_id', $client_id)
        ->select('event_date')->distinct()->pluck('event_date');

    // Lấy thông tin lịch ăn của khách hàng trong một ngày cụ thể (lấy ngày cuối cùng trong danh sách)
    $lastDate = $dateList->last();
    $mealSchedules = MealSchedule::where('client_id', $client_id)
        ->where('event_date', $lastDate)
        ->get();

    return view('be.coach.footindex', compact('dateList', 'client_id', 'clients', 'mealSchedules'));
}

}
