{{-- resources/views/users/index.blade.php --}}
@extends('layouts.be')

@section('content')
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>CHỈ ĐỊNH<b> HUẤN LUYỆN VIÊN</b></h2>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('profile.create') }}" class="btn btn-success" data-toggle="modal">
                                <i class="material-icons">&#xE147;</i> <span>Thêm hồ sơ</span>
                            </a>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Tên</th>
                            <th>Vai trò</th>
                            {{-- <th>Huấn luyện viên hưỡng dẫn</th>  --}}
                            <th>Quản lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $idincrease = 1;
                        @endphp
                        @foreach ($users as $user)
                            {{-- Chỉ hiển thị người dùng có vai trò là 3 --}}
                                <tr>
                                    <td>{{ $idincrease++ }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        @if ($user->role == 1)
                                            Quản trị
                                        @elseif ($user->role == 2)
                                            Huấn luyện viên
                                        @else
                                            Khách hàng
                                        @endif
                                   
                                    <td class="">
                                        <a href="{{ url('/profile/assignment') }}/{{ $user->id }}" class="edit"
                                            data-toggle="modal">
                                            <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                                        </a>
                                        <a href="#deleteEmployeeModal" class="delete" data-toggle="modal">
                                            <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
                                        </a>
                                    </td>
                                </tr>
                      
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
