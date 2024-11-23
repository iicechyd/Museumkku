@extends('layouts.layout')

@section('title', 'สถานะการจอง')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center py-3">สถานะการจองของคุณ</h2>
        @if ($booking)
            <div class="card shadow p-4">
                <h4>รายละเอียดการจอง</h4>
                <p><strong>ประเภทเข้าชม:</strong> {{ $booking->activity->activity_name }}</p>
                {{-- <p><strong>รอบการเข้าชม:</strong> {{ $booking->timeslot->start_time }} - {{ $booking->timeslot->end_time }}</p> --}}
                <p><strong>วันที่จอง:</strong> {{ $booking->booking_date }}</p>
                <p><strong>ชื่อหน่วยงาน:</strong> {{ $booking->institute->instituteName }}</p>
                <p><strong>จำนวนผู้เข้าชม:</strong> {{ $booking->children_qty }}</p>
                <p><strong>สถานะการจอง:</strong> {{ $booking->status }}</p>
                
            </div>
        @else
            <p>ไม่พบข้อมูลการจองที่เกี่ยวข้อง</p>
        @endif
    </div>
@endsection
