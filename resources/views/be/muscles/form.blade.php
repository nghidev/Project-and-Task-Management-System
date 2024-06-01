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
                        <h4>QUẢN LÝ {{ $Tilte }}</h4>
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
                        @if (isset($MuscleEdit))
                            {{ Form::open(['route' => 'muscle.update', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        @else
                            {{ Form::open(['route' => 'muscle.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        @endif
                        {!! Form::hidden('id', isset($MuscleEdit) ? $MuscleEdit->id : '', []) !!}
                        <div class="form-group mb-3">
                            {!! Form::label('name', 'Cơ bắp', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::text('name', old('name', isset($MuscleEdit) ? $MuscleEdit->name : ''), [
                                'placeholder' => 'Nhập vào nhóm cơ',
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                        @php
                            $options = [];
                            foreach ($MuscleGroup as $item) {
                                $options[$item->id] = $item->name;
                            }

                        @endphp

                        <div class="form-group mb-3">
                            {!! Form::label('name', 'Nhóm cơ', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::select('muscle_group_id', $options, isset($MuscleEdit) ? $MuscleEdit->muscleGroup->id : null, [
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                        <div class="form-group mb-3">
                            {!! Form::label('description', 'Mô tả', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::textarea('description', old('description', isset($MuscleEdit) ? $MuscleEdit->description : ''), [
                                'class' => 'form-control',
                                'id' => 'summernote',
                            ]) !!}
                        </div>
                        <div class="form-group mb-3">
                            {!! Form::label('image', 'Chọn Ảnh', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::file('image', ['class' => 'form-control', 'id' => 'imageInput']) !!}
                        </div>

                        @php
                            $pathimage = 'storage/muscles/';
                            $imagename = isset($MuscleEdit) ? $MuscleEdit->image : '';

                            $imageCombine = $pathimage . $imagename;
                        @endphp
                        <div class="form-group mb-3">
                            <img id="imagePreview" src="{{ asset($imageCombine) }}" alt="Ảnh đang chọn" 
                                style="display: {{ isset($MuscleEdit) ? 'display' : 'none' }}; max-width: 200px; max-height: 200px;" />
                            {{-- <img src="{{ asset('storage/muscles/1700852519.404572081_316563937830125_3856836233965715057_n.jpg') }}"
                                alt="Muscle Image"> --}}

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
