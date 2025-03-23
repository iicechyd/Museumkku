<!DOCTYPE html>
<html lang="en">
@extends('layouts.layout_admin')
@section('title', 'อนุมัติการจองเข้าชมพิพิธภัณฑ์')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/manage_bookings.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">
        <div class="button pb-2">
            <a href="{{ url('/admin/manage_bookings/general') }}" class="btn btn-primary">การจองวันนี้</i></a>
            <a href="{{ url('/admin/request_bookings/general') }}" class="btn-request-outline">รออนุมัติ</a>
            <a href="{{ url('/admin/approved_bookings/general') }}" class="btn-approved-outline">อนุมัติ</a>
            <a href="{{ url('/admin/except_cases_bookings/general') }}" class="btn-except-outline">ยกเลิก</a>
        </div>

        <div class="form col-6">
            <form method="GET" action="{{ route('today_bookings.general') }}">
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
                                {!! $item->status == 1 ? '<button type="button" class="status-btn">อนุมัติ</button>' : '' !!}
                            </td>
                            <td>
                                <form action="{{ route('bookings.updateStatus', $item->booking_id) }}" method="POST"
                                    style="display: inline;" id="statusForm_{{ $item->booking_id }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approve" id="status_{{ $item->booking_id }}">
                                    <input type="hidden" name="actual_children_qty"
                                        id="actual_children_qty_{{ $item->booking_id }}">
                                    <input type="hidden" name="actual_students_qty"
                                        id="actual_students_qty_{{ $item->booking_id }}">
                                    <input type="hidden" name="actual_adults_qty"
                                        id="actual_adults_qty_{{ $item->booking_id }}">
                                    <input type="hidden" name="actual_kid_qty" id="actual_kid_qty_{{ $item->booking_id }}">
                                    <input type="hidden" name="actual_disabled_qty"
                                        id="actual_disabled_qty_{{ $item->booking_id }}">
                                    <input type="hidden" name="actual_elderly_qty"
                                        id="actual_elderly_qty_{{ $item->booking_id }}">
                                    <input type="hidden" name="actual_monk_qty" id="actual_monk_qty_{{ $item->booking_id }}">
                                    <input type="hidden" name="actual_free_teachers_qty"
                                        id="actual_free_teachers_qty_{{ $item->booking_id }}">
                                    <input type="hidden" name="comments" id="comments_{{ $item->booking_id }}">

                                    <div class="flex items-center space-x-3">
                                        <button type="button" class="btn btn-primary"
                                            onclick="openVisitorModal({{ $item->booking_id }})">
                                            เข้าชม
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
                                @if ($item->documents->isNotEmpty())
                                    <p class="text-success pt-2">แนบไฟล์เอกสารเรียบร้อย
                                        <a class="ml-2" data-bs-toggle="modal"
                                            data-bs-target="#documentModal-{{ $item->booking_id }}">
                                            <i class="fas fa-edit" style="color: #5e81ff;"></i>
                                        </a>
                                    </p>
                                @else
                                    <p class="text-danger pt-2">รอแนบเอกสาร
                                        <a class="ml-2" data-bs-toggle="modal"
                                            data-bs-target="#documentModal-{{ $item->booking_id }}">
                                            <i class="fas fa-edit" style="color: #5e81ff;"></i>
                                        </a>
                                    </p>
                                @endif
                            </td>
                            <!-- Modal สำหรับกรอกจำนวนผู้เข้าชม -->
                            <div class="modal fade" id="visitorModal_{{ $item->booking_id }}" tabindex="-1" role="dialog"
                                aria-labelledby="visitorModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="visitorModalLabel">กรอกจำนวนผู้เข้าชม</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            @php
                                                $general_prices = [
                                                    'children' => $item->activity->children_price ?? 0,
                                                    'students' => $item->activity->student_price ?? 0,
                                                    'adults' => $item->activity->adult_price ?? 0,
                                                ];

                                                $welfare_prices = [
                                                    'kid' => $item->activity->kid_price ?? 0,
                                                    'disabled' => $item->activity->disabled_price ?? 0,
                                                    'elderly' => $item->activity->elderly_price ?? 0,
                                                    'monk' => $item->activity->monk_price ?? 0,
                                                ];

                                                $labels = [
                                                    'children' => 'เด็ก',
                                                    'students' => 'มัธยม / นักศึกษา',
                                                    'adults' => 'ผู้ใหญ่ / คุณครู',
                                                    'kid' => 'เด็กเล็ก',
                                                    'disabled' => 'ผู้พิการ',
                                                    'elderly' => 'ผู้สูงอายุ',
                                                    'monk' => 'พระภิกษุสงฆ์ /สามเณร',
                                                ];
                                            @endphp

                                            {{-- กลุ่มทั่วไป --}}
                                            <h5 class="mb-3">กลุ่มทั่วไป</h5>
                                            @foreach ($general_prices as $type => $price)
                                                <div class="d-flex justify-content-center align-items-center mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <label class="mr-2 mb-0"
                                                            for="{{ $type }}_qty_{{ $item->booking_id }}"
                                                            style="width: 100px;">
                                                            {{ $labels[$type] }}
                                                        </label>
                                                        <input type="number"
                                                            id="{{ $type }}_qty_{{ $item->booking_id }}"
                                                            class="form-control visitor-input text-center"
                                                            style="width: 70px; padding: 5px;" min="0"
                                                            value="{{ $item->{$type . '_qty'} ?? 0 }}"
                                                            data-price="{{ $price }}"
                                                            data-booking-id="{{ $item->booking_id }}"
                                                            oninput="calculateTotal({{ $item->booking_id }})" required>
                                                        <label class="ml-3" style="margin-left: 10px;">คน</label>
                                                    </div>
                                                    <span class="text-right" style="margin-left: 80px; width: 120px;">
                                                        @if ($price != 0)
                                                            {{ number_format($price) }} บาท/คน
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach

                                            {{-- สวัสดิการ --}}
                                            <h5 class="mt-4 mb-3">สวัสดิการ</h5>
                                            @foreach ($welfare_prices as $type => $price)
                                                <div class="d-flex justify-content-center align-items-center mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <label class="mr-2 mb-0"
                                                            for="{{ $type }}_qty_{{ $item->booking_id }}"
                                                            style="width: 100px;">
                                                            {{ $labels[$type] }}
                                                        </label>
                                                        <input type="number"
                                                            id="{{ $type }}_qty_{{ $item->booking_id }}"
                                                            class="form-control visitor-input text-center"
                                                            style="width: 70px; padding: 5px;" min="0"
                                                            value="{{ $item->{$type . '_qty'} ?? 0 }}"
                                                            data-price="{{ $price }}"
                                                            data-booking-id="{{ $item->booking_id }}"
                                                            oninput="calculateTotal({{ $item->booking_id }})" required>
                                                        <label class="ml-3"
                                                            style="margin-left: 10px;">{{ $type === 'monk' ? 'รูป' : 'คน' }}</label>
                                                    </div>
                                                    <span class="text-right" style="margin-left: 80px; width: 120px;">
                                                        @if ($price != 0)
                                                            {{ number_format($price) }}
                                                            {{ $type === 'monk' ? 'บาท/รูป' : 'บาท/คน' }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                            @php
                                                $maxFreeTeachers = 0;
                                            @endphp
                                            <div class="d-flex justify-content-center align-items-center mb-2">
                                                <div class="d-flex align-items-center">
                                                    <label class="mr-2 mb-0" for="teacher_free_qty_{{ $item->booking_id }}"
                                                        style="width: 100px;">
                                                        คุณครู
                                                    </label>
                                                    <input type="number" id="teacher_free_qty_{{ $item->booking_id }}"
                                                        class="form-control visitor-input text-center"
                                                        style="width: 70px; padding: 5px;" min="0"
                                                        max="{{ $maxFreeTeachers }}" value="0" data-price="0"
                                                        data-booking-id="{{ $item->booking_id }}"
                                                        oninput="validateFreeTeachersInput(this, {{ $item->booking_id }}); calculateTotal({{ $item->booking_id }});"
                                                        required>
                                                    <label class="ml-3" style="margin-left: 10px;">คน</label>
                                                </div>
                                                <span class="text-right" style="margin-left: 80px; width: 120px;">
                                                    ฟรี (สูงสุด <span id="maxFreeTeachers_{{ $item->booking_id }}">0</span>
                                                    คน)
                                                </span>
                                            </div>
                                            <div class="col-12 mt-4">
                                                <h5>จำนวนผู้เข้าชมทั้งหมด: <span
                                                        id="totalVisitors_{{ $item->booking_id }}">0</span> <span
                                                        id="totalUnit_{{ $item->booking_id }}">คน</span></h5>
                                                <h5>ราคารวม: <span id="totalPrice_{{ $item->booking_id }}">0.00</span> บาท
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">ปิด</button>
                                            <button type="button" class="btn btn-success"
                                                onclick="submitVisitorCount({{ $item->booking_id }})">บันทึก</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                            <p><strong>อนุมัติ: </strong>
                                                @if ($item->note == 'วอคอิน')
                                                    {{ \Carbon\Carbon::parse($item->updated_at)->locale('th')->translatedFormat('j F') }}
                                                    {{ \Carbon\Carbon::parse($item->updated_at)->year + 543 }}
                                                    เวลา
                                                    {{ \Carbon\Carbon::parse($item->updated_at)->format('H:i') }}
                                                    น.
                                                    โดย: {{ $item->user->name ?? 'N/A' }}
                                                @elseif ($item->latestStatusChange)
                                                    {{ \Carbon\Carbon::parse($item->latestStatusChange->updated_at)->locale('th')->translatedFormat('j F') }}
                                                    {{ \Carbon\Carbon::parse($item->latestStatusChange->updated_at)->year + 543 }}
                                                    เวลา
                                                    {{ \Carbon\Carbon::parse($item->latestStatusChange->updated_at)->format('H:i') }}
                                                    น.
                                                    โดย: {{ $item->latestStatusChange->user->name ?? 'N/A' }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">ปิด</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal ไฟล์เอกสาร-->
                            <div class="modal fade" id="documentModal-{{ $item->booking_id }}" tabindex="-1"
                                aria-labelledby="documentModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="documentModalLabel">เอกสารแนบ</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @foreach ($item->documents as $document)
                                                <p>
                                                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank"
                                                        class="text-primary">
                                                        {{ $document->file_name }}
                                                    </a>
                                                <form action="{{ route('documents.destroy', $document->document_id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('คุณต้องการลบเอกสารนี้หรือไม่?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="border-0 bg-transparent delete-button">
                                                        <i class="fa-solid fa-circle-xmark text-danger"
                                                            style="font-size: 1.2rem;"></i>
                                                    </button>
                                                </form>
                                                </p>
                                            @endforeach
                                            <form action="{{ route('documents.store', $item->booking_id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <input type="file" name="document" accept=".pdf" id="document"
                                                            class="form-control" required
                                                            style="flex-grow: 1; margin-right: 10px;"
                                                            onchange="previewFile()">
                                                        <button type="submit" class="btn btn-primary ml-3"
                                                            style="min-width: 100px;">อัปโหลด</button>
                                                    </div>
                                                    <label for="document" class="form-label text-danger mt-2">อัปโหลดเอกสาร
                                                        (PDF เท่านั้น)
                                                    </label>
                                                    <div id="file-preview"></div>
                                                </div>
                                            </form>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">ปิด</button>
                                            </div>
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
        var approvedBookings = @json($approvedBookings->pluck('booking_id'));
    </script>
    <script>
        $(document).ready(function() {
            $('[data-dismiss="modal"]').on('click', function() {
                var modalId = $(this).closest('.modal').attr('id');
                $('#' + modalId).modal('hide');
            });
        });
    </script>
    <script src="{{ asset('js/manage_bookings.js') }}"></script>
@endsection

</html>
