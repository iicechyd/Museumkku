@extends('layouts.layout_admin')
@section('title', 'จองเข้าชมพิพิธภัณฑ์')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/request_bookings.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">
        <div class="button pb-2">
            <a href="{{ url('/admin/request_bookings/general') }}" class="btn-request">รออนุมัติ</a>
            <a href="{{ url('/admin/approved_bookings/general') }}" class="btn-approved-outline">อนุมัติ</a>
            <a href="{{ url('/admin/except_cases_bookings/general') }}" class="btn-except-outline">กรณีพิเศษ</a>
        </div>
        @if (count($requestBookings) > 0)
            <h1 class="table-heading text-center">รออนุมัติการจองเข้าชม</h1>
            {{ $requestBookings->links() }}

            @component('components.table_request_bookings')
                @foreach ($requestBookings as $item)
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
                        <td class="long-cell">{{ $item->instituteName }}</td>
                        <td class="long-cell">{{ $item->instituteAddress }} &nbsp;{{ $item->subdistrict }}
                            &nbsp;{{ $item->district }}&nbsp; {{ $item->province }}&nbsp;
                            {{ $item->zip }}</td>
                        <td>{{ $item->visitorName }}</td>
                        <td>{{ $item->visitorEmail }}</td>
                        <td>{{ $item->tel }}</td>
                        <td>{{ $item->children_qty }} คน</td>
                        <td>{{ $item->students_qty }} คน</td>
                        <td>{{ $item->adults_qty }} คน</td>
                        <td>{{ $item->totalVisitors }} คน</td>
                        <td>{{ $item->remaining_capacity }} / {{ $item->timeslot->max_capacity }} คน</td>
                        <td>{{ number_format($item->totalPrice, 2) }} บาท</td>

                        <td>
                            @switch($item->status)
                                @case(0)
                                    <button type="button" class="status-btn-request">รออนุมัติ</button>
                                @break

                                @case(1)
                                    <button type="button" class="btn btn-success">อนุมัติ</button>
                                @break

                                @case(2)
                                    <button type="button" class="btn btn-danger">ยกเลิก</button>
                                @break
                            @endswitch
                        </td>
                        <td>
                            <form action="{{ route('bookings.updateStatus', $item->booking_id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                <div class="flex items-center space-x-3">
                                    <select name="status" id="statusSelect_{{ $item->booking_id }}"
                                        onchange="toggleCommentsField({{ $item->booking_id }})"
                                        class="bg-gray-100 border border-gray-300 rounded-lg p-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="pending" {{ $item->status == 0 ? 'selected' : '' }}>รออนุมัติ</option>
                                        <option value="approve" {{ $item->status == 1 ? 'selected' : '' }}>อนุมัติ</option>
                                        <option value="cancel" {{ $item->status == 2 ? 'selected' : '' }}>ยกเลิก</option>
                                    </select>

                                    <!-- ฟิลด์ comments ที่จะถูกซ่อนไว้ในตอนแรก -->
                                    <div id="commentsField_{{ $item->booking_id }}" class="comments-field"
                                        style="display: {{ $item->status == 2 ? 'block' : 'none' }};">
                                        <input type="text" name="comments" placeholder="กรอกความคิดเห็น"
                                            class="bg-gray-100 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                    </div>

                                    <!-- ปุ่มอัปเดตสถานะจะแสดงตลอดเวลา -->
                                    <button type="submit" class="button-custom">
                                        อัปเดตสถานะ
                                    </button>
                                </div>
                            </form>
                        </td>
                        <td>{{ $item->created_at }}</td>
                        </td>
                    </tr>
                @endforeach
            @endcomponent
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('js/request_bookings.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var requestBookings = @json($requestBookings->pluck('booking_id'));

            // เรียกใช้ toggleCommentsField สำหรับแต่ละ booking_id
            requestBookings.forEach(function(booking_id) {
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
