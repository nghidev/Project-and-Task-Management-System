@extends('layouts.be')

@section('SummernoteCdn')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
{{-- <h1>Tạo không gian làm việc</h1> --}}
<div class="container mt-3">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header fw-bold">
                    
                    <h4>Thông tin không gian làm việc</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif 
                    {{ Form::open(['route' => 'units.store', 'method' => 'post']) }}
                    <div class="form-group mb-3">
                        {{ Form::label('name', 'Tên Đơn Vị', ['class' => 'form-label fw-bold']) }}
                        {{ Form::text('name', '', ['placeholder' => 'Nhập vào tên đơn vị', 'class' => 'form-control', 'required' => true]) }}
                    </div>
                    <div class="form-group mb-3">
                        {{ Form::label('description', 'Mô tả', ['class' => 'form-label fw-bold']) }}
                        {{ Form::textarea('description', '', ['class' => 'form-control', 'id' => 'summernote', 'required' => true]) }}
                    </div>
                    <div class="form-group mb-3">
                        {{ Form::label('create_by', 'Người Tạo', ['class' => 'form-label fw-bold']) }}
                        {{ Form::text('create_by', $currentUser->name, ['class' => 'form-control', 'readonly' => true]) }}
                        {{ Form::hidden('create_by_id', $currentUser->id) }} {{-- Lưu ID người tạo dưới dạng hidden --}}
                    </div>
                    <div class="form-group mb-3">
                        {{ Form::label('user_ids', 'Người tham gia', ['class' => 'form-label fw-bold']) }}
                        {{ Form::select('user_ids[]', $users->pluck('name', 'id'), null, ['class' => 'form-control', 'multiple' => true]) }}
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        {{ Form::submit('Lưu', ['class' => 'btn btn-primary']) }}
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
        placeholder: 'Nhập vào mô tả',
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
