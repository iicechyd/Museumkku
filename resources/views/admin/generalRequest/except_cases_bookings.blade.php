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
            <a href="{{ url('/admin/request_bookings/general') }}" class="btn-request-outline">รออนุมัติ</a>
            <a href="{{ url('/admin/approved_bookings/general') }}" class="btn-approved-outline">อนุมัติ</a>
            <a href="{{ url('/admin/except_cases_bookings/general') }}" class="btn-except">กรณีพิเศษ</a>
        </div>
        @if (count($exceptBookings) > 0)
            <h1 class="table-heading text-center">การจองกรณีพิเศษ</h1>
            {{ $exceptBookings->links() }}

            @component('components.table_except_cases_bookings')
                @foreach ($exceptBookings as $item)
                    <tr>
                        <td>{{ $item->booking_id }}</td>
                        <td>{{ $item->activity->activity_name }}</td>
                        <td class="custom-td">
                            {{ \Carbon\Carbon::parse($item->booking_date)->locale('th')->translatedFormat('j F') }}
                            {{ \Carbon\Carbon::parse($item->booking_date)->addYears(543)->year }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->timeslot->start_time)->format('H:i') }} น. -
                            {{ \Carbon\Carbon::parse($item->timeslot->end_time)->format('H:i') }} น.
                        </td>
                        <td class="long-cell">{{ $item->institute->instituteName }}</td>
                        <td class="long-cell">{{ $item->institute->instituteAddress }} {{ $item->institute->province }}
                            {{ $item->institute->subdistrict }} {{ $item->institute->zipcode }}</td>
                        <td>{{ $item->visitor->visitorName }}</td>
                        <td>{{ $item->visitor->visitorEmail }}</td>
                        <td>{{ $item->visitor->tel }}</td>
                        <td>{{ $item->children_qty > 0 ? $item->children_qty . ' คน' : '-' }}</td>
                        <td>{{ $item->students_qty > 0 ? $item->students_qty . ' คน' : '-' }}</td>
                        <td>{{ $item->adults_qty > 0 ? $item->adults_qty . ' คน' : '-' }}</td>
                        <td>{{ $item->totalVisitors > 0 ? $item->totalVisitors . ' คน' : '-' }}</td>
                        <td>
                            @switch($item->status)
                                @case(0)
                                    <button type="button" class="btn btn-warning text-white">รออนุมัติ</button>
                                @break

                                @case(1)
                                    <button type="button" class="btn-approved-outline">อนุมัติ</button>
                                @break

                                @case(2)
                                    <button type="button" class="btn-except">ยกเลิก</button>
                                @break
                            @endswitch
                        </td>
                        <td>{{ $item->latestStatusChange->updated_at ?? 'N/A' }}</td>
                        <td>{{ $item->latestStatusChange->changed_by ?? 'N/A' }}</td>
                        <td>{{ $item->latestStatusChange->comments ?? 'ไม่มีความคิดเห็น' }}</td>
                    </tr>
                @endforeach
            @endcomponent
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('js/except_cases_bookings.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var exceptBookings = @json($exceptBookings->pluck('booking_id'));

            // เรียกใช้ toggleCommentsField สำหรับแต่ละ booking_id
            exceptBookings.forEach(function(booking_id) {
                toggleCommentsField(booking_id);
            });
        });

        function toggleCommentsField(booking_id) {
            var status = document.getElementById("statusSelect_" + booking_id).value;
            var commentsField = document.getElementById("commentsField_" + booking_id);

            // แสดงฟิลด์คอมเม้นต์เมื่อสถานะเป็น 'cancel'
            if (status === "cancel") {
                commentsField.style.display = "block"; // แสดงฟิลด์คอมเม้นต์
            } else {
                commentsField.style.display = "none"; // ซ่อนฟิลด์คอมเม้นต์
            }
        }
    </script>
@else
    <h1 class="text text-center py-5 ">ไม่พบข้อมูลในระบบ</h1>
    @endif

@endsection
