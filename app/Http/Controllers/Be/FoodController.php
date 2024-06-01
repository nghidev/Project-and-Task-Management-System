<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use App\Models\MuscleGroup;
use App\Models\Exercise;
use App\Models\Muscle;
use App\Models\ExerciseMuscle;
use App\Models\Nutrient;
use App\Models\Food;
use App\Models\FoodNutrient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nutrients = Nutrient::all();
        $foods = Food::all();

        // $query = Food::join('food_nutrient', 'foods.id', '=', 'food_nutrient.food_id')
        //     ->join('nutrients', 'nutrients.id', '=', 'food_nutrient.nutrient_id')
        //     ->select('nutrients.*', 'food_nutrient.description as pivot_description')
        //     ->where('foods.id', 1);

        // // In ra câu truy vấn SQL thực sự
        // $query->toSql();
        // // foreach ($foods as $nutrient) {
        // //     dd($nutrient->pivot_description);
        // // }
        // $foodss = $query->get();
        // dd($foodss);

        return view('be.foods.index', compact('foods','nutrients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $Title = "MÓN ĂN";
        $nutrients = Nutrient::all();

        return view("be.foods.form", compact("Title", "nutrients"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $nutrients = Nutrient::all();
        // $stringValue = '5/2';

        // // Using floatval
        // $floatValue = floatval($stringValue);
        // $floatValue += 5;
        // dd($floatValue);

        //      foreach ($nutrients as $key => $value) {
        //         // $food_nutrient  = new FoodNutrient();
        //         // $food_nutrient->food_id = $food->id;
        //         // $food_nutrient->nutrient_id = $value->id;
        //         $temp = $request->input($value->name);
        //         $cleanedValue = preg_replace("/[^0-9,.]/", "", $temp);
        //         // $food_nutrient->save();
        //         dd(floatval(str_replace(',', '.', $cleanedValue)));


        // }
        // $food_nutrient 

        // $getExerciseMuscle = ExerciseMuscle::all();
        // dd($getExerciseMuscle);

        // Xác thực dữ liệu đầu vào từ request với các thông báo tùy chỉnh
        $validatedData = ([
            'name' => 'required|max:255|unique:muscles,name',
            'description' => 'required',
            'calo' => 'required',
            'image' => 'required',
        ]);

        $messages = [
            'name.required' => 'Tên Thức ăn không được bỏ trống.',
            'name.max' => 'Tên thức ăn không thể dài hơn 255 ký tự.',
            'name.unique' => 'Tên món ăn này đã tồn tại.',
            'image.required' => "Hình ảnh không được để trống.",
            // 'image.mimes' => "Chỉ chọn file có định dạng: jpeg,png,jpg,gif,svg.",
            // 'image.image' => "Vui lòng file có định dạng là hình ảnh.",
            'description.required' => "Không được để trống mô tả "
        ];
        foreach ($nutrients as $key => $value) {
            $validatedData[$value->name] = 'required';
            $messages[$value->name . '.required'] = 'không được để trống định lượng ' . $value->name;
        }
        // dd($messages);


        $validator = Validator::make($request->all(), $validatedData, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        // Tạo Slug
        $slug = Str::slug($request->name, '-');

        $food = new Food;
        $food->name = $request->name;
        $food->slug = $slug;
        $food->calo = $request->calo;
        $food->description = $request->description;

        $imageName = '';
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalName();
            $request->image->move(public_path('storage/foods'), $imageName);
            $validatedData['image'] = $imageName;
        }
        $food->image = $imageName;

        $food->save();


        foreach ($nutrients as $key => $value) {
            $food_nutrient  = new FoodNutrient();
            $food_nutrient->food_id = $food->id;
            $food_nutrient->nutrient_id = $value->id;
            $temp = $request->input($value->name);
            $cleanedValue = preg_replace("/[^0-9,.]/", "", $temp);
            $food_nutrient->description = floatval(str_replace(',', '.', $cleanedValue));
            $food_nutrient->save();
        }
        // $exercise->muscles()->sync($request->options ?? []);

        Session::flash('success', 'Món ăn đã được thêm thành công.');
        return redirect()->route('exercise.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function show(Food $food)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // dd("adfasdf");
        $Title = "THỨC ĂN";
        $FoodEdit = Food::with('nutrients')->find($id);
        $nutrients = Nutrient::all();

        // foreach ($FoodEdit->nutrients as $nutrient){
        //     dd($nutrient->pivot->description);
        // }

        return view('be.foods.form', compact('FoodEdit', 'Title', 'nutrients'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $nutrients = Nutrient::all();

        $validatedData = [
            'name' => 'required|max:255|unique:muscles,name,',
            'description' => 'required',
            'calo' => 'required',
            // 'image' => 'required',
        ];

        $messages = [
            'name.required' => 'Tên Thực phẩm không được bỏ trống.',
            'name.max' => 'Tên thực phẩm không thể dài hơn 255 ký tự.',
            'name.unique' => 'Tên món ăn này đã tồn tại.',
            // 'image.required' => 'Hình ảnh không được để trống.',
            'description.required' => 'Không được để trống mô tả.',
        ];

        foreach ($nutrients as $key => $value) {
            $validatedData[$value->name] = 'required';
            $messages[$value->name . '.required'] = 'Không được để trống lượng ' . $value->name;
        }

        $validator = Validator::make($request->all(), $validatedData, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $food = Food::findOrFail($request->id);

        // Cập nhật các trường
        $food->name = $request->name;
        $food->calo = $request->calo;
        $food->description = $request->description;

        // Cập nhật hình ảnh nếu có
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalName();
            $request->image->move(public_path('storage/foods'), $imageName);
            $food->image = $imageName;
        }

        $food->save();

        // Cập nhật lượng chất dinh dưỡng
        foreach ($nutrients as $key => $value) {
            $foodNutrient = FoodNutrient::where('food_id', $food->id)
                ->where('nutrient_id', $value->id)
                ->first();

            if (!$foodNutrient) {
                // Nếu chưa có dữ liệu cho chất dinh dưỡng này, tạo mới
                $foodNutrient = new FoodNutrient();
                $foodNutrient->food_id = $food->id;
                $foodNutrient->nutrient_id = $value->id;
            }

            $temp = $request->input($value->name);
            $cleanedValue = preg_replace("/[^0-9,.]/", "", $temp);
            $foodNutrient->description = floatval(str_replace(',', '.', $cleanedValue));
            $foodNutrient->save();
        }

        Session::flash('success', 'Món ăn đã được cập nhật thành công.');
        return redirect()->route('food.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Food  $food
     * @return \Illuminate\Http\Response
     */
    public function destroy(Food $food)
    {
        //
    }
}
