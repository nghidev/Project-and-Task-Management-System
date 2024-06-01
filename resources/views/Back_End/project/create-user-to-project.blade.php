@extends('layouts.be')

@section('content')
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-8">
                            <h2>Thêm người dùng vào dự án <b>{{ $project->name }}</b></h2>
                        </div>
                    </div>
                </div>
                <form action="{{ route('projects.add-users', ['project' => $project->id]) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="users">Chọn người dùng:</label>
                        <select name="user_ids[]" id="users" class="form-control" multiple>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                </form>
            </div>
        </div>
    </div>
@endsection
