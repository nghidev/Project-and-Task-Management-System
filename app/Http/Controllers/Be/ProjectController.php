<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\User;
use App\Models\Project;


class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['unit', 'users'])->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $units = Unit::all();
        $currentUser = auth()->user();
        return view('Back_End.project.form', compact('units', 'currentUser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'create_by' => 'required|exists:users,id',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Kiểm tra xem người dùng có thuộc unit không
        $unit = Unit::findOrFail($request->unit_id);
        $validUserIds = $unit->users->pluck('id')->toArray();

        foreach ($request->user_ids as $userId) {
            if (!in_array($userId, $validUserIds)) {
                return redirect()->back()->withErrors(['user_ids' => 'Có người dùng không thuộc không gian làm việc được chọn.']);
            }
        }

        $project = new Project();
        $project->unit_id = $request->unit_id;
        $project->name = $request->name;
        $project->description = $request->description;
        $project->create_by = $request->create_by;
        $project->save();

        $project->users()->sync($request->user_ids);

        return redirect()->route('admin')->with('success', 'Dự án đã được tạo thành công.');
    }

    public function show(Project $project)
    {
        $project->load('unit', 'users');
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $units = Unit::all();
        $currentUser = auth()->user();
        return view('projects.edit', compact('project', 'units', 'currentUser'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Kiểm tra xem người dùng có thuộc unit không
        $unit = Unit::findOrFail($request->unit_id);
        $validUserIds = $unit->users->pluck('id')->toArray();

        foreach ($request->user_ids as $userId) {
            if (!in_array($userId, $validUserIds)) {
                return redirect()->back()->withErrors(['user_ids' => 'Có người dùng không thuộc không gian làm việc được chọn.']);
            }
        }

        $project->unit_id = $request->unit_id;
        $project->name = $request->name;
        $project->description = $request->description;
        $project->save();

        $project->users()->sync($request->user_ids);

        return redirect()->route('projects.index')->with('success', 'Dự án đã được cập nhật thành công.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Dự án đã được xóa thành công.');
    }

    public function getUsersByUnit($unitId)
    {
        $unit = Unit::with('users')->findOrFail($unitId);
        return response()->json($unit->users->pluck('name', 'id'));
    }

    public function viewUsers($projectId)
    {
        $project = Project::with('users')->findOrFail($projectId);
        return view('Back_End.project.manage-users', compact('project'));
    }

    // public function addUsers(Request $request, Project $project)
    // {
    //     $request->validate([
    //         'user_ids' => 'required|array',
    //         'user_ids.*' => 'exists:users,id',
    //     ]);

    //     $project->users()->attach($request->user_ids); // Thêm mối quan hệ giữa dự án và người dùng
    //     return redirect()->route('projects.view-users', ['project' => $project->id])->with('success', 'Thêm người dùng vào dự án thành công.');
    // }
    public function addUsers(Request $request, Project $project)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $project->users()->attach($request->user_ids); // Thêm mối quan hệ giữa dự án và người dùng

        return response()->json(['success' => 'Thêm người dùng vào dự án thành công.']);
    }

    // public function createUserForm(Project $project)
    // {
    //     $users = User::all(); // Lấy danh sách người dùng
    //     return view('Back_End.project.create-user-to-project', compact('project', 'users'));
    // }
    public function createUserForm(Project $project)
    {
        // Lấy danh sách user trong unit của project nhưng không thuộc project này
        $unitUsers = $project->unit->users()->whereNotIn('users.id', $project->users->pluck('id'))->get();
        return response()->json($unitUsers);
    }

    public function removeUserFromProject(Project $project, $user_id)
    {
        $user = User::findOrFail($user_id);

        // Xóa người dùng khỏi dự án
        $project->users()->detach($user);
        return redirect()->back()->with('success', 'Xóa người dùng khỏi dự án thành công.');
    }

    public function getProjectUsers(Project $project)
    {
        $project->load('users');
        return response()->json($project->users);
    }
} 
