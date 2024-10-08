@extends('layouts.layout')
@section('title', 'ปฏิทินการจอง')
@section('content')

    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
        <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script> <!-- เพิ่ม Moment.js -->
    </head>

    <div class="container">
        <h1 class="text-center">ปฏิทินการจอง</h1>
        <div id="calendar"></div>
    </div>


    </script>
@endsection
