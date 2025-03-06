@extends('layouts.layout')
@section('title', 'ปฏิทินการจอง')

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('img/logo_page.png') }}">
</head>

@section('content')
    <div class="container pt-4 pb-5">
        <div class="title">
            <h1 class="text-center pt-3" style="color: #489085; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
                ปฏิทินการจอง</h1>
            <div id="calendar"></div>
        </div>
    </div>
    <!-- Modal Calendar -->
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">รายละเอียดการจอง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4 id="eventTitle"></h4>
                    <p><strong id="eventTmssLabel"></strong>
                    <p id="eventTmss"></p>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/calendar.js') }}"></script>
@endsection
</html>
