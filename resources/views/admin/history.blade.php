@extends('layouts.layout_admin')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/history.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    </head>

    <div class="container">
        <h1 class="text-center pt-3" style="color: #489085; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
            ประวัติการจองทั้งหมด</h1>
        <form action="{{ route('booking.history.all') }}" method="GET" class="mb-4">
            <button type="submit" name="daily" value="true" class="btn btn-primary">รายวัน</button>
            <button type="submit" name="monthly" value="true" class="btn btn-secondary">เดือนนี้</button>
            <button type="submit" name="fiscal_year" value="true" class="btn btn-warning"
                style="color: white;">ปีงบประมาณ</button>
            <button type="button" id="toggleDateRange" class="btn btn-success">ช่วงวันที่</button>
            <div id="dateRangeFields" class="flex gap-2 py-2 items-center"
                style="display: {{ request('date_range') ? 'flex' : 'none' }};">
                <input type="text" name="date_range" id="date_range" class="border p-1 rounded w-64"
                    value="{{ request('date_range') }}" placeholder="กรุณาเลือกช่วงวันที่ (วัน/เดือน/ปี)">
                <button type="submit" class="btn btn-success">ค้นหา</button>
            </div>

            <div class="row g-3 justify-content-center">
                <div class="col-12 col-md-auto d-flex flex-column flex-md-row align-items-md-center pt-2">
                    <label for="activity_name" class="me-md-2 mb-0 text-nowrap">กิจกรรม</label>
                    <select name="activity_name" id="activity_name" class="form-control w-100 w-md-auto">
                        <option value="">ทั้งหมด</option>
                        @foreach ($activities as $activity_id => $activity_name)
                            <option value="{{ $activity_name }}"
                                {{ request('activity_name') == $activity_name ? 'selected' : '' }}>
                                {{ $activity_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- เลือกสถานะการจอง -->
                <div class="col-12 col-md-auto d-flex flex-column flex-md-row align-items-md-center">
                    <label for="status" class="me-md-2 mb-0 text-nowrap">สถานะการจอง</label>
                    <select name="status" id="status" class="form-control w-100 w-md-auto">
                        <option value="">ทั้งหมด</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>เข้าชม</option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>ยกเลิก</option>
                    </select>
                </div>
            </div>
        </form>

        @if ($histories->isEmpty())
            <h2 class="text-center">ไม่มีประวัติการจอง</h2>
        @else
            <section class="intro">
                <div class="mask d-flex align-items-center">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                {{ $histories->links() }}
                                <div class="table-responsive bg-white shadow-sm rounded">
                                    <table class="table mb-0">
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
                                            @php
                                                $cancelledBookings = 0;
                                            @endphp
                                            @foreach ($histories->sortBy('booking_date') as $index => $item)
                                                @foreach ($item->statusChanges as $statusChange)
                                                    <tr>
                                                        <th scope="row">{{ $loop->parent->iteration }}</th>
                                                        <td>{{ \Carbon\Carbon::parse($item->booking_date)->locale('th')->translatedFormat('j F') }}
                                                            {{ \Carbon\Carbon::parse($item->booking_date)->year + 543 }}
                                                        </td>
                                                        <td>
                                                            @if ($item->timeslot)
                                                                {{ \Carbon\Carbon::parse($item->timeslot->start_time)->format('H:i') }}
                                                                น. -
                                                                {{ \Carbon\Carbon::parse($item->timeslot->end_time)->format('H:i') }}
                                                                น.
                                                            @else
                                                                ไม่มีรอบการเข้าชม
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="#detailsModal_{{ $item->booking_id }}"
                                                                class="text-blue-500" data-toggle="modal">
                                                                รายละเอียด
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if ($statusChange->new_status == 2)
                                                                <button type="button" class="status-btn">เข้าชม</button>
                                                            @elseif ($statusChange->new_status == 3)
                                                                <button type="button" class="btn-except">ยกเลิก</button>
                                                                @php
                                                                    $cancelledBookings++;
                                                                @endphp
                                                            @else
                                                                {{ $statusChange->new_status }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $totalQty =
                                                                    $statusChange->actual_children_qty +
                                                                    $statusChange->actual_students_qty +
                                                                    $statusChange->actual_adults_qty +
                                                                    $statusChange->actual_kid_qty +
                                                                    $statusChange->actual_disabled_qty +
                                                                    $statusChange->actual_elderly_qty +
                                                                    $statusChange->actual_monk_qty;
                                                            @endphp
                                                            @if ($totalQty)
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#actualModal_{{ $statusChange->booking_id }}">
                                                                    {{ $totalQty }} คน
                                                                </a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $statusChange->comments ? $statusChange->comments . ' ' : '-' }}
                                                        </td>
                                                    </tr>
                                                    <!-- Modal for details -->
                                                    <div class="modal fade" id="detailsModal_{{ $item->booking_id }}"
                                                        tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                                        รายละเอียดการจอง -
                                                                        {{ $item->activity->activity_name }}</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>วันเวลาที่จองเข้ามา:</strong>
                                                                        {{ \Carbon\Carbon::parse($item->created_at)->locale('th')->translatedFormat('j F') }}
                                                                        {{ \Carbon\Carbon::parse($item->created_at)->year + 543 }}
                                                                        เวลา
                                                                        {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}
                                                                        น.</p>
                                                                    <p><strong>กิจกรรม:</strong>
                                                                        {{ $item->activity->activity_name }}</p>
                                                                    @if (!$item->subActivities->isEmpty())
                                                                        <p><strong>หลักสูตร:</strong>
                                                                            @foreach ($item->subActivities as $subactivity)
                                                                                {{ $subactivity->sub_activity_name }}
                                                                            @endforeach
                                                                        </p>
                                                                    @endif
                                                                    <p><strong>ชื่อหน่วยงาน:</strong>
                                                                        {{ $item->institute->instituteName }}</p>
                                                                    <p><strong>ที่อยู่หน่วยงาน:</strong>
                                                                        {{ $item->institute->instituteAddress }}
                                                                        {{ $item->institute->subdistrict }}
                                                                        {{ $item->institute->district }}
                                                                        {{ $item->institute->inputProvince }}
                                                                        {{ $item->institute->zipcode }}</p>
                                                                    <p><strong>ชื่อผู้ประสานงาน:</strong>
                                                                        {{ $item->visitor->visitorName }}</p>
                                                                    <p><strong>อีเมลผู้ประสานงาน:</strong>
                                                                        {{ $item->visitor->visitorEmail }}</p>
                                                                    <p><strong>เบอร์โทรศัพท์:</strong>
                                                                        {{ $item->visitor->tel }}</p>
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
                                                                        <p><strong>*หมายเหตุ: </strong>{{ $item->note }}
                                                                        </p>
                                                                    @endif
                                                                    <p><strong>จำนวนผู้เข้าชมทั้งหมด:
                                                                        </strong>{{ $item->children_qty + $item->students_qty + $item->adults_qty + $item->kid_qty + $item->disabled_qty + $item->elderly_qty + $item->monk_qty }}
                                                                        คน</p>
                                                                    <p><strong>ยอดรวมราคา:</strong>
                                                                        {{ number_format($item->totalPrice, 2) }} บาท</p>
                                                                    <p><strong>แก้ไขสถานะ:</strong>
                                                                        {{ \Carbon\Carbon::parse($item->updated_at)->locale('th')->translatedFormat('j F') }}
                                                                        {{ \Carbon\Carbon::parse($item->updated_at)->year + 543 }}
                                                                        เวลา
                                                                        {{ \Carbon\Carbon::parse($item->updated_at)->format('H:i') }}
                                                                        น.
                                                                        โดย: {{ $statusChange->changed_by ?? 'ผู้เข้าชม' }}
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">ปิด</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Modal Actual -->
                                                    <div id="actualModal_{{ $statusChange->booking_id }}"
                                                        class="modal fade" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">รายละเอียดจำนวนผู้เข้าชม</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    @if ($item->activity && !empty($priceDetails))
                                                                        @foreach ($priceDetails as $detail)
                                                                            <p>{{ $detail['label'] }} {{ $detail['qty'] }}
                                                                                คน
                                                                                @if ($detail['price'] > 0)
                                                                                    x {{ number_format($detail['price']) }}
                                                                                    บาท =
                                                                                    {{ number_format($detail['total']) }}
                                                                                    บาท
                                                                                @endif
                                                                            </p>
                                                                        @endforeach
                                                                        @if ($totalPrice > 0)
                                                                            <hr>
                                                                            <p><strong>ยอดรวมราคา:</strong>
                                                                                {{ number_format($totalPrice) }} บาท</p>
                                                                            <p><strong>ยอดรวมผู้เข้าชมจริงทั้งหมด:</strong>
                                                                                {{ number_format($totalParticipants) }} คน
                                                                            </p>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">ปิด</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                            <tr>
                                                <th scope="row" colspan="5" class="text-center">รวมยอดรายได้ทั้งหมด
                                                </th>
                                                <td class="font-bold">{{ number_format($totalRevenue, 2) }} บาท</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-3 pt-3">
                                        <div class="card text-center shadow-sm rounded">
                                            <div class="card-body">
                                                <h5 class="font-bold">จำนวนการจองทั้งหมด</h5>
                                                <h3 class="text-primary"> {{ $totalBookings }} รายการ</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pt-3">
                                        <div class="card text-center shadow-sm rounded">
                                            <div class="card-body">
                                                <h5 class="font-bold">จำนวนการจองที่ถูกเลิก</h5>
                                                <h3 class="text-danger"> {{ $cancelledBookings }} รายการ</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pt-3">
                                        <div class="card text-center shadow-sm rounded">
                                            <div class="card-body">
                                                <h5 class="font-bold">ยอดผู้เข้าชมที่จอง</h5>
                                                <h3 class="text-warning"> {{ $totalBookedVisitors }} คน</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pt-3">
                                        <div class="card text-center shadow-sm rounded">
                                            <div class="card-body">
                                                <h5 class="font-bold">ยอดผู้เข้าชมจริง</h5>
                                                <h3 class="text-success"> {{ $totalActualVisitors }} คน</h3>
                                            </div>
                                        </div>
                                    </div>
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
    <script src="{{ asset('js/history.js') }}"></script>
@endsection
