@extends('layouts.be')

@section('content')
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>DANH SÁCH<b> MÓN ĂN</b></h2>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('food.create') }}" class="btn btn-success" data-toggle="modal">
                                <i class="material-icons">&#xE147;</i> <span>Thêm món ăn</span>
                            </a>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Stt</th>
                            <th>Tên</th>
                            <th>Ảnh</th>
                            <th>Mô tả</th>
                            <th>Calo</th>
                            @foreach ($nutrients as $nutrient)
                                <th>{{ $nutrient->name }}</th>
                            @endforeach
                            <th>Quản lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $Idfood = 1;
                        @endphp
                        @foreach ($foods as $item)
                            <tr>
                                <td>{{ $Idfood++ }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    @php
                                        $pathimage = 'storage/foods/';
                                        $imagename = $item->image;
                                        $imageCombine = $pathimage . $imagename;
                                    @endphp
                                    <img src="{{ asset($imageCombine) }}" alt="Ảnh thực phẩm" style="max-width: 200px; max-height: 200px;">
                                </td>
                                <td>{!! $item->description !!}</td>
                                <td>{{ $item->calo . " gram" }}</td>

                                @foreach ($nutrients as $nutrient)
                                    <td>{{ $item->nutrients->where('id', $nutrient->id)->first()->pivot->description . " gram" }}</td>
                                @endforeach

                                <td class="">
                                    <a href="{{ url('/food/edit') }}/{{ $item->id }}" class="edit" data-toggle="modal">
                                        <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                                    </a>
                                    <a href="#deleteFoodModal" class="delete" data-toggle="modal">
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
