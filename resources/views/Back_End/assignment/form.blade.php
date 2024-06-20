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
                        <h4>CHỈ ĐỊNH HUẤN LUYỆN VIÊN</h4>
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
                        {{-- @if (isset($userEdit)) --}}
                        {{ Form::open(['route' => 'profile.assignmentupdate', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        {{-- @else
                            {{ Form::open(['route' => 'profile.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        @endif --}}
                        {{-- {!! Form::hidden('id', isset($userEdit) ? $userEdit->id : '', []) !!} --}}
                        {!! Form::hidden('client_id', isset($client) ? $client->id : '', []) !!}


                        <div class="form-group mb-3">
                            {!! Form::label('coach_id', 'Chỉ định', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::select('coach_id', $coach, isset($temp) ? $temp->coach_id : "", ['class' => 'form-control']) !!}
                        </div>

                        {{-- <div class="form-group">
                            {!! Form::label('coach_id', 'Chọn người huấn luyện', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::select(
                                'coach_id',
                                $coaches,
                                old('coach_id', isset($coachingSessionEdit) ? $coachingSessionEdit->coach_id : null),
                                ['class' => 'form-control']
                            ) !!}
                        </div>  --}}

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

    <script>
        $(document).ready(function() {
            $('#imageInput').change(function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endsection
