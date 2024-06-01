<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use App\Models\MuscleGroup;
use App\Models\Muscle;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\File;

// use Session;


use Illuminate\Http\Request;

class MuscleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $GetMuscleList = Muscle::all();
        $Tilte = "Cơ bắp";
        // dd($GetMuscleList->muscleGroup->name);

        return view("be.muscles.index", compact('GetMuscleList', 'Tilte'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $MuscleGroup = MuscleGroup::all();
        $Tilte = "CƠ BẮP";
        return view('be.muscles.form', compact('Tilte', 'MuscleGroup'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validatedData = ([
            'name' => 'required|max:255|unique:muscles,name',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $messages = [
            'name.required' => 'Tên cơ bắp là bắt buộc.',
            'name.max' => 'Tên cơ không thể dài hơn 255 ký tự.',
            'name.unique' => 'Tên nhóm cơ này đã tồn tại.',
            'image.required' => "Hình ảnh không được để trống.",
            'image.mimes' => "Chỉ chọn file có định dạng: jpeg,png,jpg,gif,svg.",
            'image.image' => "Vui lòng file có định dạng là hình ảnh.",
            'description.required' => "Không được để trống mô tả "
        ];

        $validator = Validator::make($request->all(), $validatedData, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }



        // dd($request->all());
        try {
            $muscle = new Muscle();
            $muscle->name = $request->name;
            $muscle->muscle_group_id = $request->muscle_group_id;
            $muscle->description = $request->description;

            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->getClientOriginalName();
                $request->image->move(public_path('storage/muscles'), $imageName);
                $validatedData['image'] = $imageName;
                $muscle->image = $imageName;
            }

            $muscle->save();
            // Chuyển hướng với thông điệp thành công
            Session::flash('success', 'Cơ đã được tạo thành công.');
            return redirect()->route('muscle.index');
        } catch (\Exception $e) {
            // Chuyển hướng với thông điệp lỗi
            Session::flash('error', 'Có lỗi xảy ra khi tạo cơ.');
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

        $MuscleGroup = MuscleGroup::all();
        $Tilte = "CƠ BẮP";
        $MuscleEdit = Muscle::find($id);

        return view('be.muscles.form', compact('MuscleGroup', 'MuscleEdit', 'Tilte'));
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

        // dd($request->all());
        $validatedData = ([
            'name' => 'required|max:255',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $messages = [
            'name.required' => 'Tên cơ bắp là bắt buộc.',
            'name.max' => 'Tên cơ không thể dài hơn 255 ký tự.',
            'name.unique' => 'Tên nhóm cơ này đã tồn tại.',
            // 'image.required' => "Hình ảnh không được để trống.",
            'image.mimes' => "Chỉ chọn file có định dạng: jpeg,png,jpg,gif,svg.",
            'image.image' => "Vui lòng file có định dạng là hình ảnh.",
            'description.required' => "Không được để trống mô tả "
        ];

        $validator = Validator::make($request->all(), $validatedData, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        try {
            $muscle = Muscle::find($request->id);
            $muscle->name = $request->name;
            $muscle->muscle_group_id = $request->muscle_group_id;
            $muscle->description = $request->description;
    
            if ($request->hasFile('image')) {
                // Xóa ảnh cũ nếu có
                $oldImage = public_path('storage/muscles/') . $muscle->image;
                if (File::exists($oldImage)) {
                    // dd('tồn tại ảnh');
                    File::delete($oldImage);
                }
    
                // Thêm ảnh mới
                $imageName = time() . '.' . $request->image->getClientOriginalName();
                $request->image->move(public_path('storage/muscles'), $imageName);
                $muscle->image = $imageName;
            }
    
            $muscle->save();
            Session::flash('success', 'Cơ đã được cập nhật thành công.');
            return redirect()->route('muscle.index');
        } catch (\Exception $e) {
            Session::flash('error', 'Có lỗi xảy ra khi cập nhật cơ.');
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
