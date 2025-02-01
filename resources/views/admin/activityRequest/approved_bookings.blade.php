<!DOCTYPE html>
<html lang="en">
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
            <a href="{{ url('/admin/manage_bookings/activity') }}" class="btn btn-outline-primary">การจองวันนี้</a>
            <a href="{{ url('/admin/request_bookings/activity') }}" class="btn-request-outline">รออนุมัติ</a>
            <a href="{{ url('/admin/approved_bookings/activity') }}" class="btn btn-success">อนุมัติ</a>
            <a href="{{ url('/admin/except_cases_bookings/activity') }}" class="btn-except-outline">ยกเลิก</a>
        </div>

        <div class="form col-6">
            <form method="GET" action="{{ route('approved_bookings.activity') }}">
                <label for="activity_id">เลือกกิจกรรม</label>
                <select name="activity_id" id="activity_id" class="form-select" onchange="this.form.submit()">
                    <option value="">กรุณาเลือกประเภทการเข้าชม</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->activity_id }}"
                            {{ request('activity_id') == $activity->activity_id ? 'selected' : '' }}>
                            {{ $activity->activity_name }}
                            @if ($activity->countBookings > 0)
                                ({{ $activity->countBookings}})
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

            @if (count($approvedBookings) > 0)
                <h1 class="table-heading text-center">{{ $selectedActivityName }}</h1>
                {{ $approvedBookings->appends(request()->query())->links() }}

                @component('components.table_approved_bookings')
                    @foreach ($approvedBookings as $item)
                        <tr>
                            <td>{{ $item->booking_id }}</td>
                            <td class="custom-td">
                                {{ \Carbon\Carbon::parse($item->booking_date)->locale('th')->translatedFormat('j F') }}
                                {{ \Carbon\Carbon::parse($item->booking_date)->addYears(543)->year }}
                            </td>
                            <td>
                                @if ($item->timeslot)
                                    {{ \Carbon\Carbon::parse($item->timeslot->start_time)->format('H:i') }} น. -
                                    {{ \Carbon\Carbon::parse($item->timeslot->end_time)->format('H:i') }} น.
                                @else
                                    ไม่มีรอบการเข้าชม
                                @endif
                            </td>
                            <td>
                                @if ($item->activity->max_capacity !== null)
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
                                {!! $item->status == 1 ? '<button type="button" class="status-btn">อนุมัติ</button>' : '' !!}
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
                                @if ($item->documents->isNotEmpty())
                                    <p class="text-success pt-2">แนบไฟล์เอกสารเรียบร้อย</p>
                                @else
                                    <p class="text-danger pt-2">รอแนบเอกสาร</p>
                                @endif
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
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">ปิด</button>
                                            <button type="button" class="btn btn-danger"
                                                onclick="submitCancelForm({{ $item->booking_id }})">บันทึก</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                            <p><strong>แนบเอกสาร: </strong>
                                                @if ($item->documents->isNotEmpty())
                                                    @foreach ($item->documents as $document)
                                                        <span class="mr-2">
                                                            <a href="{{ asset('storage/' . $document->file_path) }}"
                                                                target="_blank">
                                                                {{ $document->file_name }}
                                                            </a>
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-danger">รอแนบเอกสาร</span>
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
<script src="{{ asset('js/approved_bookings.js') }}"></script>
@endsection
</html>