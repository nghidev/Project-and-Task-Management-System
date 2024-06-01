@extends('layouts.be')

@section('content')
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>DANH SÁCH<b> {{ $Tilte }}</b></h2>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('muscle.create') }}" class="btn btn-success" data-toggle="modal">
                                <i class="material-icons">&#xE147;</i> <span>Thêm Nhóm cơ</span>
                            </a>
                            {{-- <a href="#deleteEmployeeModal" class="btn btn-danger" data-toggle="modal">
                                <i class="material-icons">&#xE872;</i> <span>Delete</span>
                            </a> --}}
                        </div>
                    </div>
                </div>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Tên</th>
                            <th>Nhóm cơ</th>
                            <th>Mô tả</th>
                            <th>Hình ảnh</th>
                            <th>Quản lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($GetMuscleList as $item)
                            <tr>
                                <td> {{ $item->id }}</td>
                                <td> {{ $item->name }}</td>
                                <td> {{ $item->muscleGroup->name }}</td>
                                <td> {!! $item->description !!}</td>
                                @php
                                    $pathimage = 'storage/muscles/';
                                    $imagename = $item->image;

                                    $imageCombine = $pathimage . $imagename;
                                @endphp
                                <td> <img id="imagePreview" src="{{ asset($imageCombine) }}" alt="Ảnh đang chọn"
                                        style="max-width: 200px; max-height: 200px;" /> </td>

                                <td class="">
                                    <a href="{{ url('/muscle/edit') }}/{{ $item->id }}" class="edit"
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
