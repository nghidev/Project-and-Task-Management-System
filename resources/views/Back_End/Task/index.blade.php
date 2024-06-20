@extends('layouts.be')

@section('content')
    <div class="container">
        <h1>Tasks in Project: {{ $project->name }}</h1>

        <!-- Legend for task statuses -->
        <div class="mb-3">
            <h5>Legend:</h5>
            <div class="d-flex align-items-center">
                <div class="me-2" style="width: 20px; height: 20px; background-color: #f8d7da; border: 1px solid #f5c2c7;">
                </div>
                <span class="me-4">Overdue</span>
                <div class="me-2" style="width: 20px; height: 20px; background-color: #fff3cd; border: 1px solid #ffeeba;">
                </div>
                <span class="me-4">Near Deadline</span>
                <div class="me-2" style="width: 20px; height: 20px; background-color: #d4edda; border: 1px solid #c3e6cb;">
                </div>
                <span class="me-4">Completed</span>
            </div>
        </div>

        <div class="form-group">
            <label for="status">Filter by Status:</label>
            <select id="status" class="form-control" onchange="filterTasks()">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All</option>
                <option value="0" {{ $status == '0' ? 'selected' : '' }}>Mới</option>
                <option value="1" {{ $status == '1' ? 'selected' : '' }}>Đang làm</option>
                <option value="2" {{ $status == '2' ? 'selected' : '' }}>Hoàn thành</option>
            </select>
        </div>

        <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#createTaskModal"
            {{ Auth::user()->role == 3 ? 'disabled' : '' }}>Tạo Công Việc</button>

        @if ($tasks->isEmpty())
            <p>Chưa có công việc nào.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 10%;">Tên công việc</th>
                        <th>Trạng thái</th>
                        <th>Ngày hoàn thành</th>
                        <th>Người thực hiện</th>
                        <th>Hành động</th>
                        <th>Trạng thái báo cáo</th> <!-- Thêm cột trạng thái báo cáo -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        @php
                            $dueDate = \Carbon\Carbon::parse($task->due_date);
                            $isOverdue = $dueDate->isPast() && $task->status != 2;
                            $isNearDeadline = !$isOverdue && $dueDate->diffInDays(now()) <= 2 && $task->status != 2;
                            $isCompleted = $task->status == 2;
                            $reports = $task->reports;
                            $hasUnreadReports = $reports->where('status', false)->isNotEmpty(); // Kiểm tra báo cáo chưa đọc
                            $hasReports = $reports->isNotEmpty();
                        @endphp
                        <tr class="{{ $isCompleted ? 'table-success' : ($isOverdue ? 'table-danger' : ($isNearDeadline ? 'table-warning' : '')) }}"
                            data-task-id="{{ $task->id }}">
                            <td class="task-name">
                                @if ($hasReports)
                                    @if ($hasUnreadReports)
                                        <i class="bi bi-app-indicator text-danger"></i>
                                        <!-- Hiển thị icon đỏ nếu có báo cáo chưa đọc -->
                                    @else
                                        <i class="bi bi-stars text-success"></i>
                                        <!-- Hiển thị icon xanh nếu không có báo cáo chưa đọc -->
                                    @endif
                                @else
                                    <i class="bi bi-app-indicator text-warning"></i>
                                    <!-- Hiển thị icon vàng nếu không có báo cáo -->
                                @endif
                                {{ $task->name }}
                            </td>
                            <td class="task-status">
                                <select class="form-control update-status" data-task-id="{{ $task->id }}">
                                    <option value="0" {{ $task->status == 0 ? 'selected' : '' }}>Mới</option>
                                    <option value="1" {{ $task->status == 1 ? 'selected' : '' }}>Đang làm</option>
                                    <option value="2" {{ $task->status == 2 ? 'selected' : '' }}>Hoàn thành</option>
                                </select>
                            </td>
                            <td class="task-due-date">
                                <input type="date" class="form-control update-due-date"
                                    data-task-id="{{ $task->id }}" value="{{ $dueDate->format('Y-m-d') }}"
                                    {{ $userRole == 3 ? 'disabled' : '' }}>
                            </td>
                            <td class="task-assigned-user">
                                <select class="form-control update-assigned-user" data-task-id="{{ $task->id }}"
                                    {{ $userRole == 3 ? 'disabled' : '' }}>
                                    @foreach ($project->users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $task->assigned_user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-secondary view-task-btn" data-bs-toggle="modal"
                                    data-bs-target="#viewTaskModal" data-task-id="{{ $task->id }}">Chi tiết</button>
                            </td>
                            <td>
                                @if (Auth::user()->role == 1 || Auth::user()->role == 2)
                                    <button type="button" class="btn btn-info view-reports-btn"
                                        data-task-id="{{ $task->id }}" {{ $hasReports ? '' : 'disabled' }}>
                                        <i class="bi bi-sticky"></i>
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Modal for creating a new task -->
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTaskModalLabel">Tạo Công Việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <div class="form-group">
                            <label for="name">Tên công việc</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Trạng thái</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="0">Mới</option>
                                <option value="1">Đang làm</option>
                                <option value="2">Hoàn thành</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="due_date">Ngày hoàn thành</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="assigned_user_id">Người thực hiện</label>
                            <select name="assigned_user_id" id="assigned_user_id" class="form-control" required>
                                @foreach ($project->users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="attachments">Đính kèm</label>
                            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
                        </div>
                        <button type="submit" class="btn btn-primary">Tạo Công Việc</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing and editing a task -->
    <div class="modal fade" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 66.66%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewTaskModalLabel">Chi tiết Công Việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="viewTaskForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="task_id" id="view_task_id">
                                <div class="form-group">
                                    <label for="view_name">Tên công việc</label>
                                    <input type="text" name="name" id="view_name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="view_description">Mô tả</label>
                                    <textarea name="description" id="view_description" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="view_status">Trạng thái</label>
                                    <select name="status" id="view_status" class="form-control" required>
                                        <option value="0">Mới</option>
                                        <option value="1">Đang làm</option>
                                        <option value="2">Hoàn thành</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="view_due_date">Ngày hoàn thành</label>
                                    <input type="date" name="due_date" id="view_due_date" class="form-control"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="view_assigned_user_id">Người thực hiện</label>
                                    <select name="assigned_user_id" id="view_assigned_user_id" class="form-control"
                                        required>
                                        @foreach ($project->users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Files hiện tại:</label>
                                    <div id="current_attachments">
                                        <!-- File attachments will be loaded here by JavaScript -->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="view_attachments">Đính kèm mới</label>
                                    <input type="file" name="attachments[]" id="view_attachments"
                                        class="form-control" multiple>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        @if ($userRole == 3)
                            <!-- Kiểm tra role của người dùng -->
                            <button type="button" class="btn btn-warning" id="reportTaskButton">Báo cáo công
                                việc</button>
                        @endif
                    </form>
                    <hr>
                    <div id="comments-section">
                        <h5>Bình luận</h5>
                        <div id="comments-list">
                            <!-- Comments will be loaded here by JavaScript -->
                        </div>
                        <div class="form-group mt-3">
                            <label for="comment-content">Viết bình luận</label>
                            <textarea id="comment-content" class="form-control" rows="3"></textarea>
                            <button type="button" class="btn btn-primary mt-2" id="submit-comment">Gửi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing reports -->
    <div class="modal fade" id="viewReportsModal" tabindex="-1" aria-labelledby="viewReportsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewReportsModalLabel">Báo cáo Công Việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reports-container">
                        <!-- Reports will be loaded here by JavaScript -->
                    </div>
                    <button type="button" class="btn btn-primary" id="markAsReadButton">Đánh dấu đã đọc</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for reporting task -->
    <div class="modal fade" id="reportTaskModal" tabindex="-1" aria-labelledby="reportTaskModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportTaskModalLabel">Báo cáo Công việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportTaskForm" method="POST" action="{{ route('reports.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="user_id">Người dùng</label>
                            <input type="text" name="user_id" id="user_id" class="form-control"
                                value="{{ Auth::user()->name }}" readonly>
                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        </div>
                        <div class="form-group">
                            <label for="task_id">Tên công việc</label>
                            <input type="text" name="task_name" id="task_name" class="form-control" readonly>
                            <input type="hidden" name="task_id" id="task_id">
                        </div>
                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi báo cáo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- <script>
    function filterTasks() {
        const status = document.getElementById('status').value;
        window.location.href = `{{ route('tasks.index', ['project' => $project->id]) }}?status=${status}`;
    }

    $(document).ready(function() {
        function updateTask(taskId, url, data, reload = true) {
            $.ajax({
                url: url,
                method: 'PUT',
                data: data,
                success: function(response) {
                    console.log('Task updated successfully');
                    if (reload) location.reload();
                },
                error: function(response) {
                    console.log('Error updating task');
                }
            });
        }

        $('.view-task-btn').on('click', function() {
            const taskId = $(this).data('task-id');
            $.ajax({
                url: `/tasks/${taskId}`,
                method: 'GET',
                success: function(response) {
                    $('#view_task_id').val(response.task.id);
                    $('#view_name').val(response.task.name);
                    $('#view_description').val(response.task.description);
                    $('#view_status').val(response.task.status);
                    $('#view_due_date').val(response.task.due_date);
                    $('#view_assigned_user_id').val(response.task.assigned_user_id);

                    let attachmentsHtml = '';
                    response.task.attachments.forEach(attachment => {
                        let viewUrl = `/tasks/${taskId}/attachments/${attachment.id}/view`;
                        attachmentsHtml += `
                            <div class="col-md-4 mt-3">
                                <div class="card h-100">
                                    <div class="card-body d-flex flex-column align-items-center">
                                        ${getAttachmentPreview(attachment)}
                                        <div class="mt-auto w-100">
                                            <a href="/tasks/${taskId}/attachments/${attachment.id}/download" class="btn btn-success btn-sm w-100 mb-2"><i class="bi bi-cloud-download-fill"></i> Tải xuống</a>
                                            <a href="${viewUrl}" class="btn btn-primary btn-sm w-100 mb-2"><i class="bi bi-eye-fill"></i> Xem file</a>
                                            <button type="button" class="btn btn-danger btn-sm w-100 remove-attachment" data-attachment-id="${attachment.id}"><i class="bi bi-trash-fill"></i> Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    });
                    $('#current_attachments').html('<div class="row">' + attachmentsHtml + '</div>');

                    loadComments(taskId);

                    // Check user role and disable form elements if role is 3
                    if (response.userRole == 3) {
                        $('#viewTaskForm input, #viewTaskForm select, #viewTaskForm textarea').attr('disabled', true);
                        $('#viewTaskForm button[type="submit"]').attr('disabled', true);
                        $('#reportTaskButton').show(); // Hiển thị nút báo cáo công việc
                    } else {
                        $('#reportTaskButton').hide(); // Ẩn nút báo cáo công việc cho các role khác
                    }

                    $('#viewTaskModal').modal('show');
                },
                error: function(response) {
                    alert('Error fetching task details');
                }
            });
        });

        // Xử lý sự kiện nhấn nút "Báo cáo công việc"
        $('#reportTaskButton').on('click', function() {
            const taskId = $('#view_task_id').val();
            const taskName = $('#view_name').val();
            $('#task_id').val(taskId);
            $('#task_name').val(taskName);
            $('#reportTaskModal').modal('show'); // Hiển thị modal báo cáo công việc
        });

        function getAttachmentPreview(attachment) {
            let filename = attachment.filename;
            let truncatedFilename = filename.length > 15 ? filename.substring(0, 15) + '...' : filename;
            let uploadedBy = attachment.user ? attachment.user.name : 'Unknown';

            return `
                <div style="text-align: center;">
                    <i class="bi ${getFileIcon(attachment.filetype)}" style="font-size: 2rem;"></i>
                    <p title="${filename}" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; max-width: 150px; vertical-align: top;">${truncatedFilename}</p>
                    <p style="font-weight: bold; margin-top: 0;">Uploaded by: <span style="color: red;">${uploadedBy}</span></p>
                </div>
            `;
        }

        function getFileIcon(filetype) {
            if (filetype.startsWith('image/')) {
                return 'bi-file-earmark-image-fill';
            } else if (filetype === 'application/pdf') {
                return 'bi-file-earmark-pdf-fill';
            } else if (filetype.startsWith('application/vnd.ms-excel') || filetype.startsWith('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')) {
                return 'bi-file-earmark-excel';
            } else {
                return 'bi-file-earmark-font';
            }
        }

        $(document).on('change', '.update-status', function() {
            const taskId = $(this).data('task-id');
            const status = $(this).val();
            $.ajax({
                url: `/tasks/${taskId}/status`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                },
                success: function(response) {
                    console.log('Status updated successfully');
                    location.reload();
                },
                error: function(response) {
                    console.log('Error updating status');
                }
            });
        });

        $(document).on('change', '.update-due-date', function() {
            const taskId = $(this).data('task-id');
            const dueDate = $(this).val();
            $.ajax({
                url: `/tasks/${taskId}/due-date`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    due_date: dueDate,
                },
                success: function(response) {
                    console.log('Due date updated successfully');
                    location.reload();
                },
                error: function(response) {
                    console.log('Error updating due date');
                }
            });
        });

        $(document).on('change', '.update-assigned-user', function() {
            const taskId = $(this).data('task-id');
            const assignedUserId = $(this).val();
            $.ajax({
                url: `/tasks/${taskId}/assigned-user`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    assigned_user_id: assignedUserId,
                },
                success: function(response) {
                    console.log('Assigned user updated successfully');
                    location.reload();
                },
                error: function(response) {
                    console.log('Error updating assigned user');
                }
            });
        });

        $(document).on('click', '.remove-attachment', function() {
            const attachmentId = $(this).data('attachment-id');
            const taskId = $('#view_task_id').val();
            const attachmentElement = $(this).closest('.card');

            $.ajax({
                url: `/tasks/${taskId}/attachments/${attachmentId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    attachmentElement.remove();
                    console.log('Attachment removed successfully');
                },
                error: function(response) {
                    console.log('Error removing attachment');
                }
            });
        });

        $('#viewTaskForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const taskId = $('#view_task_id').val();
            const formData = new FormData(form[0]);

            $.ajax({
                url: `/tasks/${taskId}`,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert('Task updated successfully');
                    location.reload();
                },
                error: function(response) {
                    alert('Error updating task');
                }
            });
        });

        function loadComments(taskId) {
            $.ajax({
                url: `/tasks/${taskId}/comments`,
                method: 'GET',
                success: function(comments) {
                    let commentsHtml = '';
                    comments.forEach(comment => {
                        commentsHtml += `<div class="comment">
                            <strong>${comment.user.name}</strong>: ${comment.content}
                        </div>`;
                    });
                    $('#comments-list').html(commentsHtml);
                },
                error: function(response) {
                    console.log('Error fetching comments');
                }
            });
        }

        $('#submit-comment').on('click', function() {
            const taskId = $('#view_task_id').val();
            const content = $('#comment-content').val();
            $.ajax({
                url: `/tasks/${taskId}/comments`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    content: content
                },
                success: function(comment) {
                    $('#comments-list').append(`<div class="comment">
                        <strong>${comment.user.name}</strong>: ${comment.content}
                    </div>`);
                    $('#comment-content').val('');
                },
                error: function(response) {
                    console.log('Error submitting comment');
                }
            });
        });

        $('.view-reports-btn').on('click', function() {
            const taskId = $(this).data('task-id');
            $.ajax({
                url: `/tasks/${taskId}/reports`,
                method: 'GET',
                success: function(reports) {
                    let reportsHtml = '';
                    reports.forEach(report => {
                        reportsHtml += `
                            <div class="report">
                                <strong>${report.user.name}</strong>: ${report.description}
                                <p>${new Date(report.created_at).toLocaleString()}</p>
                            </div>
                            <hr>
                        `;
                    });
                    $('#reports-container').html(reportsHtml);
                    $('#viewReportsModal').modal('show');
                },
                error: function(response) {
                    alert('Error fetching reports');
                }
            });
        });

        $('#markAsReadButton').on('click', function() {
            const taskId = $('.view-reports-btn').data('task-id');
            
            alert(taskId);


            // $.ajax({
            //     url: `/tasks/${taskId}/mark-reports-as-read`,
            //     method: 'PUT',
            //     data: {
            //         _token: '{{ csrf_token() }}'
            //     },
            //     success: function(response) {
            //         $('.view-reports-btn[data-task-id="' + taskId + '"]').closest('tr').find('.bi-app-indicator').removeClass('text-danger').addClass('bi-stars text-success');
            //         $('#viewReportsModal').modal('hide');
            //     },
            //     error: function(response) {
            //         alert('Error marking reports as read');
            //     }
            // });
        });
    });
</script> --}}

    <script>
        function filterTasks() {
            const status = document.getElementById('status').value;
            window.location.href = `{{ route('tasks.index', ['project' => $project->id]) }}?status=${status}`;
        }

        $(document).ready(function() {
            let currentTaskId = null;

            function updateTask(taskId, url, data, reload = true) {
                $.ajax({
                    url: url,
                    method: 'PUT',
                    data: data,
                    success: function(response) {
                        console.log('Task updated successfully');
                        if (reload) location.reload();
                    },
                    error: function(response) {
                        console.log('Error updating task');
                    }
                });
            }

            $('.view-task-btn').on('click', function() {
                const taskId = $(this).data('task-id');
                currentTaskId = taskId; // Cập nhật currentTaskId
                $.ajax({
                    url: `/tasks/${taskId}`,
                    method: 'GET',
                    success: function(response) {
                        $('#view_task_id').val(response.task.id);
                        $('#view_name').val(response.task.name);
                        $('#view_description').val(response.task.description);
                        $('#view_status').val(response.task.status);
                        $('#view_due_date').val(response.task.due_date);
                        $('#view_assigned_user_id').val(response.task.assigned_user_id);

                        let attachmentsHtml = '';
                        response.task.attachments.forEach(attachment => {
                            let viewUrl =
                                `/tasks/${taskId}/attachments/${attachment.id}/view`;
                            attachmentsHtml += `
                            <div class="col-md-4 mt-3">
                                <div class="card h-100">
                                    <div class="card-body d-flex flex-column align-items-center">
                                        ${getAttachmentPreview(attachment)}
                                        <div class="mt-auto w-100">
                                            <a href="/tasks/${taskId}/attachments/${attachment.id}/download" class="btn btn-success btn-sm w-100 mb-2"><i class="bi bi-cloud-download-fill"></i> Tải xuống</a>
                                            <a href="${viewUrl}" class="btn btn-primary btn-sm w-100 mb-2"><i class="bi bi-eye-fill"></i> Xem file</a>
                                            <button type="button" class="btn btn-danger btn-sm w-100 remove-attachment" data-attachment-id="${attachment.id}"><i class="bi bi-trash-fill"></i> Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        });
                        $('#current_attachments').html('<div class="row">' + attachmentsHtml +
                            '</div>');

                        loadComments(taskId);

                        // Check user role and disable form elements if role is 3
                        if (response.userRole == 3) {
                            $('#viewTaskForm input, #viewTaskForm select, #viewTaskForm textarea')
                                .attr('disabled', true);
                            $('#viewTaskForm button[type="submit"]').attr('disabled', true);
                            $('#reportTaskButton').show(); // Hiển thị nút báo cáo công việc
                        } else {
                            $('#reportTaskButton')
                        .hide(); // Ẩn nút báo cáo công việc cho các role khác
                        }

                        $('#viewTaskModal').modal('show');
                    },
                    error: function(response) {
                        alert('Error fetching task details');
                    }
                });
            });

            // Xử lý sự kiện nhấn nút "Báo cáo công việc"
            $('#reportTaskButton').on('click', function() {
                const taskId = $('#view_task_id').val();
                const taskName = $('#view_name').val();
                $('#task_id').val(taskId);
                $('#task_name').val(taskName);
                $('#reportTaskModal').modal('show'); // Hiển thị modal báo cáo công việc
            });

            function getAttachmentPreview(attachment) {
                let filename = attachment.filename;
                let truncatedFilename = filename.length > 15 ? filename.substring(0, 15) + '...' : filename;
                let uploadedBy = attachment.user ? attachment.user.name : 'Unknown';

                return `
                <div style="text-align: center;">
                    <i class="bi ${getFileIcon(attachment.filetype)}" style="font-size: 2rem;"></i>
                    <p title="${filename}" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; max-width: 150px; vertical-align: top;">${truncatedFilename}</p>
                    <p style="font-weight: bold; margin-top: 0;">Uploaded by: <span style="color: red;">${uploadedBy}</span></p>
                </div>
            `;
            }

            function getFileIcon(filetype) {
                if (filetype.startsWith('image/')) {
                    return 'bi-file-earmark-image-fill';
                } else if (filetype === 'application/pdf') {
                    return 'bi-file-earmark-pdf-fill';
                } else if (filetype.startsWith('application/vnd.ms-excel') || filetype.startsWith(
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')) {
                    return 'bi-file-earmark-excel';
                } else {
                    return 'bi-file-earmark-font';
                }
            }

            $(document).on('change', '.update-status', function() {
                const taskId = $(this).data('task-id');
                const status = $(this).val();
                $.ajax({
                    url: `/tasks/${taskId}/status`,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status,
                    },
                    success: function(response) {
                        console.log('Status updated successfully');
                        location.reload();
                    },
                    error: function(response) {
                        console.log('Error updating status');
                    }
                });
            });

            $(document).on('change', '.update-due-date', function() {
                const taskId = $(this).data('task-id');
                const dueDate = $(this).val();
                $.ajax({
                    url: `/tasks/${taskId}/due-date`,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        due_date: dueDate,
                    },
                    success: function(response) {
                        console.log('Due date updated successfully');
                        location.reload();
                    },
                    error: function(response) {
                        console.log('Error updating due date');
                    }
                });
            });

            $(document).on('change', '.update-assigned-user', function() {
                const taskId = $(this).data('task-id');
                const assignedUserId = $(this).val();
                $.ajax({
                    url: `/tasks/${taskId}/assigned-user`,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        assigned_user_id: assignedUserId,
                    },
                    success: function(response) {
                        console.log('Assigned user updated successfully');
                        location.reload();
                    },
                    error: function(response) {
                        console.log('Error updating assigned user');
                    }
                });
            });

            $(document).on('click', '.remove-attachment', function() {
                const attachmentId = $(this).data('attachment-id');
                const taskId = $('#view_task_id').val();
                const attachmentElement = $(this).closest('.card');

                $.ajax({
                    url: `/tasks/${taskId}/attachments/${attachmentId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        attachmentElement.remove();
                        console.log('Attachment removed successfully');
                    },
                    error: function(response) {
                        console.log('Error removing attachment');
                    }
                });
            });

            $('#viewTaskForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const taskId = $('#view_task_id').val();
                const formData = new FormData(form[0]);

                $.ajax({
                    url: `/tasks/${taskId}`,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        alert('Task updated successfully');
                        location.reload();
                    },
                    error: function(response) {
                        alert('Error updating task');
                    }
                });
            });

            function loadComments(taskId) {
                $.ajax({
                    url: `/tasks/${taskId}/comments`,
                    method: 'GET',
                    success: function(comments) {
                        let commentsHtml = '';
                        comments.forEach(comment => {
                            commentsHtml += `<div class="comment">
                            <strong>${comment.user.name}</strong>: ${comment.content}
                        </div>`;
                        });
                        $('#comments-list').html(commentsHtml);
                    },
                    error: function(response) {
                        console.log('Error fetching comments');
                    }
                });
            }

            $('#submit-comment').on('click', function() {
                const taskId = $('#view_task_id').val();
                const content = $('#comment-content').val();
                $.ajax({
                    url: `/tasks/${taskId}/comments`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        content: content
                    },
                    success: function(comment) {
                        $('#comments-list').append(`<div class="comment">
                        <strong>${comment.user.name}</strong>: ${comment.content}
                    </div>`);
                        $('#comment-content').val('');
                    },
                    error: function(response) {
                        console.log('Error submitting comment');
                    }
                });
            });

            $('.view-reports-btn').on('click', function() {
                const taskId = $(this).data('task-id');
                currentTaskId = taskId; // Cập nhật currentTaskId khi mở modal báo cáo
                $.ajax({
                    url: `/tasks/${taskId}/reports`,
                    method: 'GET',
                    success: function(reports) {
                        let reportsHtml = '';
                        reports.forEach(report => {
                            reportsHtml += `
                            <div class="report">
                                <strong>${report.user.name}</strong>: ${report.description}
                                <p>${new Date(report.created_at).toLocaleString()}</p>
                            </div>
                            <hr>
                        `;
                        });
                        $('#reports-container').html(reportsHtml);
                        $('#viewReportsModal').modal('show');
                    },
                    error: function(response) {
                        alert('Error fetching reports');
                    }
                });
            });

            $('#markAsReadButton').on('click', function() {
                const taskId = currentTaskId; // Sử dụng currentTaskId thay vì tìm từ .view-reports-btn
                $.ajax({
                    url: `/tasks/${taskId}/mark-reports-as-read`,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('.view-reports-btn[data-task-id="' + taskId + '"]').closest('tr')
                            .find('.bi-app-indicator').removeClass('text-danger').addClass(
                                'bi-stars text-success');
                        $('#viewReportsModal').modal('hide');
                    },
                    error: function(response) {
                        alert('Error marking reports as read');
                    }
                });
            });
        });
    </script>
@endsection
