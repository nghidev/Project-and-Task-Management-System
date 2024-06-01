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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;


class WorkoutScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
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
        // $clients = User::where('role', 'client')->pluck('name', 'id');
        $muscles = Muscle::pluck('name', 'id');
        // $exercises = Exercise::pluck('name', 'id'); // Lấy danh sách tất cả bài tập
        $exercises = Exercise::all();

        return view('be.coach.coachform', compact('coaches', 'clients', 'muscles', 'exercises'));
    }

    public function getExercises($muscleGroupId)
    {
        $exercises = Exercise::whereHas('muscles', function ($query) use ($muscleGroupId) {
            $query->where('muscle_group_id', $muscleGroupId);
        })->pluck('name', 'id');
    
        return response()->json($exercises);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  
        // dd($request->exercise_ids);
        // dd($request->exercise_ids);
      // Tạo mới một instance của WorkoutSchedule
   
    foreach ($request->exercise_ids as $key => $value) {
    $workoutSchedule = new WorkoutSchedule;
    $workoutSchedule->coach_id = Auth::user()->id;
    $workoutSchedule->client_id = $request->input('client_id');
    $workoutSchedule->event_date = $request->input('event_date');
    $workoutSchedule->exercise_id = $value;
    $workoutSchedule->save();

    }
    // Lưu trữ WorkoutSchedule

    // Đính kèm các bài tập vào lịch tập luyện sử dụng sync
    // $workoutSchedule->exercises()->sync($request->input('exercise_id'));

    // Chuyển hướng hoặc phản hồi theo cách cần thiết
    return redirect()->route('coach.listusers')
        ->with('success', 'Lịch Tập Luyện được tạo thành công');
    
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

    // Lấy thông tin WorkoutSchedule cần chỉnh sửa
    $workoutScheduleEdit = WorkoutSchedule::where('client_id', $id)
        ->whereDate('event_date', $date)
        ->get();

    $exercisesSelected = [];
    $dateSelected = '';

    foreach ($workoutScheduleEdit as $key => $value) {
        $exerciseId = $value->exercise_id;
        $exercisesSelected[$exerciseId] = $value->exercise->name;

        $client = $value->client->id;
    }

    $dateSelected = $date;

    $exercises = Exercise::all();

    // Truyền dữ liệu đến view để hiển thị trong form
    return view('be.coach.coachform', compact('workoutScheduleEdit', 'exercises', 'client', 'exercisesSelected', 'clients', 'dateSelected'));
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
    // Lấy thông tin lịch tập cần cập nhật
    $workoutSchedules = WorkoutSchedule::where('client_id', $request->id)
        ->whereDate('event_date', $request->date)
        ->get();
    // dd($request->date);
    // Lặp qua mảng các bài tập trong CSDL
    foreach ($workoutSchedules as $workoutSchedule) {
        $exerciseId = $workoutSchedule->exercise_id;

        // Kiểm tra xem bài tập có trong danh sách bài tập được chọn từ form hay không
        if (!in_array($exerciseId, $request->exercise_ids)) {
            // Nếu không có, xóa bản ghi
            $workoutSchedule->delete();
        }
    }

    // Lặp qua mảng các bài tập được chọn từ form
    foreach ($request->exercise_ids as $exerciseId) {
        // Kiểm tra xem đã có lịch tập cho bài tập này chưa
        $existingWorkoutSchedule = $workoutSchedules->where('exercise_id', $exerciseId)->first();

        // Nếu chưa có, tạo mới một instance của WorkoutSchedule
        if (!$existingWorkoutSchedule) {
            $workoutSchedule = new WorkoutSchedule;
            $workoutSchedule->coach_id = Auth::user()->id;
            $workoutSchedule->client_id = $request->id;
            $workoutSchedule->event_date = $request->date;
            $workoutSchedule->exercise_id = $exerciseId;

            // Lưu trữ lịch tập
            $workoutSchedule->save();
        }
    }

    // Chuyển hướng hoặc phản hồi theo cách cần thiết
    return redirect()->route('coach.listusers')
        ->with('success', 'Lịch Tập được cập nhật thành công');
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


    public function displaySchedule($client_id){
        // $date = []
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


        $temp = '';
        $client_id = $client_id;
        $dateList = WorkoutSchedule::where('client_id', $client_id)
        ->select('event_date')->distinct()->pluck('event_date');
        
        foreach ($dateList as $key => $value) {
          $temp = $value;
        }

        // dd($temp);
        $workoutDates = WorkoutSchedule::where('event_date', $temp)->get();

        // dd($workoutDates);
        // foreach ($WorkoutSchedule as $key => $value) {
        //     # code...asdfds

        // }
        return view('be.coach.coachindex', compact('dateList', 'client_id', "clients"));
    }
}
