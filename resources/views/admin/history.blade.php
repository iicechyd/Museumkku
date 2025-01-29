@extends('layouts.layout_admin')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/history.css') }}">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <div class="container">
        <h1 class="text-center pt-3" style="color: #489085; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
            ประวัติการจองทั้งหมด</h1>
        <form action="{{ route('booking.history.all') }}" method="GET" class="form-inline mb-4 justify-content-center">
            <div class="form-group">
                <label for="activity_name" class="mr-2">ชื่อกิจกรรม:</label>
                <select name="activity_name" id="activity_name" class="form-control">
                    <option value="">ทั้งหมด</option>
                    @foreach ($activities as $activity_id => $activity_name)
                        <option value="{{ $activity_name }}"
                            {{ request('activity_name') == $activity_name ? 'selected' : '' }}>
                            {{ $activity_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="status" class="mr-2">สถานะการจอง:</label>
                <select name="status" id="status" class="form-control">
                    <option value="">ทั้งหมด</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>เข้าชม</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>ยกเลิก</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">ค้นหา</button>
        </form>

        @if ($histories->isEmpty())
            <h2 class="text-center">ไม่มีประวัติการจอง</h2>
        @else
            <section class="intro">
                <div class="mask d-flex align-items-center">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="table-responsive bg-white shadow-sm rounded">
                                    <table class="table mb-0">
                                        <thead class="thead-white">
                                            <tr>
                                                <th scope="col">รายการที่</th>
                                                <th scope="col">วันที่จองเข้าชม</th>
                                                <th scope="col">รอบการเข้าชม</th>
                                                <th scope="col">รายละเอียดการจอง</th>
                                                <th scope="col">สถานะการจอง</th>
                                                <th scope="col">ยอดผู้เข้าชมจริง</th>
                                                <th scope="col">หมายเหตุ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($histories as $item)
                                                @foreach ($item->statusChanges as $statusChange)
                                                    <tr>
                                                        <th scope="row">{{ $item->booking_id }}</th>
                                                        <td>{{ \Carbon\Carbon::parse($item->booking_date)->locale('th')->translatedFormat('j F') }}
                                                            {{ \Carbon\Carbon::parse($item->booking_date)->year + 543 }}
                                                        </td>
                                                        <td>
                                                            @if ($item->timeslot)
                                                                {{ \Carbon\Carbon::parse($item->timeslot->start_time)->format('H:i') }}
                                                                น. -
                                                                {{ \Carbon\Carbon::parse($item->timeslot->end_time)->format('H:i') }}
                                                                น.
                                                        </td>
                                                    @else
                                                        ไม่มีรอบการเข้าชม
                                                @endif
                                                <td>
                                                    <a href="#detailsModal_{{ $item->booking_id }}" class="text-blue-500"
                                                        data-toggle="modal">
                                                        รายละเอียด
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($statusChange->new_status == 2)
                                                        <button type="button" class="status-btn">เข้าชม</button>
                                                    @elseif ($statusChange->new_status == 3)
                                                        <button type="button" class="btn-except">ยกเลิก</button>
                                                    @else
                                                        {{ $statusChange->new_status }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $totalQty =
                                                            $statusChange->actual_children_qty +
                                                            $statusChange->actual_students_qty +
                                                            $statusChange->actual_adults_qty +
                                                            $statusChange->actual_disabled_qty +
                                                            $statusChange->actual_elderly_qty +
                                                            $statusChange->actual_monk_qty;
                                                    @endphp
                                                    @if ($totalQty)
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#actualModal_{{ $statusChange->booking_id }}">
                                                            {{ $totalQty }} คน
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $statusChange->comments ? $statusChange->comments . ' ' : '-' }}
                                                </td>
                                                </tr>

                                                <!-- Modal for details -->
                                                <div class="modal fade" id="detailsModal_{{ $item->booking_id }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">
                                                                    รายละเอียดการจอง - {{ $item->activity_name }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>หลักสูตร:</strong>
                                                                    @if ($item->subActivities->isEmpty())
                                                                        -
                                                                    @else
                                                                        @foreach ($item->subActivities as $subactivity)
                                                                            {{ $subactivity->sub_activity_name }}
                                                                        @endforeach
                                                                    @endif
                                                                </p>
                                                                <p><strong>วันเวลาที่จองเข้ามา:</strong>
                                                                    {{ \Carbon\Carbon::parse($item->created_at)->locale('th')->translatedFormat('j F') }}
                                                                    {{ \Carbon\Carbon::parse($item->created_at)->year + 543 }}
                                                                    เวลา
                                                                    {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}
                                                                    น.</p>
                                                                <p><strong>ชื่อหน่วยงาน:</strong>
                                                                    {{ $item->institute->instituteName }}</p>
                                                                <p><strong>ที่อยู่หน่วยงาน:</strong>
                                                                    {{ $item->institute->instituteAddress }}
                                                                    {{ $item->institute->subdistrict }}
                                                                    {{ $item->institute->district }}
                                                                    {{ $item->institute->inputProvince }}
                                                                    {{ $item->institute->zipcode }}</p>
                                                                <p><strong>ชื่อผู้ประสานงาน:</strong>
                                                                    {{ $item->visitor->visitorName }}</p>
                                                                <p><strong>อีเมลผู้ประสานงาน:</strong>
                                                                    {{ $item->visitor->visitorEmail }}</p>
                                                                <p><strong>เบอร์โทรศัพท์:</strong>
                                                                    {{ $item->visitor->tel }}</p>
                                                                <p><strong>เด็ก (คน):</strong>
                                                                    {{ $item->children_qty > 0 ? $item->children_qty . ' คน' : '-' }}
                                                                </p>
                                                                <p><strong>นร / นศ (คน):</strong>
                                                                    {{ $item->students_qty > 0 ? $item->students_qty . ' คน' : '-' }}
                                                                </p>
                                                                <p><strong>ผู้ใหญ่ / คุณครู (คน):</strong>
                                                                    {{ $item->adults_qty > 0 ? $item->adults_qty . ' คน' : '-' }}
                                                                </p>
                                                                <p><strong>ผู้พิการ (คน):</strong>
                                                                    {{ $item->disabled_qty > 0 ? $item->disabled_qty . ' คน' : '-' }}
                                                                </p>
                                                                <p><strong>ผู้สูงอายุ (คน):</strong>
                                                                    {{ $item->elderly_qty > 0 ? $item->elderly_qty . ' คน' : '-' }}
                                                                </p>
                                                                <p><strong>พระภิกษุสงฆ์ / สามเณร (คน):</strong>
                                                                    {{ $item->monk_qty > 0 ? $item->monk_qty . ' รูป' : '-' }}
                                                                </p>
                                                                <p><strong>จำนวนคนทั้งหมด:</strong>
                                                                    {{ $item->children_qty + $item->students_qty + $item->adults_qty + $item->disabled_qty + $item->elderly_qty + $item->monk_qty }}
                                                                    คน</p>
                                                                <p><strong>ยอดรวมราคา:</strong>
                                                                    {{ number_format($item->totalPrice, 2) }} บาท</p>
                                                                <p><strong>แก้ไขสถานะ:</strong>
                                                                    {{ \Carbon\Carbon::parse($item->status_updated_at)->locale('th')->translatedFormat('j F') }}
                                                                    {{ \Carbon\Carbon::parse($item->status_updated_at)->year + 543 }}
                                                                    เวลา
                                                                    {{ \Carbon\Carbon::parse($item->status_updated_at)->format('H:i') }}
                                                                    น. โดยเจ้าหน้าที่:
                                                                    {{ $statusChange->changed_by ?? 'N/A' }}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">ปิด</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal Actual -->
                                                <div id="actualModal_{{ $statusChange->booking_id }}" class="modal fade"
                                                    tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">รายละเอียดจำนวนผู้เข้าชม</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                    <p>เด็ก: {{ $statusChange->actual_children_qty }} คน</p>
                                                                    <p>นักเรียน: {{ $statusChange->actual_students_qty }} คน</p>
                                                                    <p>ผู้ใหญ่: {{ $statusChange->actual_adults_qty }} คน</p>
                                                                    <p>ผู้พิการ: {{ $statusChange->actual_disabled_qty }}คน</p>
                                                                    <p>ผู้สูงอายุ: {{ $statusChange->actual_elderly_qty }}คน</p>
                                                                    <p>พระสงฆ์: {{ $statusChange->actual_monk_qty }} คน</p>
                                                                </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">ปิด</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach
    </div>
    @endif
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
@endsection
