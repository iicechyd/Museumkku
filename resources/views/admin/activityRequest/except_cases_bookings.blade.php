<!DOCTYPE html>
<html lang="en">
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
            <a href="{{ url('/admin/manage_bookings/activity') }}" class="btn btn-outline-primary">การจองวันนี้</a>
            <a href="{{ url('/admin/request_bookings/activity') }}" class="btn-request-outline">รออนุมัติ</a>
            <a href="{{ url('/admin/approved_bookings/activity') }}" class="btn-approved-outline">อนุมัติ</a>
            <a href="{{ url('/admin/except_cases_bookings/activity') }}" class="btn-except">ยกเลิก</a>
        </div>

        <div class="form col-6">
            <form method="GET" action="{{ route('except_bookings.activity') }}">
                <label for="activity_id">เลือกกิจกรรม</label>
                <select name="activity_id" id="activity_id" class="form-select" onchange="this.form.submit()">
                    <option value="">กรุณาเลือกประเภทการเข้าชม</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->activity_id }}"
                            {{ request('activity_id') == $activity->activity_id ? 'selected' : '' }}>
                            {{ $activity->activity_name }}
                            @if ($activity->countBookings > 0)
                                ({{ $activity->countBookings }})
                            @endif
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
                            <td>
                                @if ($item->tmss)
                                    {{ \Carbon\Carbon::parse($item->tmss->start_time)->format('H:i') }} น. -
                                    {{ \Carbon\Carbon::parse($item->tmss->end_time)->format('H:i') }} น.
                                @else
                                    ไม่มีรอบการเข้าชม
                                @endif
                            </td>
                            <td>
                                {!! $item->status == 3 ? '<button type="button" class="btn-except">ยกเลิก</button>' : '' !!}
                            </td>
                            <td>{{ $item->latestStatusChange->comments ?? 'ไม่มีความคิดเห็น' }}</td>
                            <td>
                                <a href="#detailsModal_{{ $item->booking_id }}" class="text-blue-500" data-toggle="modal">
                                    รายละเอียด
                                </a>
                            </td>
                            <!-- Modal สำหรับแสดงรายละเอียด -->
                            <div class="modal fade" id="detailsModal_{{ $item->booking_id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel">
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
                                            @if (!$item->subActivities->isEmpty())
                                                <p><strong>หลักสูตร:</strong>
                                                    {{ $item->subActivities->pluck('sub_activity_name')->implode(', ') }}
                                                </p>
                                            @endif
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
                                            @if ($item->kid_qty > 0)
                                                <p><strong>เด็กเล็ก :
                                                    </strong>{{ $item->kid_qty }} คน</p>
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
                                                </strong>{{ $item->children_qty + $item->students_qty + $item->adults_qty + $item->kid_qty + $item->disabled_qty + $item->elderly_qty + $item->monk_qty }}
                                                คน</p>
                                            <p><strong>ยอดรวมราคา: </strong>{{ number_format($item->totalPrice, 2) }} บาท</p>

                                            <p><strong>แก้ไขสถานะ: </strong>
                                                @if ($item->latestStatusChange)
                                                    {{ \Carbon\Carbon::parse($item->latestStatusChange->updated_at)->locale('th')->translatedFormat('j F') }}
                                                    {{ \Carbon\Carbon::parse($item->latestStatusChange->updated_at)->year + 543 }}
                                                    เวลา
                                                    {{ \Carbon\Carbon::parse($item->latestStatusChange->updated_at)->format('H:i') }}
                                                    น.
                                                    โดยเจ้าหน้าที่: {{ $item->latestStatusChange->changed_by ?? 'N/A' }}
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
@endsection

</html>
