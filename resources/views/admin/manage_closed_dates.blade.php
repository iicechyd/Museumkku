<!DOCTYPE html>
<html lang="en">
@extends('layouts.layout_admin')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/closed_date.css') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    @php
        use Carbon\Carbon;
        Carbon::setLocale('th');
    @endphp
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container px-4 py-6">
        <h1 class="table-heading text-center">จัดการวันปิดรอบการเข้าชม</h1>
        <div class="card shadow p-4">
            <form action="{{ route('admin.saveClosedDates') }}" class="row g-3" method="POST">
                @csrf
                <div class="form-group col-md-4">
                    <label for="activity_id" class="font-weight-bold">เลือกประเภทการเข้าชม</label>
                    <select id="activity_id" name="activity_id" class="form-control" required>
                        <option value="">กรุณาเลือกประเภทการเข้าชม</option>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->activity_id }}">{{ $activity->activity_name }}</option>
                        @endforeach
                    </select>
                    @error('activity_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    <label for="timeslots_id" class="font-weight-bold">เลือกรอบการเข้าชม</label>
                    <select id="timeslots_id" name="timeslots_id" class="form-control" required disabled>
                        <option value="">กรุณาเลือกรอบการเข้าชม</option>
                        <option value="all">ปิดทุกรอบ</option>
                    </select>
                    @error('timeslots_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="closed_on" class="font-weight-bold">วันที่ปิดรอบการเข้าชม</label>
                    <div class="input-group">
                        <input type="date" id="closed_on" name="closed_on" class="form-control" required
                            placeholder="กรุณาเลือกวันที่ต้องการปิด (วัน/เดือน/ปี)">
                        <div class="input-group-append">
                            <label for="closed_on" class="input-group-text" style="cursor: pointer;">
                                <i class="fas fa-calendar-alt" style="font-size: 1.5rem;"></i>
                            </label>
                        </div>
                    </div>
                    @error('closed_on')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group col-md-5">
                    <label for="comments" class="font-weight-bold">หมายเหตุ</label>
                    <textarea id="comments" name="comments" class="form-control" rows="3"
                        placeholder="กรุณากรอกหมายเหตุ"></textarea>
                    @error('comments')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary w-40 px-5">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
    @if ($closedDates->isNotEmpty())
        <div class="container px-4 py-6">
            <h2 class="font-weight-bold mt-5">รายการวันที่ปิดรอบการเข้าชม</h2>
            <div class="table-responsive">
                <table class="table table-bordered mt-4 shadow-sm">
                    <thead class="thead-light">
                        <tr>
                            <th data-type="text-long">ประเภทการเข้าชม<span class="resize-handle"></span></th>
                            <th data-type="text-short">รอบเวลา<span class="resize-handle"></span></th>
                            <th data-type="text-short">วันที่ปิดรอบการเข้าชม<span class="resize-handle"></span></th>
                            <th data-type="text-short">หมายเหตุ<span class="resize-handle"></span></th>
                            <th data-type="text-short">การจัดการ<span class="resize-handle"></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($closedDates as $closed)
                            <tr>
                                <td>{{ $closed->activity->activity_name }}</td>
                                <td>
                                    @if ($closed->timeslot)
                                        {{ Carbon::parse($closed->timeslot->start_time)->format('H:i') }} น. -
                                        {{ Carbon::parse($closed->timeslot->end_time)->format('H:i') }} น.
                                    @else
                                        ปิดทุกรอบ
                                    @endif
                                </td>
                                <td>{{ Carbon::parse($closed->closed_on)->translatedFormat('d /M/ ') }}{{ Carbon::parse($closed->closed_on)->year + 543 }}
                                </td>
                                <td>{{ $closed->comments }}</td>
                                <td>
                                    <form action="{{ route('admin.deleteClosedDate', $closed->closed_timeslots_id) }}" method="POST"
                                        onsubmit="return confirm('ยืนยันการยกเลิกวันที่ปิดรอบการเข้าชมนี้หรือไม่?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            ยกเลิก
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    <script>
        var getTimeslotsUrl = "{{ route('admin.getTimeslots') }}";
        var csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/manage_closed_dates.js') }}"></script>
@endsection

</html>