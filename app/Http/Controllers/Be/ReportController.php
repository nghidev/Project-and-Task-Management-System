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
use App\Models\Report;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string',
        ]);

        $report = Report::create([
            'task_id' => $request->task_id,
            'user_id' => $request->user_id,
            'description' => $request->description,
            'status' => false, // mặc định là chưa xem
        ]);

        return redirect()->back()->with('success', 'Báo cáo đã được gửi thành công.');
    }

    public function getReportsByTask($taskId)
    {
        $reports = Report::where('task_id', $taskId)->with('user')->get();
        return response()->json($reports);
    }

    public function markReportsAsRead($taskId)
    {
        Report::where('task_id', $taskId)->update(['status' => true]);
        return response()->json(['success' => 'Reports marked as read successfully.']);
    }

}
