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
                        <h4>QUẢN LÝ MÓN ĂN</h4>
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
                        {{-- Form tạo thực phẩm --}}
                        @if (isset($FoodEdit))
                            {{ Form::model($FoodEdit, ['route' => ['food.update', $FoodEdit->id], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        @else
                            {{ Form::open(['route' => 'food.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        @endif
                        {!! Form::hidden('id', isset($FoodEdit) ? $FoodEdit->id : '', []) !!}

                        <div class="form-group mb-3">
                            {!! Form::label('name', 'Tên thực phẩm', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::text('name', old('name', $FoodEdit->name ?? ''), [
                                'placeholder' => 'Nhập tên thực phẩm',
                                'class' => 'form-control',
                            ]) !!}
                        </div>

                        <div class="form-group mb-3">
                            {!! Form::label('description', 'Định lượng', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::textarea('description', old('description', $FoodEdit->description ?? ''), [
                                'class' => 'form-control',
                                'id' => 'summernote',
                            ]) !!}
                        </div>

                        <div class="form-group mb-3">
                            {!! Form::label('calo', 'Calo', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::number('calo', old('calo', $FoodEdit->calo ?? ''), [
                                'placeholder' => 'Nhập số calo',
                                'class' => 'form-control',
                            ]) !!}
                        </div>

                        @foreach ($nutrients as $nutrient)
                            <div class="form-group mb-3">
                                {!! Form::label($nutrient->name, $nutrient->name, ['class' => 'form-label fw-bold']) !!}
                                {!! Form::text(
                                    $nutrient->name,
                                    old($nutrient->name, isset($FoodEdit) ? ($FoodEdit->nutrients->where('id', $nutrient->id)->first()->pivot->description ?? '') : ''),
                                    [
                                        'placeholder' => 'Nhập lượng định lượng ' . $nutrient->name,
                                        'class' => 'form-control',
                                    ]
                                ) !!}
                                
                            </div>
                        @endforeach




                      

                        <div class="form-group mb-3">
                            {!! Form::label('image', 'Chọn Ảnh', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::file('image', ['class' => 'form-control', 'id' => 'imageInput']) !!}
                        </div>

                        @php
                            $pathimage = 'storage/foods/';
                            $imagename = isset($FoodEdit) ? $FoodEdit->image : '';

                            $imageCombine = $pathimage . $imagename;
                        @endphp

                        <div class="form-group mb-3">
                            <img id="imagePreview" src="{{ asset($imageCombine) }}" alt="Ảnh đang chọn"
                                style="display: {{ isset($exerciseEdit) ? 'display' : 'none' }}; max-width: 200px; max-height: 200px;" />
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
