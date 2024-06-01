@extends('layouts.be')

@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="fw-bold">Thêm người dùng vào không gian làm việc</h4>
                    </div>
                    <div class="card-body">
                        @if ($users->isEmpty())
                            <div class="alert alert-info">
                                Tất cả người dùng đã được thêm vào unit.
                            </div>
                        @else
                            <form action="{{ route('units.add-users', ['unit' => $unit]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="unitId" value="{{ $unit->id }}">
                                <div class="form-group">
                                    <label for="user_ids">Chọn người dùng</label>
                                    <div class="list-group">
                                        @foreach ($users as $user)
                                            @unless ($unit->users->contains($user))
                                                <label class="list-group-item">
                                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                                        class="form-check-input">
                                                    {{ $user->name }}
                                                </label>
                                            @endunless
                                        @endforeach
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection