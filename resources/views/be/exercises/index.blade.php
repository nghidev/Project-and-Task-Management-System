@extends('layouts.be')

@section('content')
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>DANH SÁCH<b> BÀI TẬP</b></h2>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('exercise.create') }}" class="btn btn-success" data-toggle="modal">
                                <i class="material-icons">&#xE147;</i> <span>Thêm bài tập</span>
                            </a>
                            {{-- <a href="#deleteEmployeeModal" class="btn btn-danger" data-toggle="modal">
                                <i class="material-icons">&#xE872;</i> <span>Delete</span>
                            </a> --}}
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Stt</th>
                            <th>Tên</th>
                            <th>Slug</th>
                            <th>Cơ ảnh hưởng</th>
                            <th>Mô tả</th>
                            <th>Hình ảnh</th>
                            <th>Cơ phụ ảnh hưởng</th>
                            <th>Quản lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $Idexercise = 1;
                        @endphp
                        @foreach ($exercise as $item)
                            <tr>
                                <td> {{ $Idexercise++ }}</td>
                                <td> {{ $item->name }}</td>
                                <td> {{ $item->slug }}</td>
                                <td> {{ $item->muscle->name }}</td>
                                <td> {!! $item->description !!}</td>
                                @php
                                    $pathimage = 'storage/exercises/';
                                    $imagename = $item->image;

                                    $imageCombine = $pathimage . $imagename;
                                @endphp
                                <td> <img id="imagePreview" src="{{ asset($imageCombine) }}" alt="Ảnh đang chọn"
                                        style="max-width: 200px; max-height: 200px;" /> </td>

                                <td>
                                    @foreach ($item->muscles as $second_muscle)
                                        <p>{{ $second_muscle->name }}</p>
                                    @endforeach
                                </td>

                                <td class="">
                                    <a href="{{ url('/exercise/edit') }}/{{ $item->id }}" class="edit"
                                        data-toggle="modal">
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
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    {{-- <script type="text/javascript">
    $(document).ready(function() {
        $('a[href="{{ route('muscle_groups.create') }}"]').on('click', function(e) {
            e.preventDefault(); // Ngăn chặn sự kiện click mặc định của thẻ a
            var userConfirmed = confirm('Bạn có muốn đi đến trang thêm mới Nhóm cơ không?');

            if (userConfirmed) {
                window.location.href = $(this).attr('href'); // Điều hướng người dùng đến trang thêm mới
            }
            // Nếu người dùng không xác nhận, không làm gì cả và họ sẽ ở lại trang hiện tại
        });
    });
</script> --}}
@endsection
