<!DOCTYPE html>
<html lang="en">
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
            <a href="{{ url('/admin/manage_bookings/general') }}" class="btn btn-outline-primary">การจองวันนี้</a>
            <a href="{{ url('/admin/request_bookings/general') }}" class="btn-request">รออนุมัติ</a>
            <a href="{{ url('/admin/approved_bookings/general') }}" class="btn-approved-outline">อนุมัติ</a>
            <a href="{{ url('/admin/except_cases_bookings/general') }}" class="btn-except-outline">ยกเลิก</a>
        </div>

        <div class="form col-6">
            <form method="GET" action="{{ route('request_bookings.general') }}">
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
            <h1 class="table-heading text-center">{{ $selectedActivityName }}</h1>

            @if (count($requestBookings) > 0)
                {{ $requestBookings->appends(request()->query())->links() }}

                @component('components.table_request_bookings')
                    @foreach ($requestBookings as $item)
                        <tr>
                            <td>{{ $item->booking_id }}</td>
                            <td class="custom-td">
                                {{ \Carbon\Carbon::parse($item->booking_date)->locale('th')->translatedFormat('j F') }}
                                {{ \Carbon\Carbon::parse($item->booking_date)->addYears(543)->year }}
                            </td>
                            <td>
                                @if ($item->note === 'วอคอิน')
                                    วอคอิน
                                @elseif ($item->tmss)
                                    {{ \Carbon\Carbon::parse($item->tmss->start_time)->format('H:i') }} น. -
                                    {{ \Carbon\Carbon::parse($item->tmss->end_time)->format('H:i') }} น.
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($item->note == 'วอคอิน')
                                    -
                                @elseif ($item->activity->max_capacity !== null)
                                    @if ($item->remaining_capacity > 0)
                                        {{ $item->remaining_capacity }} / {{ $item->activity->max_capacity }} คน
                                    @else
                                        รอบการเข้าชมนี้เต็มแล้ว
                                    @endif
                                @else
                                    ไม่จำกัดจำนวนคน
                                @endif
                            </td>
                            <td>
                                {!! $item->status == 0 ? '<button type="button" class="status-btn-request">รออนุมัติ</button>' : '' !!}
                            </td>
                            <td>
                                <form action="{{ route('bookings.updateStatus', $item->booking_id) }}" method="POST"
                                    style="display: inline;" id="statusForm_{{ $item->booking_id }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approve" id="status_{{ $item->booking_id }}">
                                    <input type="hidden" name="number_of_visitors"
                                        id="number_of_visitors_{{ $item->booking_id }}">
                                    <input type="hidden" name="comments" id="comments_{{ $item->booking_id }}">

                                    <div class="flex items-center space-x-3">
                                        <button type="button" class="btn btn-success"
                                            onclick="confirmUpdateStatus(event, {{ $item->booking_id }})">
                                            อนุมัติ
                                        </button>
                                        <button type="button" class="btn btn-danger"
                                            onclick="openCancelModal({{ $item->booking_id }})">
                                            ยกเลิก
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td>
                                <a href="#detailsModal_{{ $item->booking_id }}" class="text-blue-500" data-toggle="modal">
                                    รายละเอียด
                                </a>
                            </td>
                            <!-- Modal สำหรับยกเลิกการจอง -->
                            <div class="modal fade" id="cancelModal_{{ $item->booking_id }}" tabindex="-1" role="dialog"
                                aria-labelledby="cancelModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="cancelModalLabel">กรอกหมายเหตุการยกเลิก</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <label for="reason_{{ $item->booking_id }}">กรุณาระบุหมายเหตุ</label>
                                            <textarea id="reason_{{ $item->booking_id }}" class="form-control"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                                            <button type="button" class="btn btn-danger"
                                                onclick="submitCancelForm({{ $item->booking_id }})">บันทึก</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} น.
                                                โดย: {{ $item->user ? $item->user->name : 'ผู้จองเข้าชม' }}</p>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var requestBookings = @json($requestBookings->pluck('booking_id'));
    </script>
    <script>
        $(document).ready(function() {
            $('[data-dismiss="modal"]').on('click', function() {
                var modalId = $(this).closest('.modal').attr('id');
                $('#' + modalId).modal('hide');
            });
        });
    </script>
    <script src="{{ asset('js/request_bookings.js') }}"></script>
@endsection

</html>
