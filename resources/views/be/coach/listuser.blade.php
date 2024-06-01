{{-- resources/views/be/workout_schedules/index.blade.php --}}

@extends('layouts.be')

@section('content')
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>DANH SÁCH<b> LỊCH TẬP LUYỆN</b></h2>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('workout_schedules.create') }}" class="btn btn-success" data-toggle="modal">
                                <i class="material-icons">&#xE147;</i> <span>Thêm Lịch tập luyện</span>
                            </a>
                            <a href="{{ route('meal_schedules.create') }}" class="btn btn-success" data-toggle="modal">
                                <i class="material-icons">&#xE147;</i> <span>Thêm Lịch ăn</span>
                            </a>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Khách hàng</th>
                            <th>Lịch tập</th>
                            <th>Lịch ăn</th>
                            {{-- <th>Quản lý</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trainedUsers as $schedule)
                            <tr>
                                {{-- {{ $schedule }} --}}
                                <td>{{ $schedule->id }}</td>
                                <td>{{ $schedule->name }}</td>
                                <td>  <a href ="{{ url('/workout-schedules/displaySchedule') }}/{{ $schedule->id }}" class="delete" data-toggle="modal">
                                   Lịch tập
                                </a></td>
                                <td>  <a href="{{ url('/meal-schedules/displaySchedule') }}/{{ $schedule->id }}" class="delete" data-toggle="modal">
                                    Lịch ăn
                                </a></td>
                                {{-- <td class="">
                                    <a href="" class="edit"
                                        data-toggle="modal">đấ
                                        <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                                    </a>
                                    <a href="#deleteScheduleModal" class="delete" data-toggle="modal">
                                        <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
                                    </a>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
