{{-- unit/manage-users.blade.php --}}

@extends('layouts.be')

@section('content')
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-8">
                            <h2>Quản lý thành viên <b>{{ $unit->name }}</b></h2>
                        </div>
                        <div class="col-sm-4">
                            @if (auth()->user()->role != 2 && auth()->user()->role != 3)
                                <a href="{{ route('units.create-users', ['unit' => $unit->id]) }}">
                                    <button class="btn btn-secondary"><i class="material-icons"></i> <span>Add New User</span></button>
                                </a>
                            @else
                                <button class="btn btn-secondary disabled" disabled><i class="material-icons"></i> <span>Add New User</span></button>
                            @endif
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unit->users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->role }}</td>
                                <td>
                                    @if (auth()->user()->role == 2 || auth()->user()->role == 3)
                                        <button class="btn btn-danger btn-sm delete disabled" title="Delete" disabled>
                                            <i class="material-icons">delete</i>
                                        </button>
                                    @else
                                        <form action="{{ route('units.remove-user', ['unit' => $unit->id, 'user_id' => $user->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-danger btn-sm delete" title="Delete"
                                                onclick="return confirm('Bạn có chắc muốn xóa người dùng {{ $user->name }} khỏi không gian làm việc?')">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="clearfix">
                    <div class="hint-text">Showing <b>{{ $unit->users->count() }}</b> out of
                        <b>{{ $unit->users->count() }}</b> entries</div>
                    <ul class="pagination">
                        <li class="page-item disabled"><a href="#">Previous</a></li>
                        <li class="page-item"><a href="#" class="page-link">1</a></li>
                        <li class="page-item"><a href="#" class="page-link">2</a></li>
                        <li class="page-item active"><a href="#" class="page-link">3</a></li>
                        <li class="page-item"><a href="#" class="page-link">4</a></li>
                        <li class="page-item"><a href="#" class="page-link">5</a></li>
                        <li class="page-item"><a href="#" class="page-link">Next</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
