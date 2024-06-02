<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\User;
use App\Models\Project;

class TaskController extends Controller
{
    public function index(Project $project, Request $request)
    {
        $status = $request->get('status', 'all');
        $tasksQuery = Task::where('project_id', $project->id);

        if ($status !== 'all') {
            $tasksQuery->where('status', $status);
        }

        $tasks = $tasksQuery->get();

        return view('tasks.index', compact('tasks', 'project', 'status'));
    }

    public function create()
    {
        $projects = Project::all();
        $users = User::all();
        return view('tasks.create', compact('projects', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1,2',
            'due_date' => 'required|date',
            'assigned_user_id' => 'required|exists:users,id',
            'attachments.*' => 'nullable|file|mimes:jpg,png,jpeg,gif,doc,docx,xlsx,pdf',
        ]);

        $task = Task::create([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'assigned_user_id' => $request->assigned_user_id,
            'create_by' => auth()->id(),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                Attachment::create([
                    'task_id' => $task->id,
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => $path,
                    'filetype' => $file->getClientMimeType(),
                ]);
            }
        }

        return redirect()->route('tasks.index', ['project' => $task->project_id])
                         ->with('success', 'Task created successfully.');
    }
}
