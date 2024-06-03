<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        return view('Back_End.Task.index', compact('tasks', 'project', 'status'));
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
    public function show(Task $task)
    {
        $task->load('attachments'); // Load attachments with task
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1,2',
            'due_date' => 'required|date',
            'assigned_user_id' => 'required|exists:users,id',
            'attachments.*' => 'nullable|file|mimes:jpg,png,jpeg,gif,doc,docx,xlsx,pdf',
        ]);

        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'assigned_user_id' => $request->assigned_user_id,
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

        return response()->json(['success' => 'Task updated successfully']);
    }

    public function destroyAttachment(Request $request, $taskId, $attachmentId)
    {
        $task = Task::findOrFail($taskId);
        $attachment = Attachment::where('task_id', $task->id)->where('id', $attachmentId)->firstOrFail();

        // Delete physical file from storage
        Storage::disk('public')->delete($attachment->filepath);

        // Delete record from database
        $attachment->delete();

        return response()->json(['success' => 'Attachment removed successfully']);
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2',
        ]);

        $task->update(['status' => $request->status]);

        return response()->json(['success' => 'Status updated successfully']);
    }

    public function updateDueDate(Request $request, Task $task)
    {
        $request->validate([
            'due_date' => 'required|date',
        ]);

        $task->update(['due_date' => $request->due_date]);

        return response()->json(['success' => 'Due date updated successfully']);
    }

    public function updateAssignedUser(Request $request, Task $task)
    {
        $request->validate([
            'assigned_user_id' => 'required|exists:users,id',
        ]);

        $task->update(['assigned_user_id' => $request->assigned_user_id]);

        return response()->json(['success' => 'Assigned user updated successfully']);
    }

    public function downloadAttachment($taskId, $attachmentId)
    {
        $task = Task::findOrFail($taskId);
        $attachment = Attachment::where('task_id', $task->id)->where('id', $attachmentId)->firstOrFail();

        return Storage::disk('public')->download($attachment->filepath, $attachment->filename);
    }


}
