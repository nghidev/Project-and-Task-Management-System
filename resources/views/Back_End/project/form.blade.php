@extends('layouts.be')

@section('SummernoteCdn')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header fw-bold">
                    <h4>Thông tin Dự Án</h4>
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
                    {{ Form::open(['route' => 'projects.store', 'method' => 'post']) }}
                    <div class="form-group mb-3">
                        {{ Form::label('unit_id', 'Không gian làm việc', ['class' => 'form-label fw-bold']) }}
                        {{ Form::select('unit_id', $units->pluck('name', 'id'), null, ['placeholder' => 'Chọn không gian làm việc', 'class' => 'form-control', 'required' => true, 'id' => 'unit_id']) }}
                    </div>
                    <div class="form-group mb-3">
                        {{ Form::label('name', 'Tên Dự Án', ['class' => 'form-label fw-bold']) }}
                        {{ Form::text('name', '', ['placeholder' => 'Nhập vào tên dự án', 'class' => 'form-control', 'required' => true]) }}
                    </div>
                    <div class="form-group mb-3">
                        {{ Form::label('description', 'Mô tả', ['class' => 'form-label fw-bold']) }}
                        {{ Form::textarea('description', '', ['class' => 'form-control', 'id' => 'summernote', 'required' => true]) }}
                    </div>
                    <div class="form-group mb-3">
                        {{ Form::label('create_by', 'Người Tạo', ['class' => 'form-label fw-bold']) }}
                        {{ Form::text('create_by', $currentUser->name, ['class' => 'form-control', 'readonly' => true]) }}
                        {{ Form::hidden('create_by', $currentUser->id) }} {{-- Lưu ID người tạo dưới dạng hidden --}}
                    </div>
                    <div class="form-group mb-3">
                        {{ Form::label('user_ids', 'Người tham gia', ['class' => 'form-label fw-bold']) }}
                        <div id="user_ids">
                            {{-- Các checkbox cho người dùng sẽ được thêm ở đây bởi Ajax --}}
                        </div>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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

    // Ajax request to get users based on selected unit
    $('#unit_id').change(function () {
        var unitId = $(this).val();
        if (unitId) {
            $.ajax({
                url: '{{ url("get-users-by-unit") }}/' + unitId,
                type: 'GET',
                success: function (data) {
                    $('#user_ids').empty();
                    $.each(data, function (key, value) {
                        $('#user_ids').append(
                            '<div class="form-check">' +
                                '<input class="form-check-input" type="checkbox" name="user_ids[]" value="' + key + '" id="user_' + key + '">' +
                                '<label class="form-check-label" for="user_' + key + '">' + value + '</label>' +
                            '</div>'
                        );
                    });
                }
            });
        } else {
            $('#user_ids').empty();
        }
    });
</script>
@endsection
