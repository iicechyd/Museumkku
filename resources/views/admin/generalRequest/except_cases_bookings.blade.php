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
                        <td>
                            <form action="{{ route('bookings.updateStatus', $item->booking_id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                <div class="flex items-center space-x-3">
                                    <select name="status" id="statusSelect" onchange="toggleCommentsAndButton()"
                                        class="bg-gray-100 border border-gray-300 rounded-lg p-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="pending" {{ $item->status == 0 ? 'selected' : '' }}>รออนุมัติ</option>
                                        <option value="approve" {{ $item->status == 1 ? 'selected' : '' }}>อนุมัติ</option>
                                        <option value="cancel" {{ $item->status == 2 ? 'selected' : '' }}>ยกเลิก</option>
                                    </select>

                                    {{-- <!-- ฟิลด์ comments ที่จะถูกซ่อนไว้ในตอนแรก -->
                                    <div id="commentsField" class="comments-field">
                                        <input type="text" name="comments" placeholder="กรอกความคิดเห็น"
                                            class="bg-gray-100 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                    </div> --}}
                                    <button type="submit" class="button-custom">
                                        อัปเดตสถานะ
                                    </button>
                                </div>
                            </form>
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
@else
    <h1 class="text text-center py-5 ">ไม่พบข้อมูลในระบบ</h1>
    @endif

@endsection
