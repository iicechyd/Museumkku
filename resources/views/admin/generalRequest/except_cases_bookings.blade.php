@extends('layouts.layout_admin')
@section('title', 'การจองกรณีพิเศษ')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/except_cases_bookings.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">
        <div class="button pb-2">
            <a href="{{ url('/admin/manage_bookings/general') }}" class="btn btn-outline-primary">การจองวันนี้</a>
            <a href="{{ url('/admin/request_bookings/general') }}" class="btn-request-outline">รออนุมัติ</a>
            <a href="{{ url('/admin/approved_bookings/general') }}" class="btn-approved-outline">อนุมัติ</a>
            <a href="{{ url('/admin/except_cases_bookings/general') }}" class="btn-except">ยกเลิก</a>
        </div>

        <div class="form col-6">
            <form method="GET" action="{{ route('except_bookings.general') }}">
                <label for="activity_id">เลือกกิจกรรม:</label>
                <select name="activity_id" id="activity_id" class="form-select" onchange="this.form.submit()">
                    <option value="">กรุณาเลือกประเภทการเข้าชม</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->activity_id }}"
                            {{ request('activity_id') == $activity->activity_id ? 'selected' : '' }}>
                            {{ $activity->activity_name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if (request('activity_id'))
            @php
                $selectedActivityName =
                    $activities->firstWhere('activity_id', request('activity_id'))->activity_name ?? null;
            @endphp

            @if (count($exceptBookings) > 0)
                <h1 class="table-heading text-center">{{ $selectedActivityName }}</h1>
                {{ $exceptBookings->appends(request()->query())->links() }}

                @component('components.table_except_cases_bookings')
                    @foreach ($exceptBookings as $item)
                        <tr>
                            <td>{{ $item->booking_id }}</td>
                            <td class="custom-td">
                                {{ \Carbon\Carbon::parse($item->booking_date)->locale('th')->translatedFormat('j F') }}
                                {{ \Carbon\Carbon::parse($item->booking_date)->addYears(543)->year }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->timeslot->start_time)->format('H:i') }} น. -
                                {{ \Carbon\Carbon::parse($item->timeslot->end_time)->format('H:i') }} น.
                            </td>
                            <td>
                                @if ($item->status == 3)
                                    <button type="button" class="btn-except">ยกเลิก</button>
                                @endif
                            </td>
                            <td>{{ $item->latestStatusChange->comments ?? 'ไม่มีความคิดเห็น' }}</td>
                            <td>
                                <a href="#detailsModal_{{ $item->booking_id }}" class="text-blue-500" data-toggle="modal">
                                    รายละเอียด
                                </a>
                            </td>
                            <!-- Modal สำหรับแสดงรายละเอียด -->
                            <div class="modal fade" id="detailsModal_{{ $item->booking_id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">รายละเอียดการจอง -
                                                {{ $item->activity->activity_name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                            <p><strong>วันเวลาที่จองเข้ามา:
                                                </strong>{{ \Carbon\Carbon::parse($item->created_at)->locale('th')->translatedFormat('j F') }}
                                                {{ \Carbon\Carbon::parse($item->created_at)->year + 543 }} เวลา
                                                {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} น.</p>
                                            <p><strong>ชื่อหน่วยงาน: </strong>{{ $item->institute->instituteName }}</p>
                                            <p><strong>ที่อยู่หน่วยงาน: </strong>{{ $item->institute->instituteAddress }}
                                                {{ $item->institute->subdistrict }} {{ $item->institute->district }}
                                                {{ $item->institute->inputProvince }} {{ $item->institute->zipcode }}</p>
                                            <p><strong>ชื่อผู้ประสานงาน: </strong>{{ $item->visitor->visitorName }}</p>
                                            <p><strong>อีเมลผู้ประสานงาน: </strong>{{ $item->visitor->visitorEmail }}</p>
                                            <p><strong>เบอร์โทรศัพท์: </strong>{{ $item->visitor->tel }}</p>
                                            <p><strong>เด็ก (คน):
                                                </strong>{{ $item->children_qty > 0 ? $item->children_qty . ' คน' : '-' }}</p>
                                            <p><strong>นร / นศ (คน):
                                                </strong>{{ $item->students_qty > 0 ? $item->students_qty . ' คน' : '-' }}</p>
                                            <p><strong>ผู้ใหญ่ / คุณครู (คน):
                                                </strong>{{ $item->adults_qty > 0 ? $item->adults_qty . ' คน' : '-' }}</p>
                                            <p><strong>ผู้พิการ (คน):
                                                </strong>{{ $item->disabled_qty > 0 ? $item->disabled_qty . ' คน' : '-' }}</p>
                                            <p><strong>ผู้สูงอายุ (คน):
                                                </strong>{{ $item->elderly_qty > 0 ? $item->elderly_qty . ' คน' : '-' }}</p>
                                            <p><strong>พระภิกษุสงฆ์ / สามเณร (คน):
                                                </strong>{{ $item->monk_qty > 0 ? $item->monk_qty . ' รูป' : '-' }}</p>
                                            <p><strong>จำนวนคนทั้งหมด:
                                                </strong>{{ $item->children_qty + $item->students_qty + $item->adults_qty + $item->disabled_qty + $item->elderly_qty + $item->monk_qty }}
                                                คน</p>
                                            <p><strong>ยอดรวมราคา: </strong>{{ number_format($item->totalPrice, 2) }} บาท</p>
                                            <p><strong>แก้ไขสถานะ: </strong>
                                                @if ($item->latestStatusChange)
                                                    {{ \Carbon\Carbon::parse($item->latestStatusChange->updated_at)->locale('th')->translatedFormat('j F') }}
                                                    {{ \Carbon\Carbon::parse($item->latestStatusChange->updated_at)->year + 543 }}
                                                    เวลา
                                                    {{ \Carbon\Carbon::parse($item->latestStatusChange->updated_at)->format('H:i') }}
                                                    น.
                                                    แก้ไขโดยเจ้าหน้าที่: {{ $item->latestStatusChange->changed_by ?? 'N/A' }}
                                                @else
                                                    ไม่พบข้อมูลการเปลี่ยนแปลงสถานะ
                                                @endif
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @endforeach
                @endcomponent
            @else
                <h2 class="text-center py-5">ไม่พบข้อมูลการจองสำหรับกิจกรรมนี้</h2>
            @endif
    </div>
@else
    <h1 class="text text-center py-5 ">กรุณาเลือกกิจกรรมเพื่อตรวจสอบข้อมูล</h1>
    @endif
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/except_cases_bookings.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var exceptBookings = @json($exceptBookings->pluck('booking_id'));
        exceptBookings.forEach(function(booking_id) {
            toggleCommentsField(booking_id);
        });
    });

    function toggleCommentsField(booking_id) {
        var status = document.getElementById("statusSelect_" + booking_id).value;
        var commentsField = document.getElementById("commentsField_" + booking_id);
        if (status === "cancel") {
            commentsField.style.display = "block";
        } else {
            commentsField.style.display = "none";
        }
    }
</script>
