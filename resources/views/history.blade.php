@extends('layouts.layout_admin')

<head>
    <link rel="stylesheet" href="{{ asset('css/history.css') }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

@section('content')

<div class="container">
    <h1 class="text-center pt-3" style="color: #489085; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">ประวัติการจองทั้งหมด</h1>
    <form action="{{ route('booking.history.all') }}" method="GET" class="form-inline mb-4 justify-content-center">
        <div class="form-group">
            <label for="activity_name" class="mr-2">ชื่อกิจกรรม:</label>
            <select name="activity_name" id="activity_name" class="form-control">
                <option value="">ทั้งหมด</option>
                @foreach($activities as $activity_id => $activity_name)
                    <option value="{{ $activity_name }}" {{ request('activity_name') == $activity_name ? 'selected' : '' }}>
                        {{ $activity_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="status" class="mr-2">สถานะการจอง:</label>
            <select name="status" id="status" class="form-control">
                <option value="">ทั้งหมด</option>
                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>เข้าชม</option>
                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>ยกเลิก</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">ค้นหา</button>
    </form>

    @if($histories->isEmpty())
        <h2 class="text-center">ไม่มีประวัติการจอง</h2>
    @else
    <section class="intro">
        <div class="mask d-flex align-items-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="table-responsive bg-white shadow-sm rounded">
                            <table class="table table-striped mb-0">
                                <thead class="thead-white">
                                    <tr>
                                        <th scope="col">รายการที่</th>
                                        <th scope="col">วันที่จองเข้าชม</th>
                                        <th scope="col">รอบการเข้าชม</th>
                                        <th scope="col">รายละเอียดการจอง</th>
                                        <th scope="col">สถานะการจอง</th>
                                        <th scope="col">ยอดผู้เข้าชมจริง</th>
                                        <th scope="col">หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($histories as $item)
                                    <tr>
                                        <th scope="row">{{ $item->booking->booking_id }}</th>
                                        <td>{{ \Carbon\Carbon::parse($item->booking->booking_date)->locale('th')->translatedFormat('j F') }} {{ \Carbon\Carbon::parse($item->booking->booking_date)->year + 543 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->booking->timeslot->start_time)->format('H:i') }} น. - {{ \Carbon\Carbon::parse($item->booking->timeslot->end_time)->format('H:i') }} น.</td>
                                        <td>
                                            <a href="#detailsModal_{{ $item->booking->booking_id }}" class="text-blue-500" data-toggle="modal">
                                                รายละเอียด
                                            </a>
                                        </td>
                                        
                                        <td>
                                            @if ($item->statusChange->new_status == 2)
                                                <button type="button" class="status-btn">เข้าชม</button>
                                            @elseif ($item->statusChange->new_status == 3)
                                                <button type="button" class="btn-except">ยกเลิก</button>
                                            @else
                                                {{ $item->statusChange->new_status }}
                                            @endif
                                        </td>
                                        <td>{{ $item->statusChange->number_of_visitors ? $item->statusChange->number_of_visitors . ' คน' : '-' }}</td>
                                        <td>{{ $item->statusChange->comments ? $item->statusChange->comments . ' ' : '-' }}</td>

                                    </tr>

                                    <!-- Modal for details -->
                                    <div class="modal fade" id="detailsModal_{{ $item->booking->booking_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">รายละเอียดการจอง - {{ $item->booking->activity->activity_name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>วันเวลาที่จองเข้ามา:</strong> {{ \Carbon\Carbon::parse($item->created_at)->locale('th')->translatedFormat('j F') }} {{ \Carbon\Carbon::parse($item->created_at)->year + 543 }} เวลา {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} น.</p>
                                                    <p><strong>ชื่อหน่วยงาน:</strong> {{ $item->booking->institute->instituteName }}</p>
                                                    <p><strong>ที่อยู่หน่วยงาน:</strong> {{ $item->booking->institute->instituteAddress }} {{ $item->booking->institute->subdistrict }} {{ $item->booking->institute->district }} {{ $item->booking->institute->inputProvince }} {{ $item->booking->institute->zipcode }}</p>
                                                    <p><strong>ชื่อผู้ประสานงาน:</strong> {{ $item->booking->visitor->visitorName }}</p>
                                                    <p><strong>อีเมลผู้ประสานงาน:</strong> {{ $item->booking->visitor->visitorEmail }}</p>
                                                    <p><strong>เบอร์โทรศัพท์:</strong> {{ $item->booking->visitor->tel }}</p>
                                                    <p><strong>เด็ก (คน):</strong> {{ $item->booking->children_qty > 0 ? $item->booking->children_qty . ' คน' : '-' }}</p>
                                                    <p><strong>นร / นศ (คน):</strong> {{ $item->booking->students_qty > 0 ? $item->booking->students_qty . ' คน' : '-' }}</p>
                                                    <p><strong>ผู้ใหญ่ / คุณครู (คน):</strong> {{ $item->booking->adults_qty > 0 ? $item->booking->adults_qty . ' คน' : '-' }}</p>
                                                    <p><strong>ผู้พิการ (คน):</strong> {{ $item->booking->disabled_qty > 0 ? $item->disabled_qty . ' คน' : '-' }}</p>
                                                    <p><strong>ผู้สูงอายุ (คน):</strong> {{ $item->booking->elderly_qty > 0 ? $item->booking->elderly_qty . ' คน' : '-' }}</p>
                                                    <p><strong>พระภิกษุสงฆ์ / สามเณร (คน):</strong> {{ $item->booking->monk_qty > 0 ? $item->booking->monk_qty . ' รูป' : '-' }}</p>
                                                    <p><strong>จำนวนคนทั้งหมด:</strong> {{ $item->booking->children_qty + $item->booking->students_qty + $item->booking->adults_qty + $item->booking->disabled_qty + $item->booking->elderly_qty + $item->booking->monk_qty }} คน</p>
                                                    <p><strong>ยอดรวมราคา:</strong> {{ number_format($item->booking->totalPrice, 2) }} บาท</p>
                                                    <p><strong>แก้ไขสถานะ:</strong> {{ \Carbon\Carbon::parse($item->statusChange->status_updated_at)->locale('th')->translatedFormat('j F') }} {{ \Carbon\Carbon::parse($item->statusChange->status_updated_at)->year + 543 }} เวลา {{ \Carbon\Carbon::parse($item->statusChange->status_updated_at)->format('H:i') }} น. โดยเจ้าหน้าที่: {{ $item->statusChange->changed_by ?? 'N/A' }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
@endsection
