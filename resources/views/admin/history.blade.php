@extends('layouts.layout_admin')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/history.css') }}">
    </head>

    <div class="container">
        <h1 class="text-center pt-3" style="color: #489085; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
            ประวัติการจองทั้งหมด</h1>
            <form action="{{ route('booking.history.all') }}" method="GET" class="mb-4">
                <div class="row g-3 justify-content-center">
                    <!-- เลือกกิจกรรม -->
                    <div class="col-12 col-md-auto d-flex flex-column flex-md-row align-items-md-center">
                        <label for="activity_name" class="me-md-2 mb-0 text-nowrap">กิจกรรม</label>
                        <select name="activity_name" id="activity_name" class="form-control w-100 w-md-auto">
                            <option value="">ทั้งหมด</option>
                            @foreach ($activities as $activity_id => $activity_name)
                                <option value="{{ $activity_name }}" {{ request('activity_name') == $activity_name ? 'selected' : '' }}>
                                    {{ $activity_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- เลือกสถานะการจอง -->
                    <div class="col-12 col-md-auto d-flex flex-column flex-md-row align-items-md-center">
                        <label for="status" class="me-md-2 mb-0 text-nowrap">สถานะการจอง</label>
                        <select name="status" id="status" class="form-control w-100 w-md-auto">
                            <option value="">ทั้งหมด</option>
                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>เข้าชม</option>
                            <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>ยกเลิก</option>
                        </select>
                    </div>
                     <div class="col-12 col-md-auto text-center">
                        <button type="submit" class="btn btn-primary w-100 w-md-auto">ค้นหา</button>
                    </div>
                </div>
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
                                                            @else
                                                                ไม่มีรอบการเข้าชม
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="#detailsModal_{{ $item->booking_id }}"
                                                                class="text-blue-500" data-toggle="modal">
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
                                                                        รายละเอียดการจอง - {{ $item->activity->activity_name }}</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>วันเวลาที่จองเข้ามา:</strong>
                                                                        {{ \Carbon\Carbon::parse($item->created_at)->locale('th')->translatedFormat('j F') }}
                                                                        {{ \Carbon\Carbon::parse($item->created_at)->year + 543 }}
                                                                        เวลา
                                                                        {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}
                                                                        น.</p>
                                                                    <p><strong>กิจกรรม:</strong>
                                                                        {{ $item->activity->activity_name }}</p>
                                                                    @if (!$item->subActivities->isEmpty())
                                                                        <p><strong>หลักสูตร:</strong>
                                                                            @foreach ($item->subActivities as $subactivity)
                                                                                {{ $subactivity->sub_activity_name }}
                                                                            @endforeach
                                                                        </p>
                                                                    @endif
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
                                                                        @if ($item->children_qty > 0)
                                                                        <p><strong>เด็ก :
                                                                            </strong>{{ $item->children_qty }} คน</p>
                                                                    @endif
                                                                    @if ($item->students_qty > 0)
                                                                        <p><strong>นร / นศ :
                                                                            </strong>{{ $item->students_qty }} คน</p>
                                                                    @endif
                                                                    @if ($item->adults_qty > 0)
                                                                        <p><strong>ผู้ใหญ่ / คุณครู :
                                                                            </strong>{{ $item->adults_qty }} คน</p>
                                                                    @endif
                                                                    @if ($item->disabled_qty > 0)
                                                                        <p><strong>ผู้พิการ :
                                                                            </strong>{{ $item->disabled_qty }} คน</p>
                                                                    @endif
                                                                    @if ($item->elderly_qty > 0)
                                                                        <p><strong>ผู้สูงอายุ (คน):
                                                                            </strong>{{ $item->elderly_qty }} คน</p>
                                                                    @endif
                                                                    @if ($item->monk_qty > 0)
                                                                        <p><strong>พระภิกษุสงฆ์ / สามเณร (คน):
                                                                            </strong>{{ $item->monk_qty }} รูป</p>
                                                                    @endif
                                                                    @if (!empty($item->note))
                                                                        <p><strong>*หมายเหตุ: </strong>{{ $item->note }}</p>
                                                                    @endif
                                                                    <p><strong>จำนวนผู้เข้าชมทั้งหมด:
                                                                        </strong>{{ $item->children_qty + $item->students_qty + $item->adults_qty + $item->disabled_qty + $item->elderly_qty + $item->monk_qty }}
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
                                                    <div id="actualModal_{{ $statusChange->booking_id }}"
                                                        class="modal fade" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">รายละเอียดจำนวนผู้เข้าชม</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    @if ($item->activity)
                                                                        @php
                                                                            $totalPrice = 0;
                                                                            $childrenTotal = 0;
                                                                            $studentTotal = 0;
                                                                            $adultTotal = 0;
                                                                            $disabledTotal = 0;
                                                                            $elderlyTotal = 0;
                                                                            $monkTotal = 0;
                                                                            $totalParticipants = 0;
                                                                            if ($item->activity->children_price > 0) {
                                                                                $childrenTotal =
                                                                                    $statusChange->actual_children_qty *
                                                                                    $item->activity->children_price;
                                                                                $totalPrice += $childrenTotal;
                                                                                $totalParticipants +=
                                                                                    $statusChange->actual_children_qty;
                                                                            }
                                                                            if ($item->activity->student_price > 0) {
                                                                                $studentTotal =
                                                                                    $statusChange->actual_students_qty *
                                                                                    $item->activity->student_price;
                                                                                $totalPrice += $studentTotal;
                                                                                $totalParticipants +=
                                                                                    $statusChange->actual_students_qty;
                                                                            }
                                                                            if ($item->activity->adult_price > 0) {
                                                                                $adultTotal =
                                                                                    $statusChange->actual_adults_qty *
                                                                                    $item->activity->adult_price;
                                                                                $totalPrice += $adultTotal;
                                                                                $totalParticipants +=
                                                                                    $statusChange->actual_adults_qty;
                                                                            }
                                                                            if ($item->activity->disabled_price > 0) {
                                                                                $disabledTotal =
                                                                                    $statusChange->actual_disabled_qty *
                                                                                    $item->activity->disabled_price;
                                                                                $totalPrice += $disabledTotal;
                                                                                $totalParticipants +=
                                                                                    $statusChange->actual_disabled_qty;
                                                                            }
                                                                            if ($item->activity->elderly_price > 0) {
                                                                                $elderlyTotal =
                                                                                    $statusChange->actual_elderly_qty *
                                                                                    $item->activity->elderly_price;
                                                                                $totalPrice += $elderlyTotal;
                                                                                $totalParticipants +=
                                                                                    $statusChange->actual_elderly_qty;
                                                                            }
                                                                            if ($item->activity->monk_price > 0) {
                                                                                $monkTotal =
                                                                                    $statusChange->actual_monk_qty *
                                                                                    $item->activity->monk_price;
                                                                                $totalPrice += $monkTotal;
                                                                                $totalParticipants +=
                                                                                    $statusChange->actual_monk_qty;
                                                                            }
                                                                            $combinedAdultsDisabled =
                                                                                $statusChange->actual_adults_qty +
                                                                                $statusChange->actual_disabled_qty;
                                                                            $totalParticipants =
                                                                                $combinedAdultsDisabled +
                                                                                $statusChange->actual_children_qty +
                                                                                $statusChange->actual_students_qty +
                                                                                $statusChange->actual_elderly_qty +
                                                                                $statusChange->actual_monk_qty;
                                                                        @endphp
                                                                        @if ($statusChange->actual_children_qty > 0 && $item->activity->children_price > 0)
                                                                            <p>เด็ก
                                                                                {{ $statusChange->actual_children_qty }} คน
                                                                                x
                                                                                {{ number_format($item->activity->children_price) }}
                                                                                บาท = {{ number_format($childrenTotal) }}
                                                                                บาท</p>
                                                                        @elseif ($statusChange->actual_children_qty > 0)
                                                                            <p>เด็ก
                                                                                {{ $statusChange->actual_children_qty }} คน
                                                                            </p>
                                                                        @endif
                                                                        @if ($statusChange->actual_students_qty > 0 && $item->activity->student_price > 0)
                                                                            <p>นักเรียน
                                                                                {{ $statusChange->actual_students_qty }} คน
                                                                                x
                                                                                {{ number_format($item->activity->student_price) }}
                                                                                บาท = {{ number_format($studentTotal) }}
                                                                                บาท</p>
                                                                        @elseif ($statusChange->actual_students_qty > 0)
                                                                            <p>นักเรียน
                                                                                {{ $statusChange->actual_students_qty }} คน
                                                                            </p>
                                                                        @endif
                                                                        @if ($statusChange->actual_adults_qty > 0 && $item->activity->adult_price > 0)
                                                                            <p>ผู้ใหญ่
                                                                                {{ $statusChange->actual_adults_qty }} คน x
                                                                                {{ number_format($item->activity->adult_price) }}
                                                                                บาท = {{ number_format($adultTotal) }} บาท
                                                                            </p>
                                                                        @elseif ($statusChange->actual_adults_qty > 0)
                                                                            <p>ผู้ใหญ่
                                                                                {{ $statusChange->actual_adults_qty }} คน
                                                                            </p>
                                                                        @endif
                                                                        @if ($statusChange->actual_disabled_qty > 0 && $item->activity->disabled_price > 0)
                                                                            <p>ผู้พิการ
                                                                                {{ $statusChange->actual_disabled_qty }} คน
                                                                                x
                                                                                {{ number_format($item->activity->disabled_price) }}
                                                                                บาท = {{ number_format($disabledTotal) }}
                                                                                บาท</p>
                                                                        @elseif ($statusChange->actual_disabled_qty > 0)
                                                                            <p>ผู้พิการ
                                                                                {{ $statusChange->actual_disabled_qty }} คน
                                                                            </p>
                                                                        @endif
                                                                        @if ($statusChange->actual_elderly_qty > 0 && $item->activity->elderly_price > 0)
                                                                            <p>ผู้สูงอายุ
                                                                                {{ $statusChange->actual_elderly_qty }} คน
                                                                                x
                                                                                {{ number_format($item->activity->elderly_price) }}
                                                                                บาท = {{ number_format($elderlyTotal) }}
                                                                                บาท</p>
                                                                        @elseif ($statusChange->actual_elderly_qty > 0)
                                                                            <p>ผู้สูงอายุ
                                                                                {{ $statusChange->actual_elderly_qty }} คน
                                                                            </p>
                                                                        @endif
                                                                        @if ($statusChange->actual_monk_qty > 0 && $item->activity->monk_price > 0)
                                                                            <p>พระภิกษุ
                                                                                {{ $statusChange->actual_monk_qty }} รูป x
                                                                                {{ number_format($item->activity->monk_price) }}
                                                                                บาท = {{ number_format($monkTotal) }} บาท
                                                                            </p>
                                                                        @elseif ($statusChange->actual_monk_qty > 0)
                                                                            <p>พระภิกษุ
                                                                                {{ $statusChange->actual_monk_qty }} รูป
                                                                            </p>
                                                                        @endif
                                                                        @if ($totalPrice > 0)
                                                                            <hr>
                                                                            <p><strong>ยอดรวมราคา:</strong>
                                                                                {{ number_format($totalPrice) }} บาท</p>
                                                                            <p><strong>ยอดรวมผู้เข้าชมจริงทั้งหมด:</strong>
                                                                                {{ number_format($totalParticipants) }} คน
                                                                            </p>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">ปิด</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
@endsection
