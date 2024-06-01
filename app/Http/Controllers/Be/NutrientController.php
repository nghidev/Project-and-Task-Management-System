<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use App\Models\MuscleGroup;
use App\Models\Exercise;
use App\Models\Muscle;
use App\Models\ExerciseMuscle;
use App\Models\Nutrient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class NutrientController extends Controller
{
    protected $Title;
    public function __construct()
    {   
        $this->Title = 'DINH DƯỠNG';
        // $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Title = 'DINH DƯỠNG';

        $Nutrients = Nutrient::all();
        return view('be.nutrients.index', compact('Nutrients', 'Title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Title = 'DINH DƯỠNG';
        return view('be.nutrients.form', compact('Title'));
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

        ]);

        $messages = [
            'name.required' => 'Tên dinh dưỡng là bắt buộc.',
            'name.max' => 'Tên cơ không thể dài hơn 255 ký tự.',
            'name.unique' => 'Tên nhóm cơ này đã tồn tại.',
            'description.required' => "Không được để trống mô tả "
        ];

        $validator = Validator::make($request->all(), $validatedData, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $nutrient = new Nutrient;
        $nutrient->name = $request->name;
        $nutrient->description = $request->description;

        // Lưu thông tin dinh dưỡng
        $nutrient->save();

        // Gửi thông báo thành công và chuyển hướng
        Session::flash('message', 'Dinh dưỡng mới đã được tạo thành công!');
        return redirect()->route('nutrient.index');
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
        $NutrientEdit = Nutrient::find($id);
        $Title = 'DINH DƯỠNG';
        // dd($NutrientEdit);
        return view('be.nutrients.form', compact('Title', 'NutrientEdit'));
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
        $validatedData = ([
            'name' => 'required|max:255|unique:muscles,name',
            'description' => 'required',

        ]);

        $messages = [
            'name.required' => 'Tên dinh dưỡng là bắt buộc.',
            'name.max' => 'Tên cơ không thể dài hơn 255 ký tự.',
            'name.unique' => 'Tên nhóm cơ này đã tồn tại.',
            'description.required' => "Không được để trống mô tả "
        ];

        $validator = Validator::make($request->all(), $validatedData, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $nutrient = Nutrient::findOrFail($request->id);

        // Cập nhật thông tin
        $nutrient->name = $request->name;
        $nutrient->description = $request->description;
    
        // Lưu các thay đổi
        $nutrient->save();
    
        // Gửi thông báo thành công và chuyển hướng
        Session::flash('message', 'Thông tin dinh dưỡng đã được cập nhật thành công!');
        return redirect()->route('nutrient.index');
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
