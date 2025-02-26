@extends('layouts.layout')
@section('title', 'ยกเลิกการจองเข้าชม')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/checkbookingstatus.css') }}">
    </head>

    <div class="container pt-5">
        <h2 class="text-2xl text-center font-bold" style="color: #e61212;">ยกเลิกการจองเข้าชม</h2>
        @if ($booking->status == 3)
            <p class="text-center mb-3 ">ยกเลิกการจองสำเร็จ</p>
        @else
            <p class="text-center mb-3">คุณต้องการยกเลิกการจองนี้หรือไม่?</p>
        @endif
        @component('components.table_checkbookings')
            <tr>
                <td>{{ $booking->activity->activity_name }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->locale('th')->translatedFormat('j F') }}
                    {{ \Carbon\Carbon::parse($booking->booking_date)->addYears(543)->year }}
                </td>
                <td>
                    @if ($booking->timeslot)
                        {{ \Carbon\Carbon::parse($booking->timeslot->start_time)->format('H:i') }} น. -
                        {{ \Carbon\Carbon::parse($booking->timeslot->end_time)->format('H:i') }} น.
                    @else
                        -
                    @endif
                </td>
                <td>
                    <a href="#detailsModal_{{ $booking->booking_id }}" class="text-blue-500 no-underline" data-bs-toggle="modal">
                        รายละเอียด
                    </a>
                </td>
                <td>{{ $booking->children_qty + $booking->students_qty + $booking->adults_qty + $booking->kid_qty + $booking->disabled_qty + $booking->elderly_qty + $booking->monk_qty }}
                    คน</td>
                <td>
                    @switch($booking->status)
                        @case(0)
                            <button type="button" class="status-btn-request">รออนุมัติ</button>
                        @break

                        @case(1)
                            <button type="button" class="status-btn-approved">อนุมัติ</button>
                        @break

                        @case(2)
                            <button type="button" class="status-btn-approved">เข้าชม</button>
                        @break

                        @case(3)
                            <button type="button" class="status-btn-except">ยกเลิก</button>
                        @break
                    @endswitch
                </td>
            </tr>
        @endcomponent
        <!-- Modal สำหรับแสดงรายละเอียด -->
        <div class="modal fade" id="detailsModal_{{ $booking->booking_id }}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">รายละเอียดการจอง -
                            {{ $booking->activity->activity_name }}</h5>
                    </div>
                    <div class="modal-body">
                        @if (!$booking->subActivities->isEmpty())
                            <p><strong>หลักสูตร:</strong>
                                {{ $booking->subActivities->pluck('sub_activity_name')->implode(', ') }}
                            </p>
                        @endif
                        <p><strong>ชื่อหน่วยงาน: </strong>{{ $booking->institute->instituteName }}</p>
                        <p><strong>ที่อยู่หน่วยงาน: </strong>{{ $booking->institute->instituteAddress }}
                            {{ $booking->institute->subdistrict }} {{ $booking->institute->district }}
                            {{ $booking->institute->inputProvince }} {{ $booking->institute->zipcode }}</p>
                        <p><strong>ชื่อผู้ประสานงาน: </strong>{{ $booking->visitor->visitorName }}</p>
                        <p><strong>อีเมลผู้ประสานงาน: </strong>{{ $booking->visitor->visitorEmail }}</p>
                        <p><strong>เบอร์โทรศัพท์: </strong>{{ $booking->visitor->tel }}</p>
                        @if ($booking->children_qty > 0)
                            <p>เด็ก : {{ $booking->children_qty }} คน</p>
                        @endif

                        @if ($booking->students_qty > 0)
                            <p>นร / นศ : {{ $booking->students_qty }} คน</p>
                        @endif

                        @if ($booking->adults_qty > 0)
                            <p><strong>ผู้ใหญ่ / คุณครู : </strong>{{ $booking->adults_qty }} คน</p>
                        @endif

                        @if ($booking->kid_qty > 0)
                            <p>เด็กเล็ก : {{ $booking->kid_qty }} คน</p>
                        @endif

                        @if ($booking->disabled_qty > 0)
                            <p>ผู้พิการ : {{ $booking->disabled_qty }} คน</p>
                        @endif

                        @if ($booking->elderly_qty > 0)
                            <p>ผู้สูงอายุ : {{ $booking->elderly_qty }} คน</p>
                        @endif

                        @if ($booking->monk_qty > 0)
                            <p>พระภิกษุสงฆ์ / สามเณร : {{ $booking->monk_qty }} รูป</p>
                        @endif
                        @if (!empty($booking->note))
                            <p>*หมายเหตุ: {{ $booking->note }}</p>
                        @endif
                        <p><strong>ยอดรวมราคา:</strong> {{ number_format($totalPrice, 2) }} บาท</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
        @if ($booking->status != 3)
            <div class="d-flex justify-content-center mt-4">
                <form action="{{ route('bookings.updateStatus', $booking->booking_id) }}" method="POST"
                    style="display: inline;" id="statusForm_{{ $booking->booking_id }}">
                    @csrf
                    <input type="hidden" name="status" 
                    id="status_{{ $booking->booking_id }}">
                    <input type="hidden" name="actual_children_qty"
                        id="actual_children_qty_{{ $booking->booking_id }}">
                    <input type="hidden" name="actual_students_qty"
                        id="actual_students_qty_{{ $booking->booking_id }}">
                    <input type="hidden" name="actual_adults_qty"
                        id="actual_adults_qty_{{ $booking->booking_id }}">
                    <input type="hidden" name="actual_kid_qty"
                        id="actual_kid_qty_{{ $booking->booking_id }}">
                    <input type="hidden" name="actual_disabled_qty"
                        id="actual_disabled_qty_{{ $booking->booking_id }}">
                    <input type="hidden" name="actual_elderly_qty"
                        id="actual_elderly_qty_{{ $booking->booking_id }}">
                    <input type="hidden" name="actual_monk_qty" 
                        id="actual_monk_qty_{{ $booking->booking_id }}">
                    <input type="hidden" name="comments" 
                        id="comments_{{ $booking->booking_id }}">
                    <div class="flex items-center space-x-3">
                        <button type="button" class="btn btn-danger"
                            onclick="openCancelModal({{ $booking->booking_id }})">
                            ยกเลิกการจอง
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <!-- Modal สำหรับยกเลิกการจอง -->
    <div class="modal fade" id="cancelModal_{{ $booking->booking_id }}" tabindex="-1" role="dialog"
        aria-labelledby="cancelModalLabel" aria-modal="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <h5 class="modal-title" id="cancelModalLabel">กรอกหมายเหตุการยกเลิก</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="reason_{{ $booking->booking_id }}">กรุณาระบุหมายเหตุ</label>
                    <textarea id="reason_{{ $booking->booking_id }}" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger"
                        onclick="submitCancelForm({{ $booking->booking_id }})">ยืนยันการยกเลิก</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/cancel_bookings.js') }}"></script>
@endsection
