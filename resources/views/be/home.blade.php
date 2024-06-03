@extends('layouts.be')

@section('content')
<div class="pagetitle d-flex align-items-center justify-content-between">
  <h1>Các không gian làm việc</h1>
  <div class="d-flex align-items-center justify-content-between">
    @if (Auth::user()->role == 1)
      {{-- <a href="{{ route('units.create') }}">
        <button type="button" class="btn btn-primary me-2">Thêm không gian làm việc</button>
      </a> --}}
    @endif
  </div>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">
    <!-- Left side columns -->
    <div class="col-lg-12">
      <div class="row">
      
        @foreach ($units as $unit)
          <!-- Recent Sales -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">

              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                  <h5 class="card-title">{{ $unit->name }}</h5>
                  <div class="d-flex">
                    <a href="">
                      <button type="button" class="btn btn-primary me-2">Xem chi tiết</button>
                    </a>
                    @if (Auth::user()->role == 1)
                      <a href="{{ route('units.view-users', ["unit" => $unit->id]) }}">
                        <button type="button" class="btn btn-primary">Thành viên ({{ $unit->users->count() }})</button>
                      </a>
                    @endif
                  </div>
                </div>

                <!-- Display projects for the unit -->
                @if ($unit->projects->isEmpty())
                  <p>Chưa có dự án nào được gán cho bạn.</p>
                @else
                  <div class="row mt-3">
                    @foreach ($unit->projects as $project)
                      <div class="col-xxl-4 col-xl-12">
                        <div class="card info-card customers-card">

                          <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                          </div>

                          <div class="card-body">
                            <h5 class="card-title project-title">{{ $project->name }}</h5>

                            <div class="d-flex align-items-center">
                              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-people"></i>
                              </div>
                              <div class="ps-3">
                                <h6>Số người tham gia: {{ $project->users->count() }}</h6>
                                <span class="text-muted small pt-2 ps-1">Người tạo: {{ $project->creator->name }}</span>
                                <br>
                                <span class="text-muted small pt-2 ps-1">Số lượng công việc: {{ $project->tasks->count() }}</span>
                              </div>
                            </div>
                            <a href="{{ route('tasks.index', ['project' => $project->id]) }}">
                                <button type="button" class="btn btn-primary mt-3">Xem công việc</button>
                            </a>
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addUserModal" data-project-id="{{ $project->id }}">
                                Thêm người dùng
                            </button>
                            <button type="button" class="btn btn-secondary mt-3" data-bs-toggle="modal" data-bs-target="#viewUsersModal" data-project-id="{{ $project->id }}">
                                Xem người dùng
                            </button>
                          </div>

                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
                <!-- End display projects for the unit -->

              </div>

            </div>
          </div><!-- End Recent Sales -->
        @endforeach

      </div>
    </div><!-- End Left side columns -->

  </div>
</section>

<!-- Modal for adding users -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Thêm người dùng vào dự án</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    @csrf
                    <div class="form-group">
                        <label for="users">Chọn người dùng:</label>
                        <select name="user_ids[]" id="users" class="form-control" multiple>
                        </select>
                    </div>
                    <input type="hidden" id="project_id" name="project_id">
                    <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for viewing users -->
<div class="modal fade" id="viewUsersModal" tabindex="-1" aria-labelledby="viewUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUsersModalLabel">Người dùng tham gia dự án</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="projectUsersTable">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#addUserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var projectId = button.data('project-id');
        var modal = $(this);
        modal.find('.modal-body #project_id').val(projectId);

        $.ajax({
            url: "{{ route('projects.create-users', ':id') }}".replace(':id', projectId),
            type: 'GET',
            success: function (data) {
                var select = $('#users');
                select.empty();
                $.each(data, function (key, value) {
                    select.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            },
            error: function (error) {
                alert('Có lỗi xảy ra, vui lòng thử lại sau.');
            }
        });
    });

    $('#addUserForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var url = "{{ route('projects.add-users', ':id') }}".replace(':id', form.find('#project_id').val());

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            success: function (response) {
                location.reload();
            },
            error: function (response) {
                alert('Có lỗi xảy ra, vui lòng thử lại sau.');
            }
        });
    });

    $('#viewUsersModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var projectId = button.data('project-id');
        var modal = $(this);

        $.ajax({
            url: "{{ route('projects.get-users', ':id') }}".replace(':id', projectId),
            type: 'GET',
            success: function (data) {
                var tableBody = $('#projectUsersTable');
                tableBody.empty();
                $.each(data, function (index, user) {
                    var actionButton = user.role == 1 ? 
                        '<button class="btn btn-danger btn-sm delete disabled" title="Delete" disabled><i class="material-icons">delete</i></button>' :
                        '<form action="{{ route('projects.remove-user', [":project_id", ":user_id"]) }}" method="POST" class="d-inline">' +
                            '@csrf' +
                            '@method('POST')' +
                            '<button type="submit" class="btn btn-danger btn-sm delete" title="Delete" onclick="return confirm(\'Bạn có chắc muốn xóa người dùng ' + user.name + ' khỏi dự án?\')"><i class="material-icons">delete</i></button>' +
                        '</form>';
                    
                    tableBody.append( 
                        '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + user.name + '</td>' +
                            '<td>' + user.role + '</td>' +
                            '<td>' + actionButton.replace(':project_id', projectId).replace(':user_id', user.id) + '</td>' +
                        '</tr>'
                    );
                });
            },
            error: function (error) {
                alert('Có lỗi xảy ra, vui lòng thử lại sau.');
            }
        });
    });
</script>
@endsection
