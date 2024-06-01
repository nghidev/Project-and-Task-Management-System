@extends('layouts.be')

@section('SummernoteCdn')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
    <h1>Tạo Nhóm Cơ</h1>
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header fw-bold">
                        <h4>QUẢN LÝ NHÓM CƠ</h4>
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
                        {{-- Form tạo nhóm cơ --}}
                        @if (isset($muscleGroupEdit))
                            {{ Form::open(['route' => 'muscle_groups.update', 'method' => 'post']) }}
                        @else
                            {{ Form::open(['route' => 'muscle_groups.store', 'method' => 'post']) }}
                        @endif
                        {!! Form::hidden('id', isset($muscleGroupEdit) ? $muscleGroupEdit->id : '', []) !!}
                        <div class="form-group mb-3">
                            {!! Form::label('name', 'Nhóm cơ', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::text('name', old('name', isset($muscleGroupEdit) ? $muscleGroupEdit->name : ''), ['placeholder' => 'Nhập vào nhóm cơ', 'class' => 'form-control']) !!}
                        </div>
                        <div class="form-group mb-3">
                            {!! Form::label('description', 'Mô tả', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::textarea('description', old('description', isset($muscleGroupEdit) ? $muscleGroupEdit->description : ''), ['class' => 'form-control', 'id' => 'summernote']) !!}
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
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $('#summernote').summernote({
            placeholder: 'Nhập vào mô tả',
            tabsize: 2,
            height: 120,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    </script>
@endsection
