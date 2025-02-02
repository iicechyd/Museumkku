@extends('layouts.layout')
@section('title', 'อัปโหลดไฟล์ขอความอนุเคราะห์')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/checkbookingstatus.css') }}">
    </head>

    @if (session('showSuccessModal'))
        <script>
            var myModal = new bootstrap.Modal(document.getElementById('successModal'), {
                keyboard: false
            });
            myModal.show();
        </script>
    @endif

    <div class="container mt-5">
        <h2 class="text-center" style="color: #C06628; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
            ตรวจสอบสถานะการจอง
        </h2>
        @if ($booking->documents->isNotEmpty())
            <p class="text-center">หากต้องการแก้ไขข้อมูลเพิ่มเติม กรุณาติดต่อ
                096-XXX-XXXX เจ้าหน้าที่ฝ่ายกิจกรรม</p>
        @else
        @endif
        @if ($booking->status == 3)
            <p class="text-center mb-3 ">ไม่สามารถอัปโหลดไฟล์ได้เนื่องจากการจองถูกยกเลิกแล้ว</p>
        @else
        @endif
        @component('components.table_upload')
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
                <td>{{ $booking->children_qty + $booking->students_qty + $booking->adults_qty + $booking->disabled_qty + $booking->elderly_qty + $booking->monk_qty }}
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
                            <button type="button" class="status-btn">เข้าชม</button>
                        @break

                        @case(3)
                            <button type="button" class="status-btn-except">ยกเลิก</button>
                        @break
                    @endswitch
                </td>
                <td>
                    @if ($booking->status == 3)
                        <span class="text-muted">ไม่สามารถอัปโหลดไฟล์ได้</span>
                    @elseif ($booking->documents->isNotEmpty())
                        @foreach ($booking->documents as $document)
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">
                                {{ $document->file_name }}
                            </a>
                        @endforeach
                    @else
                        <form action="{{ route('documents.store', $booking->booking_id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <input type="file" name="document" accept=".pdf" id="document" class="form-control"
                                        required style="flex-grow: 1; margin-right: 10px;" onchange="previewFile()">
                                    <button type="submit" class="btn btn-primary ml-3"
                                        style="min-width: 100px;">อัปโหลด</button>
                                </div>
                                <label for="document" class="form-label text-danger mt-2">อัปโหลดเอกสาร (PDF เท่านั้น)</label>
                                <div id="file-preview"></div>
                            </div>
                        </form>
                    @endif
                </td>
            </tr>
            </tbody>
        @endcomponent
    </div>
    <!-- Modal สำหรับแสดงรายละเอียด -->
    <div class="modal fade" id="detailsModal_{{ $booking->booking_id }}" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">รายละเอียดการจอง -
                        {{ $booking->activity->activity_name }}</h5>
                </div>
                <div class="modal-body">
                    @if ($booking->subActivities->isEmpty())
                    @else
                        @foreach ($booking->subActivities as $subactivity)
                            <p><strong>หลักสูตร: {{ $subactivity->sub_activity_name }}</strong></p>
                        @endforeach
                    @endif
                    <p><strong>ชื่อหน่วยงาน: </strong>{{ $booking->institute->instituteName }}</p>
                    <p><strong>ที่อยู่หน่วยงาน: </strong>{{ $booking->institute->instituteAddress }}
                        {{ $booking->institute->subdistrict }} {{ $booking->institute->district }}
                        {{ $booking->institute->inputProvince }} {{ $booking->institute->zipcode }}</p>
                    <p><strong>ชื่อผู้ประสานงาน: </strong>{{ $booking->visitor->visitorName }}</p>
                    <p><strong>อีเมลผู้ประสานงาน: </strong>{{ $booking->visitor->visitorEmail }}</p>
                    <p><strong>เบอร์โทรศัพท์: </strong>{{ $booking->visitor->tel }}</p>
                    <p><strong>เด็ก (คน):
                        </strong>{{ $booking->children_qty > 0 ? $booking->children_qty . ' คน' : '-' }}</p>
                    <p><strong>นร / นศ (คน):
                        </strong>{{ $booking->students_qty > 0 ? $booking->students_qty . ' คน' : '-' }}</p>
                    <p><strong>ผู้ใหญ่ / คุณครู (คน):
                        </strong>{{ $booking->adults_qty > 0 ? $booking->adults_qty . ' คน' : '-' }}</p>
                    <p><strong>ผู้พิการ (คน):
                        </strong>{{ $booking->disabled_qty > 0 ? $booking->disabled_qty . ' คน' : '-' }}</p>
                    <p><strong>ผู้สูงอายุ (คน):
                        </strong>{{ $booking->elderly_qty > 0 ? $booking->elderly_qty . ' คน' : '-' }}</p>
                    <p><strong>พระภิกษุสงฆ์ / สามเณร (คน):
                        </strong>{{ $booking->monk_qty > 0 ? $booking->monk_qty . ' รูป' : '-' }}</p>
                    <p><strong>ยอดรวมราคา:</strong> {{ number_format($totalPrice, 2) }} บาท</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function previewFile() {
            const file = document.getElementById('document').files[0];
            const preview = document.getElementById('file-preview');
            preview.innerHTML = '';

            if (file) {
                const reader = new FileReader();

            }
            if (file.type === 'application/pdf') {
                const iframe = document.createElement('iframe');
                iframe.src = URL.createObjectURL(file);
                preview.appendChild(iframe);
            }
        }
    </script>
@endsection
