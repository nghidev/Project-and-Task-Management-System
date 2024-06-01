<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use App\Models\MuscleGroup;
use App\Models\Exercise;
use App\Models\Muscle;
use App\Models\ExerciseMuscle;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $exercise = Exercise::all();

        return view("be.exercises.index", compact('exercise'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $Muscle = Muscle::all();
        $convert = $Muscle->toArray();
        $options = [];
        foreach ($Muscle as $value) {
            $options[$value->id] =  $value->name;
        }

        $selectedOptions = ['option1', 'option3']; // Các giá trị được chọn mặc định


        return view("be.exercises.form", compact('options', 'selectedOptions', 'Muscle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // $getExerciseMuscle = ExerciseMuscle::all();
        // dd($getExerciseMuscle);

        // Xác thực dữ liệu đầu vào từ request với các thông báo tùy chỉnh
        $validatedData = ([
            'name' => 'required|max:255|unique:muscles,name',
            'description' => 'required',
            'image' => 'required',
        ]);

        $messages = [
            'name.required' => 'Tên cơ bắp là bắt buộc.',
            'name.max' => 'Tên cơ không thể dài hơn 255 ký tự.',
            'name.unique' => 'Tên nhóm cơ này đã tồn tại.',
            'image.required' => "Hình ảnh không được để trống.",
            // 'image.mimes' => "Chỉ chọn file có định dạng: jpeg,png,jpg,gif,svg.",
            // 'image.image' => "Vui lòng file có định dạng là hình ảnh.",
            'description.required' => "Không được để trống mô tả "
        ];

        $validator = Validator::make($request->all(), $validatedData, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        // Tạo Slug
        $slug = Str::slug($request->name, '-');

        $exercise = new Exercise;
        $exercise->name = $request->name;
        $exercise->slug = $slug;
        $exercise->muscle_id = $request->muscle_id;
        $exercise->description = $request->description;

        $imageName = '';
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalName();
            $request->image->move(public_path('storage/exercises'), $imageName);
            $validatedData['image'] = $imageName;
        }
        $exercise->image = $imageName;

        $exercise->save();

        // if (!empty($request->options)) {
        //     foreach ($request->options as $value) {
        //         $exerciseMuscle = new ExerciseMuscle;
        //         $exerciseMuscle->exercise_id = $exercise->id;
        //         $exerciseMuscle->muscle_id = $value;
        //         $exerciseMuscle->save();
        //     }
        // }
        $exercise->muscles()->sync($request->options ?? []);

        Session::flash('success', 'Bài tập đã được tạo thành công.');
        return redirect()->route('exercise.index');
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
        
        $exerciseEdit = Exercise::find($id);
        // dd($exerciseEdit->image);
        $Muscle = Muscle::all();
        $options = [];
        foreach ($Muscle as $value) {
            $options[$value->id] =  $value->name;
        }

        // $selectedOptions = [2,4];
        $selectedOptions = [];
        foreach ($exerciseEdit->muscles as $key => $value) {
            $selectedOptions[$key] =  $value->id;
        }

        // dd($selectedOptions);
        return view('be.exercises.form', compact('exerciseEdit', 'Muscle', 'options', 'selectedOptions'));
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
        // dd('vào được cập nhật');
        // Tìm Exercise theo ID hoặc trả về lỗi 404 nếu không tìm thấy
        $exercise = Exercise::findOrFail($request->id);

        // Xác thực dữ liệu đầu vào từ request với các thông báo tùy chỉnh
        $rules = [
            'name' => 'required|max:255|unique:muscles,name,', // Loại trừ bản ghi hiện tại khỏi quy tắc unique
            'description' => 'required',
            'image' => 'sometimes|required',
        ];

        $messages = [
            'name.required' => 'Tên cơ bắp là bắt buộc.',
            'name.max' => 'Tên cơ không thể dài hơn 255 ký tự.',
            'name.unique' => 'Tên nhóm cơ này đã tồn tại.',
            'image.required' => "Hình ảnh không được để trống.",
            // 'image.mimes' => "Chỉ chọn file có định dạng: jpeg,png,jpg,gif,svg.",
            // 'image.image' => "Vui lòng file có định dạng là hình ảnh.",
            'description.required' => "Không được để trống mô tả "
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cập nhật Slug
        $slug = Str::slug($request->name, '-');

        // Cập nhật thông tin Exercise
        $exercise->name = $request->name;
        $exercise->slug = $slug;
        $exercise->muscle_id = $request->muscle_id;
        $exercise->description = $request->description;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalName();
            $request->image->move(public_path('storage/exercises'), $imageName);
            $exercise->image = $imageName;
        }

        $exercise->save();

        // Cập nhật quan hệ với Muscle, sử dụng phương thức sync để xử lý việc thêm và xóa
        if (!empty($request->options)) {
            $exercise->muscles()->sync($request->options);
        } else {
            $exercise->muscles()->detach(); // Xóa tất cả quan hệ nếu không có option nào được chọn
        }

        Session::flash('success', 'Bài tập đã được cập nhật thành công.');
        return redirect()->route('exercise.index');
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
    // public function movetoback(Request $request)
    // {
    //     // return redirect()->back();
    //     dd()
    // }
}
