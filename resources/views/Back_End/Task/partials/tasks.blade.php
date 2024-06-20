@foreach ($tasks as $task)
    @php
        $dueDate = \Carbon\Carbon::parse($task->due_date);
        $isOverdue = $dueDate->isPast() && $task->status != 2;
        $isNearDeadline = !$isOverdue && $dueDate->diffInDays(now()) <= 2 && $task->status != 2;
        $isCompleted = $task->status == 2;
    @endphp
    <tr class="{{ $isCompleted ? 'table-success' : ($isOverdue ? 'table-danger' : ($isNearDeadline ? 'table-warning' : '')) }}" data-task-id="{{ $task->id }}">
        <td class="task-name">{{ $task->name }}</td>
        <td class="task-status">
            <select class="form-control update-status" data-task-id="{{ $task->id }}">
                <option value="0" {{ $task->status == 0 ? 'selected' : '' }}>Mới</option>
                <option value="1" {{ $task->status == 1 ? 'selected' : '' }}>Đang làm</option>
                <option value="2" {{ $task->status == 2 ? 'selected' : '' }}>Hoàn thành</option>
            </select>
        </td>
        <td class="task-due-date">
            <input type="date" class="form-control update-due-date" data-task-id="{{ $task->id }}" value="{{ $dueDate->format('Y-m-d') }}">
        </td>
        <td class="task-assigned-user">
            <select class="form-control update-assigned-user" data-task-id="{{ $task->id }}">
                @foreach ($project->users as $user)
                    <option value="{{ $user->id }}" {{ $task->assigned_user_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-secondary view-task-btn" data-bs-toggle="modal" data-bs-target="#viewTaskModal" data-task-id="{{ $task->id }}">Chi tiết</button>
        </td>
    </tr>
@endforeach
