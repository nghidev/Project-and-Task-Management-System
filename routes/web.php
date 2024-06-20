<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Be\UnitController;
use App\Http\Controllers\Be\ProjectController;
use App\Http\Controllers\Be\TaskController;
use App\Http\Controllers\Be\UserController;
use App\Http\Controllers\Be\ReportController;

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('admin');
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin')->middleware('admin');

// Quản lý Auth
Route::post('/userlogin', [AuthController::class, 'login'])->name('userlogin');
Route::get('/showLoginForm', [AuthController::class, 'showLoginForm'])->name('showLoginForm');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Route dành cho userRole 1 và 2

Route::middleware(['admin', 'checkUserRole:1,2'])->group(function () {

    // Route dành cho các chức năng liên quan đến báo cáo công việc
    // Route::post('/reports/store', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/tasks/{task}/reports', [ReportController::class, 'getReportsByTask'])->name('tasks.getReportsByTask');
    Route::put('/tasks/{task}/mark-reports-as-read', [ReportController::class, 'markReportsAsRead'])->name('tasks.markReportsAsRead');


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

    // Route cho Project
    Route::get('/projects/index', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::get('/projects/show/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/edit/{id}', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::post('/projects/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('/projects/update/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::get('/get-users-by-unit/{unitId}', [ProjectController::class, 'getUsersByUnit']);
    Route::get('/projects/{project}/users', [ProjectController::class, 'viewUsers'])->name('projects.view-users');
    Route::post('/projects/{project}/remove-user/{user_id}', [ProjectController::class, 'removeUserFromProject'])->name('projects.remove-user');
    Route::get('/projects/{project}/create-users', [ProjectController::class, 'createUserForm'])->name('projects.create-users');
    Route::post('/projects/{project}/addUsers', [ProjectController::class, 'addUsers'])->name('projects.add-users');
    Route::get('/projects/{project}/users', [ProjectController::class, 'getProjectUsers'])->name('projects.get-users');

    // Route cho Task
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/projects/{project}/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}/attachments/{attachment}', [TaskController::class, 'destroyAttachment'])->name('attachments.destroy');
    Route::put('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::put('tasks/{task}/due-date', [TaskController::class, 'updateDueDate'])->name('tasks.updateDueDate');
    Route::put('tasks/{task}/assigned-user', [TaskController::class, 'updateAssignedUser'])->name('tasks.updateAssignedUser');
    Route::get('tasks/{task}/attachments/{attachment}/download', [TaskController::class, 'downloadAttachment'])->name('tasks.downloadAttachment');
    Route::get('tasks/{task}/attachments/{attachment}/view', [TaskController::class, 'viewAttachment'])->name('tasks.viewAttachment');
    Route::get('/tasks/search', [TaskController::class, 'search'])->name('tasks.search');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('tasks.storeComment');
    Route::get('/tasks/{task}/comments', [TaskController::class, 'getComments'])->name('tasks.getComments');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::put('/tasks/{task}/due-date', [TaskController::class, 'updateDueDate'])->name('tasks.updateDueDate');
    Route::put('/tasks/{task}/assigned-user', [TaskController::class, 'updateAssignedUser'])->name('tasks.updateAssignedUser');

    // Route cho Profile
    Route::get('/profile/index', [UserController::class, 'index'])->name('profile.index');
    Route::get('/profile/create', [UserController::class, 'create'])->name('profile.create');
    Route::get('/profile/edit/{id}', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/store', [UserController::class, 'store'])->name('profile.store');
    Route::post('/profile/update', [UserController::class, 'update'])->name('profile.update');
    Route::delete('/profile/destroy/{id}', [UserController::class, 'destroy'])->name('profile.destroy');
});



// Route dành cho userRole 3
Route::middleware(['admin', 'checkUserRole:1,3,2'])->group(function () {
    // Route cho Unit
    Route::get('/units/index', [UnitController::class, 'index'])->name('units.index');
    Route::get('/units/{unit}/users', [UnitController::class, 'viewUsers'])->name('units.view-users');

    // Route cho Project
    Route::get('/projects/index', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/show/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/users', [ProjectController::class, 'viewUsers'])->name('projects.view-users');
    Route::get('/projects/{project}/users', [ProjectController::class, 'getProjectUsers'])->name('projects.get-users');

    // Route cho Task
    Route::middleware(['checkProjectAccess'])->group(function () {
        Route::get('/projects/{project}/tasks', [TaskController::class, 'index'])->name('tasks.index');
    });
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::get('tasks/{task}/attachments/{attachment}/download', [TaskController::class, 'downloadAttachment'])->name('tasks.downloadAttachment');
    Route::get('tasks/{task}/attachments/{attachment}/view', [TaskController::class, 'viewAttachment'])->name('tasks.viewAttachment');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('tasks.storeComment');
    Route::get('/tasks/{task}/comments', [TaskController::class, 'getComments'])->name('tasks.getComments');

    


    // Route cho báo cáo công việc
    Route::post('/reports/store', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/tasks/{task}/reports', [ReportController::class, 'getReportsByTask'])->name('tasks.getReportsByTask');
    // Route::put('/tasks/{task}/mark-reports-as-read', [ReportController::class, 'markReportsAsRead'])->name('tasks.markReportsAsRead');

});
