<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\isAdminCheck;
use App\Http\Controllers\Be\MuscleGroupsController;
use App\Http\Controllers\Be\MuscleController;
use App\Http\Controllers\Be\ExerciseController;
use App\Http\Controllers\Be\MusGroupsController;
use App\Http\Controllers\Be\NutrientController;
use App\Http\Controllers\Be\FoodController;
use App\Http\Controllers\Be\UserController;
use App\Http\Controllers\Be\WorkoutScheduleController;
use App\Http\Controllers\Be\MealScheduleController;
use App\Http\Controllers\Be\UnitController;
use App\Http\Controllers\Be\ProjectController;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// $title = "Cách Sử Dụng Slug trong Laravel";
// $slug = Str::slug($title, '-');

Route::get('/', function () {
    // return view('welcome');
    return redirect('/admin');
});

Auth::routes();



Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('admin');
// Route::get('/user', [TestController::class, 'index'])->name('user');
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin')->middleware('admin');

// Quản lý Auth
Route::post('/userlogin', [AuthController::class, 'login'])->name('userlogin');
Route::get('/showLoginForm', [AuthController::class, 'showLoginForm'])->name('showLoginForm');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['admin', 'checkUserRole:1'])->group(function () {

    // Route cho Unit
    Route::get('/units/index', [UnitController::class, 'index'])->name('units.index');
    Route::get('/units/create', [UnitController::class, 'create'])->name('units.create');
    Route::get('/units/edit/{id}', [UnitController::class, 'edit'])->name('units.edit');
    Route::post('/units/update', [UnitController::class, 'update'])->name('units.update');
    Route::post('/units/store', [UnitController::class, 'store'])->name('units.store');
    Route::get('/units/{unit}/users', [UnitController::class, 'viewUsers'])->name('units.view-users');
    Route::post('/units/{unit}/addUsers', [UnitController::class, 'addUsers'])->name('units.add-users');
    Route::get('/units/{unit}/create-users', [UnitController::class, 'createUserForm'])->name('units.create-users');
    Route::post('/units/{unit}/remove-user/{user_id}', [UnitController::class, 'removeUserFromUnit'])->name('units.remove-user');

    // Route cho project
    Route::get('/projects/index', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::get('/projects/show/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/edit/{id}', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::post('/projects/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('/projects/update/{id}', [ProjectController::class, 'update'])->name('projects.update');


    // Quản lý nhóm cơ
    Route::get('/muscle_groups/index', [MuscleGroupsController::class, 'index'])->name('muscle_groups.index');
    Route::get('/muscle_groups/create', [MuscleGroupsController::class, 'create'])->name('muscle_groups.create');
    Route::get('/muscle_groups/edit/{id}', [MuscleGroupsController::class, 'edit'])->name('muscle_groups.edit');
    Route::post('/muscle_groups/update', [MuscleGroupsController::class, 'update'])->name('muscle_groups.update');
    Route::post('/muscle_groups/store', [MuscleGroupsController::class, 'store'])->name('muscle_groups.store');
    Route::get('/muscle_groups/test', [MusGroupsController::class, 'index'])->name('muscle_groups.test');

    // Route::post('/muscle_groups/store-test', [MusGroupsController::class, 'store'])->name('muscle_groups.store-test');

    // Quản lý cơ
    Route::get('/muscle/index', [MuscleController::class, 'index'])->name('muscle.index');
    Route::get('/muscle/create', [MuscleController::class, 'create'])->name('muscle.create');
    Route::get('/muscle/edit/{id}', [MuscleController::class, 'edit'])->name('muscle.edit');
    Route::post('/muscle/store', [MuscleController::class, 'store'])->name('muscle.store');
    Route::post('/muscle/update', [MuscleController::class, 'update'])->name('muscle.update');



    // Quản lý bài tập
    Route::get('/exercise/index', [ExerciseController::class, 'index'])->name('exercise.index');
    Route::get('/exercise/create', [ExerciseController::class, 'create'])->name('exercise.create');
    Route::get('/exercise/edit/{id}', [ExerciseController::class, 'edit'])->name('exercise.edit');
    Route::post('/exercise/store', [ExerciseController::class, 'store'])->name('exercise.store');
    Route::post('/exercise/update', [ExerciseController::class, 'update'])->name('exercise.update');

    // quản lý dinh dưỡng 
    Route::get('/nutrient/index', [NutrientController::class, 'index'])->name('nutrient.index');
    Route::get('/nutrient/create', [NutrientController::class, 'create'])->name('nutrient.create');
    Route::get('/nutrient/edit/{id}', [NutrientController::class, 'edit'])->name('nutrient.edit');
    Route::post('/nutrient/store', [NutrientController::class, 'store'])->name('nutrient.store');
    Route::post('/nutrient/update', [NutrientController::class, 'update'])->name('nutrient.update');

    // Quản lý món ăn 
    Route::get('/food/index', [FoodController::class, 'index'])->name('food.index');
    Route::get('/food/create', [FoodController::class, 'create'])->name('food.create');
    Route::get('/food/edit/{id}', [FoodController::class, 'edit'])->name('food.edit');
    Route::post('/food/store', [FoodController::class, 'store'])->name('food.store');
    Route::post('/food/update', [FoodController::class, 'update'])->name('food.update');
    Route::delete('/food/destroy/{id}', [FoodController::class, 'destroy'])->name('food.destroy');


    // Quản lý hồ sơ
    // Các route cho quản lý profile
    Route::get('/profile/index', [UserController::class, 'index'])->name('profile.index');
    Route::get('/profile/create', [UserController::class, 'create'])->name('profile.create');
    Route::get('/profile/edit/{id}', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/store', [UserController::class, 'store'])->name('profile.store');
    Route::post('/profile/update', [UserController::class, 'update'])->name('profile.update');
    Route::delete('/profile/destroy/{id}', [UserController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/assignmentindex', [UserController::class, 'assignmentIndex'])->name('profile.assignmentindex');
    Route::get('/profile/assignment/{id}', [UserController::class, 'assignment'])->name('profile.assignment');
    Route::post('/profile/assignmentupdate', [UserController::class, 'assignmentupdate'])->name('profile.assignmentupdate');
});

Route::middleware(['admin', 'checkUserRole:2'])->group(function () {
    Route::get('/coach/listusers', [UserController::class, 'ListUsers'])->name('coach.listusers');

    // Quản lý lịch tập
    Route::get('/workout-schedules/index', [WorkoutScheduleController::class, 'index'])->name('workout_schedules.index');
    Route::get('/workout-schedules/create', [WorkoutScheduleController::class, 'create'])->name('workout_schedules.create');
    Route::get('/workout-schedules/edit/{id}/{date}', [WorkoutScheduleController::class, 'edit'])->name('workout_schedules.edit');
    Route::post('/workout-schedules/store', [WorkoutScheduleController::class, 'store'])->name('workout_schedules.store');
    Route::post('/workout-schedules/update', [WorkoutScheduleController::class, 'update'])->name('workout_schedules.update');
    Route::delete('/workout-schedules/destroy/{id}', [WorkoutScheduleController::class, 'destroy'])->name('workout_schedules.destroy');
    Route::get('/get-exercises/{muscleGroupId}', [WorkoutScheduleController::class, 'getExercises'])->name('get-exercises');
    Route::get('/workout-schedules/displaySchedule/{client_id}', [WorkoutScheduleController::class, 'displaySchedule'])->name('workout_schedules.displaySchedule');
    Route::get('/workout-schedules/detailSchedule/{client_id}', [WorkoutScheduleController::class, 'detailSchedule'])->name('workout_schedules.detailSchedule');


    // Quản lý lịch ăn
    Route::get('/meal-schedules/index', [MealScheduleController::class, 'index'])->name('meal_schedules.index');
    Route::get('/meal-schedules/create', [MealScheduleController::class, 'create'])->name('meal_schedules.create');
    Route::get('/meal-schedules/edit/{id}/{date}', [MealScheduleController::class, 'edit'])->name('meal_schedules.edit');
    Route::post('/meal-schedules/store', [MealScheduleController::class, 'store'])->name('meal_schedules.store');
    Route::post('/meal-schedules/update', [MealScheduleController::class, 'update'])->name('meal_schedules.update');
    Route::delete('/meal-schedules/destroy/{id}', [MealScheduleController::class, 'destroy'])->name('meal_schedules.destroy');
    Route::get('/get-foods/{foodType}', [MealScheduleController::class, 'getFoods'])->name('get-foods');
    Route::get('/meal-schedules/displaySchedule/{client_id}', [MealScheduleController::class, 'displaySchedule'])->name('meal_schedules.displaySchedule');
    Route::get('/meal-schedules/detailSchedule/{client_id}', [MealScheduleController::class, 'detailSchedule'])->name('meal_schedules.detailSchedule');
});
