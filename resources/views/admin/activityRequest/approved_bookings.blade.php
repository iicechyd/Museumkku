@extends('layouts.layout_admin')
@section('title', 'อนุมัติการจองเข้าชมพิพิธภัณฑ์')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/approved_bookings.css') }}">
    </head>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="container">
        <div class="button pb-2">
            <a href="{{ url('/admin/request_bookings/activity') }}" class="btn-request-outline">รออนุมัติ</a>
            <a href="{{ url('/admin/approved_bookings/activity') }}" class="btn btn-success">อนุมัติ</a>
            <a href="{{ url('/admin/except_cases_bookings/activity') }}" class="btn-except-outline">กรณีพิเศษ</a>
        </div>
        @if (count($approvedBookings) > 0)
            <h1 class="table-heading text-center">อนุมัติการจองเข้าชม</h1>
            {{ $approvedBookings->links() }}

            @component('components.table_approved_bookings')
                @foreach ($approvedBookings as $item)
                    <tr>
                        <td>{{ $item->booking_id }}</td>
                        <td>{{ $item->activity->activity_name }}</td>
                        <td class="custom-td">
                            {{ \Carbon\Carbon::parse($item->booking_date)->locale('th')->translatedFormat('j F') }}
                            {{ \Carbon\Carbon::parse($item->booking_date)->addYears(543)->year }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->timeslot->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($item->timeslot->end_time)->format('H:i') }}
                        </td>
                        <td class="long-cell">{{ $item->instituteName }}</td>
                        <td class="long-cell">{{ $item->instituteAddress }} {{ $item->province }}
                            {{ $item->subdistrict }} {{ $item->zip }}</td>
                        <td>{{ $item->visitorName }}</td>
                        <td>{{ $item->visitorEmail }}</td>
                        <td>{{ $item->tel }}</td>
                        <td>{{ $item->children_qty }} คน</td>
                        <td>{{ $item->students_qty }} คน</td>
                        <td>{{ $item->adults_qty }} คน</td>
                        <td>{{ $item->children_qty + $item->students_qty + $item->adults_qty }} คน</td>
                        <td>{{ $item->remaining_capacity }} / {{ $item->timeslot->max_capacity }} คน</td>
                        <td>
                            @switch($item->status)
                                @case(0)
                                    <button type="button" class="btn btn-warning text-white">รออนุมัติ</button>
                                @break

                                @case(1)
                                    <button type="button" class="status-btn">อนุมัติ</button>
                                @break

                                @case(2)
                                    <button type="button" class="status-btn-except">ยกเลิก</button>
                                @break
                            @endswitch
                        </td>
                        <td>
                            <form action="{{ route('bookings.updateStatus', $item->booking_id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                <select name="status" id="statusSelect" onchange="toggleCommentsAndButton()">
                                    <option value="pending" {{ $item->status == 0 ? 'selected' : '' }}>รออนุมัติ
                                    </option>
                                    <option value="approve" {{ $item->status == 1 ? 'selected' : '' }}>อนุมัติ
                                    </option>
                                    <option value="cancel" {{ $item->status == 2 ? 'selected' : '' }}>ยกเลิก
                                    </option>
                                </select>
                                <!-- ฟิลด์ comments ที่จะถูกซ่อนไว้ในตอนแรก -->
                                <div id="commentsField" style="display: none; margin-top: 10px;">
                                    <input type="text" name="comments" placeholder="กรอกความคิดเห็น" />
                                </div>
                                <button type="submit">อัปเดตสถานะ</button>
                            </form>
                        </td>
                        <td>{{ $item->latestStatusChange->updated_at ?? 'N/A' }}</td>
                        <td>{{ $item->latestStatusChange->changed_by ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            @endcomponent
    </div>

    <script src="{{ asset('js/approved_bookings.js') }}"></script>
@else
    <h1 class="text text-center py-5 ">ไม่พบข้อมูลในระบบ</h1>
    @endif
@endsection
