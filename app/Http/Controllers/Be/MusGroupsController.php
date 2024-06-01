<?php

namespace App\Http\Controllers\Be;
use App\Http\Controllers\Controller;
use App\Models\MuscleGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Session;

use Illuminate\Http\Request;

class MusGroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $temp = MuscleGroup::all();
        
        return view('be.muscle_groups.test', compact('temp'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:muscle_groups,name',
            'description' => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new MuscleGroup instance
        $muscleGroup = new MuscleGroup();
        $muscleGroup->name = $request->name;
        $muscleGroup->slug = Str::slug($request->name, '-');
        $muscleGroup->description = $request->description;
        $muscleGroup->save(); // Save to database

        // Return a response
        Session::flash('success', 'Nhóm cơ đã được tạo thành công.');
        return response()->json(['success' => 'Nhóm cơ đã được tạo thành công.'], 200);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
