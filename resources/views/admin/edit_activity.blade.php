@extends('layouts.layout')
@section('title', 'แก้ไขรายละเอียดกิจกรรม')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/UpdateActivity.css') }}">
    </head>

    <div class="container mt-5">
        <h2 class="text-center py-3">แบบฟอร์มแก้ไขรายละเอียดกิจกรรม</h2>
        <div class="card shadow p-4">
            <form method="POST" action="{{ route('UpdateActivity', $activity->activity_id) }}" class="row g-3" novalidate>
                @csrf
                <div class="container">
                    <div class="row">
                        <div class="col-25">
                            <label for="activity_name">ชื่อกิจกรรม</label>
                        </div>
                        <div class="col-75">
                            <input type="text" class="form-control" id="activity_name" name="activity_name"
                                value="{{ old('activity_name', $activity->activity_name) }}">
                            @error('activity_name')
                                <div class="my-2">
                                    <span class="text-danger">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-25">
                            <label for="description" class="form-label">คำอธิบายกิจกรรม</label>
                        </div>
                        <div class="col-75">
                            <textarea id="description" name="description" style="height:200px">{{ old('description', trim($activity->description)) }}</textarea>
                            @error('description')
                                <div class="my-2">
                                    <span class="text-danger">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-25">
                            <label for="price" class="form-label">ราคา</label>
                        </div>
                        <div class="col-1">
                            <input type="number" id="price" name="price"
                                value="{{ old('price', $activity->price) }}">
                            @error('price')
                                <div class="my-2">
                                    <span class="text-danger">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div class="col-25">
                            <label class="bath">บาท</label>
                        </div>
                    </div>
                </div>
                <br>
                <div class="col-12 d-flex justify-content-center py-4">
                    <button type="submit" class="btn btn-primary btn-lg ms-2" id="openModal">
                        ยืนยันข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">ยืนยันการแก้ไขข้อมูล</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    กรุณาตรวจสอบข้อมูลก่อนทำการยืนยัน
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmit">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/updateEditActivity.js') }}"></script>

@endsection
