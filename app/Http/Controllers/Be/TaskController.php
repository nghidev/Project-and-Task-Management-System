<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Attachment;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    // public function index(Project $project, Request $request)
    // {
    //     $status = $request->get('status', 'all');
    //     $tasksQuery = Task::where('project_id', $project->id);

    //     if ($status !== 'all') {
    //         $tasksQuery->where('status', $status);
    //     }

    //     $tasks = $tasksQuery->get();

    //     return view('Back_End.Task.index', compact('tasks', 'project', 'status'));
    // }

    public function index(Project $project, Request $request)
    {
        $status = $request->get('status', 'all');
        $tasksQuery = Task::where('project_id', $project->id);
        $userRole = auth()->user()->role;
        $userId = auth()->user()->id; // Lấy ID của người dùng hiện tại
    
        if ($status !== 'all') {
            $tasksQuery->where('status', $status);
        }
    
        if ($userRole == 3) {
            $tasksQuery->where('assigned_user_id', $userId); // Chỉ lấy task được gán cho người dùng có role 3
        }
    
        $tasks = $tasksQuery->get();
    
        return view('Back_End.Task.index', compact('tasks', 'project', 'status', 'userRole'));
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
                    'uploaded_by' => auth()->id(), // Lưu thông tin người tải lên
                ]);
            }
        }

        return redirect()->route('tasks.index', ['project' => $task->project_id])
            ->with('success', 'Task created successfully.');
    }

    // public function show(Task $task)
    // {
    //     $task->load('attachments'); // Load attachments with task
    //     return response()->json($task);
    // }

    // public function show(Task $task)
    // {
    //     $task->load('attachments.user'); // Load attachments cùng với thông tin người dùng đã tải lên
    //     return response()->json($task);
    // }

    public function show(Task $task)
{
    $task->load('attachments.user'); // Load attachments cùng với thông tin người dùng đã tải lên
    $userRole = auth()->user()->role;
    return response()->json(['task' => $task, 'userRole' => $userRole]);
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
                    'uploaded_by' => auth()->user()->id, // Lưu thông tin người tải lên
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
    public function viewAttachment($taskId, $attachmentId)
    {
        $task = Task::findOrFail($taskId);
        $attachment = Attachment::where('task_id', $task->id)->where('id', $attachmentId)->firstOrFail();

        // Tạo URL công khai cho file
        $fileUrl = Storage::disk('public')->url($attachment->filepath);

        // Thay thế `http://localhost` bằng URL Ngrok của bạn
        $publicUrl = str_replace('http://localhost', 'https://6e64-14-240-120-126.ngrok-free.app', $fileUrl);

        // Redirect to Google Docs viewer
        return redirect('https://docs.google.com/viewer?url=' . urlencode($publicUrl));
    }
    public function search(Request $request)
    {
        $project_id = $request->input('project_id');
        $query = $request->input('query');
        $tasks = Task::where('project_id', $project_id)
            ->where('name', 'like', "%{$query}%")
            ->get();

        $view = view('Back_End.Task.partials.tasks', compact('tasks'))->render();

        return response()->json($view);
    }

    public function storeComment(Request $request, $taskId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'task_id' => $taskId,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user'));
    }

    public function getComments(Task $task)
    {
        $comments = $task->comments()->with('user')->get();
        return response()->json($comments);
    }
}
