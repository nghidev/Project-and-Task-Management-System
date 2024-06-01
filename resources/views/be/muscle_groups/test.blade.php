@extends('layouts.be')


@section('SummernoteCdn')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection


@section('content')

    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>DANH SÁCH<b> NHÓM CƠ</b></h2>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Tên</th>
                            <th>Slug</th>
                            <th>Mô tả</th>
                            <th>Quản lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($temp as $item)
                            <tr>
                                <td> {{ $item->id }}</td>
                                <td> {{ $item->name }}</td>
                                <td> {{ $item->slug }}</td>
                                <td> {!! $item->description !!}</td>
                                <td class="">
                                    <a href="javascript:void(0);" class="edit" data-id="{{ $item->id }}"
                                        data-toggle="modal" data-target="#muscleGroupModal">
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

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#muscleGroupModal">
        Tạo Nhóm Cơ
    </button>

    <!-- Modal -->
    <div class="modal fade" id="muscleGroupModal" tabindex="-1" role="dialog" aria-labelledby="muscleGroupModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="muscleGroupModalLabel">QUẢN LÝ NHÓM CƠ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
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
                        {!! Form::text('name', old('name', isset($muscleGroupEdit) ? $muscleGroupEdit->name : ''), [
                            'placeholder' => 'Nhập vào nhóm cơ',
                            'class' => 'form-control',
                        ]) !!}
                    </div>
                    <div class="form-group mb-3">
                        {!! Form::label('description', 'Mô tả', ['class' => 'form-label fw-bold']) !!}
                        {!! Form::textarea(
                            'description',
                            old('description', isset($muscleGroupEdit) ? $muscleGroupEdit->description : ''),
                            ['class' => 'form-control', 'id' => 'summernote'],
                        ) !!}
                    </div>
                    {{-- <div class="form-group d-flex justify-content-end">
                        {!! Form::submit('Lưu', ['class' => 'btn btn-primary']) !!}
                    </div> --}}
                    {{ Form::close() }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="saveButton" onclick="$('#muscleGroupForm').submit();">Lưu</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <!-- Thêm thư viện jQuery - chỉ cần một phiên bản -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Thêm thư viện Bootstrap JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- Thêm thư viện Summernote -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <!-- Các script xử lý -->
    <script type="text/javascript">
        $(document).ready(function() {
            // Xử lý sự kiện nhấn vào biểu tượng cây bút để chỉnh sửa
            $('.edit').on('click', function() {
                var id = $(this).data('id'); // Lấy ID
                // Có thể thêm mã để lấy thông tin và điền vào form tại đây
                $('#muscleGroupModal').modal('show'); // Hiển thị modal chỉnh sửa
            });


            $(document).ready(function() {
                // ...

                // Xử lý sự kiện khi nhấn nút Lưu trong modal
                $('#saveButton').click(function(e) {
                    e.preventDefault();

                    // Sử dụng FormData để lấy dữ liệu từ form
                    var formData = new FormData($('#muscleGroupForm')[0]);

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('muscle_groups.store-test') }}',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            if (data.success) {
                                $('#muscleGroupModal').modal('hide');
                                alert(data.success);
                                // Cập nhật danh sách nhóm cơ ở đây nếu cần
                            }
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            // Hiển thị lỗi ở đây nếu cần
                        }
                    });
                });

                // ...
            });



            // Khởi tạo Summernote cho phần nhập mô tả
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
        });
    </script>
@endsection
