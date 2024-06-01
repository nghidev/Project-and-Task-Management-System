{{-- resources/views/be/workout_schedules/form.blade.php --}}

@extends('layouts.be')

@section('SummernoteCdn')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
    <h1>Tạo Lịch Tập Luyện</h1>
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header fw-bold">
                        <h4>QUẢN LÝ LỊCH TẬP LUYỆN</h4>
                    </div>
                    <div class="card-body">
                        {{-- Hiển thị lỗi nếu có --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        {{-- Form tạo lịch tập luyện --}}
                        @if (isset($workoutScheduleEdit))
                            {{ Form::open(['route' => ['workout_schedules.update'], 'method' => 'post']) }}
                        @else
                            {{ Form::open(['route' => 'workout_schedules.store', 'method' => 'post']) }}
                        @endif
                        {!! Form::hidden('id', isset($workoutScheduleEdit) ? $client : '', []) !!}
                        {!! Form::hidden('date', isset($workoutScheduleEdit) ? $dateSelected : '', []) !!}
                        <div class="form-group mb-3">
                            {!! Form::label('client_id', 'ID Khách hàng', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::select(
                                'client_id',
                                $clients,
                                old('client_id', isset($workoutScheduleEdit) ? $client : ''),
                                ['class' => 'form-control', 'placeholder' => 'Chọn Khách hàng'],
                            ) !!}
                        </div>
                        {{-- <div class="form-group mb-3">
                            {!! Form::label('muscle_group_id', 'Nhóm Cơ', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::select(
                                'muscle_group_id',
                                $muscles,
                                old('muscle_group_id', isset($workoutScheduleEdit) ? $workoutScheduleEdit->muscle_group_id : ''),
                                ['class' => 'form-control', 'placeholder' => 'Chọn Nhóm Cơ'],
                            ) !!}
                        </div> --}}

                        {{-- <div class="form-group mb-3">
                            {!! Form::label('exercise_id', 'Bài Tập', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::select(
                                'exercise_id',
                                $exercises,
                                old('exercise_id', isset($workoutScheduleEdit) ? $workoutScheduleEdit->exercise_id : ''),
                                ['class' => 'form-control', 'placeholder' => 'Chọn Bài Tập'],
                            ) !!}
                        </div> --}}

                        {{-- <div class="form-group mb-3">
                            {!! Form::label('muscle_group_id', 'Nhóm Cơ', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::select('muscle_group_id', $muscles, old('muscle_group_id', isset($workoutScheduleEdit) ? $workoutScheduleEdit->muscle_group_id : ''), ['class' => 'form-control', 'placeholder' => 'Chọn Nhóm Cơ', 'id' => 'muscle_group_id']) !!}
                        </div>
                        
                        <div class="form-group mb-3">
                            {!! Form::label('exercise_id', 'Bài Tập', ['class' => 'form-label fw-bold']) !!}
                            <div class="form-check" id="exercise_id_container">
                                <!-- Nội dung checkbox sẽ được thêm vào đây -->
                            </div>
                        </div> --}}

                        <div class="form-group mb-3">
                            {!! Form::label('exercise_id', 'Bài Tập', ['class' => 'form-label fw-bold']) !!}
                            <div class="form-check">
                                @foreach($exercises as $exercise)
                                    <div class="form-check">
                                        {!! Form::checkbox('exercise_ids[]', $exercise->id, isset($exercisesSelected[$exercise->id]), ['class' => 'form-check-input', 'id' => 'exercise_id_' . $exercise->id]) !!}
                                        {!! Form::label('exercise_id_' . $exercise->id, $exercise->name, ['class' => 'form-check-label']) !!}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        


                     
                        <div class="form-group mb-3">
                            {!! Form::label('event_date', 'Ngày Tập luyện', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::date('event_date', old('event_date', isset($workoutScheduleEdit) ? $dateSelected : ''), ['class' => 'form-control']) !!}

                        </div>
                        <div class="form-group d-flex justify-content-end">
                            {!! Form::submit('Lưu', ['class' => 'btn btn-primary']) !!}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('SummernoteScript')
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        // $(document).ready(function() {
        //     $('#muscle_group_id').on('change', function() {
        //         var muscleGroupId = $(this).val();
        //         if (muscleGroupId) {
        //             $.ajax({
        //                 url: '/get-exercises/' + muscleGroupId,
        //                 type: 'GET',
        //                 dataType: 'json',
        //                 success: function(data) {
        //                     $('#exercise_id').empty();
        //                     if ($.isEmptyObject(data)) {
        //                         $('#exercise_id').append(
        //                             '<option value="">Không có bài tập</option>');
        //                     } else {
        //                         $.each(data, function(key, value) {
        //                             $('#exercise_id').append('<option value="' + key +
        //                                 '">' + value + '</option>');
        //                         });
        //                     }
        //                 }
        //             });
        //         } else {
        //             $('#exercise_id').empty();
        //         }
        //     });
        // });

        $(document).ready(function() {
        $('#muscle_group_id').on('change', function() {
            var muscleGroupId = $(this).val();
            if (muscleGroupId) {
                $.ajax({
                    url: '/get-exercises/' + muscleGroupId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#exercise_id_container').empty();
                        if ($.isEmptyObject(data)) {
                            $('#exercise_id_container').append('<p>Không có bài tập</p>');
                        } else {
                            $.each(data, function(key, value) {
                                var checkbox = '<div class="form-check">' +
                                    '<input type="checkbox" name="exercise_ids[]" value="' + key +
                                    '" class="form-check-input">' +
                                    '<label class="form-check-label">' + value + '</label>' +
                                    '</div>';
                                $('#exercise_id_container').append(checkbox);
                            });
                        }
                    }
                });
            } else {
                $('#exercise_id_container').empty();
            }
        });
    });
    </script>
@endsection
