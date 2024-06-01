<?php

namespace App\Http\Controllers\Be;
use App\Http\Controllers\Controller;
use App\Models\MuscleGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Session;


use Illuminate\Http\Request;

class MuscleGroupsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function test()
    {
        return view('be.muscle_groups.test');
    }
    public function index()
    {   

        $temp = MuscleGroup::all();
        
        return view('be.muscle_groups.index', compact('temp'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('be.muscle_groups.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $StoreMuscleGroup = MuscleGroup::new();
         // Định nghĩa quy tắc xác thực
         $rules = [
            'name' => 'required|max:255|unique:muscle_groups,name',
            'description' => 'required',
        ];

        // Định nghĩa thông điệp xác thực tùy chỉnh
        $messages = [
            'name.required' => 'Tên nhóm cơ là bắt buộc.',
            'name.max' => 'Tên nhóm cơ không thể dài hơn 255 ký tự.',
            'name.min' => 'Tên nhóm cơ tối thiểu 8 ký tự.',
            'name.unique' => 'Tên nhóm cơ này đã tồn tại.',
            'description.required' => 'Mô tả là bắt buộc.',
        ];

        // Tạo một thể hiện của validator
        $validator = Validator::make($request->all(), $rules, $messages);

        // Kiểm tra nếu xác thực không thành công
        if ($validator->fails()) {
            // Chuyển hướng trở lại form với dữ liệu đã nhập và lỗi
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput($request->all());
        }

        try {
            $muscleGroup = new MuscleGroup();
            $muscleGroup->name = $request->name;
            $muscleGroup->description = $request->description;
            $muscleGroup->slug = Str::slug($request->name, '-');
            $muscleGroup->save();

            

            // Chuyển hướng với thông điệp thành công
            Session::flash('success', 'Nhóm cơ đã được tạo thành công.');
            return redirect()->route('muscle_groups.index');
        } catch (\Exception $e) {
            // Chuyển hướng với thông điệp lỗi
            Session::flash('error', 'Có lỗi xảy ra khi tạo nhóm cơ.');
            return redirect()->back();
        }

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
        $muscleGroupEdit = MuscleGroup::find($id);

        if (!$muscleGroupEdit) {
            Session::flash('error', 'Nhóm cơ không tồn tại.');
            return redirect()->route('muscle_groups.index');
        }

        return view('be.muscle_groups.form', compact('muscleGroupEdit'));
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
      
        $rules = [
            'name' => 'required|max:255',
            'description' => 'required',
        ];

        $messages = [
            'name.required' => 'Tên nhóm cơ là bắt buộc.',
            'name.max' => 'Tên nhóm cơ không thể dài hơn 255 ký tự.',
            'name.min' => 'Tên nhóm cơ tối thiểu 8 ký tự.',
            // 'name.unique' => 'Tên nhóm cơ này đã tồn tại.',
            'description.required' => 'Mô tả là bắt buộc.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput($request->all());
        }

        try {
            $muscleGroup = MuscleGroup::find($request->id);
         
            if (!$muscleGroup) {
                Session::flash('error', 'Nhóm cơ không tồn tại.');
                return redirect()->route('muscle_groups.index');
            }

            $muscleGroup->name = $request->name;
            $muscleGroup->description = $request->description;
            $muscleGroup->slug = Str::slug($request->name,'-');
            $muscleGroup->save();

            Session::flash('success', 'Nhóm cơ đã được cập nhật thành công.');
            return redirect()->route('muscle_groups.index');
        } catch (\Exception $e) {
            Session::flash('error', 'Có lỗi xảy ra khi cập nhật nhóm cơ.');
            return redirect()->back();
        }
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
}
