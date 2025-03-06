@extends('layouts.layout_admin')
@section('title', 'ปฏิทินการจอง')

<head>
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

@section('content')
    <div class="container pt-4 pb-5">
        <div class="title">
            <h1 class="text-center" style="color: #489085; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
                ปฏิทินการจอง</h1>
            <div id="calendar"></div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">รายละเอียดการจอง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4 id="eventTitle"></h4>
                    <p id="eventStatus"></p>
                    <p><strong id="eventTmssLabel">รอบการเข้าชม:</strong> <span id="eventTmss"></span></p>
                    <p><strong>ผู้จอง:</strong> <span id="eventVisitor"></span></p>
                    <p><strong>เบอร์โทรศัพท์:</strong> <span id="eventTel"></span></p>
                    <p><strong>อีเมล:</strong> <span id="eventvisitorEmail"></span></p>
                    <p><strong>หน่วยงาน:</strong> <span id="eventInstitute"></span></p>
                    <p><strong>ที่อยู่หน่วยงาน:</strong> <span id="eventSubdistrict"></span> <span
                    id="eventDistrict"></span> <span id="eventProvince"></span> <span id="eventZipcode"></span></p>
                    <p><strong>จำนวนผู้เข้าชมทั้งหมด:</strong> <span id="eventTotalQty"></span> คน</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/admin_calendar.js') }}"></script>
@endsection
