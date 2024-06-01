@extends('layouts.be')

@section('SummernoteCdn')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
    <h1>Tạo Lịch Ăn</h1>
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header fw-bold">
                        <h4>QUẢN LÝ LỊCH ĂN</h4>
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
                        {{-- Form tạo lịch ăn --}}
                        @if (isset($mealScheduleEdit))
                            {{ Form::model($mealScheduleEdit, ['route' => ['meal_schedules.update'], 'method' => 'post']) }}
                        @else
                            {{ Form::open(['route' => 'meal_schedules.store', 'method' => 'post']) }}
                        @endif
                        {!! Form::hidden('id', isset($mealScheduleEdit) ? $client : '', []) !!}
                        {!! Form::hidden('date', isset($mealScheduleEdit) ? $dateSelected : '', []) !!}
                        
                        <div class="form-group mb-3">
                            {!! Form::label('client_id', 'ID Khách hàng', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::select(
                                'client_id',
                                $clients,
                                old('client_id', isset($mealScheduleEdit) ? $client : ''),
                                ['class' => 'form-control', 'placeholder' => 'Chọn Khách hàng'],
                            ) !!}
                        </div>

                        @foreach ($foods as $food)
                        <div class="form-check">
                            {!! Form::checkbox('food_ids[]', $food->id, isset($foodIdsSelected) ? in_array($food->id, $foodIdsSelected):'', ['class' => 'form-check-input']) !!}
                            {!! Form::label('food_id_' . $food->id, $food->name, ['class' => 'form-check-label']) !!}
                        </div>
                    @endforeach
                    
                        
                        


                    


                        <div class="form-group mb-3">
                            {!! Form::label('event_date', 'Ngày Ăn', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::date('event_date', old('event_date', isset($mealScheduleEdit) ? $dateSelected : ''), ['class' => 'form-control']) !!}
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
    <!-- Thêm các đoạn mã JavaScript cần thiết cho chức năng lịch ăn -->
    {{-- <script>
        // Gọi AJAX để lấy danh sách món ăn tương ứng với khách hàng hoặc các chức năng khác nếu cần
    </script> --}}
@endsection
