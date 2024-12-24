@extends('layouts.layout')
@section('title', 'ปฏิทินการจอง')
@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/th.global.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

    <div class="container pt-4 pb-5">
        <div class="title">
            <h1 class="text-center" style="color: #489085; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">ปฏิทินการจอง</h1>
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">รายละเอียดการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 id="eventTitle"></h4>
                <p id="eventStatus"></p>
                <p><strong id="eventTimeslotLabel">รอบการเข้าชม:</strong> <span id="eventTimeslot"></span></p>
                <p><strong id="eventTimeslotLabel">จำนวนที่นั่งคงเหลือ:</strong> <span id="eventRemainingCapacity"></span></p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/calendar.js') }}"></script>

@endsection
