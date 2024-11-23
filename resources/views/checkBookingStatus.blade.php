@extends('layouts.layout')
@section('title', 'ตรวจสอบสถานะการจอง')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/checkbookingstatus.css') }}">
    </head>

    <div class="container mt-5">
        <h1 class="text-center" style="color: #C06628; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
            ตรวจสอบสถานะการจอง
        </h1>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="container d-flex justify-content-center">
            <form action="{{ route('searchBookingByEmail') }}" method="POST" class="mb-4" style="width: 50%;">
                @csrf
                <div class="form-group text-center">
                    <label for="email" class="mb-2" style="font-weight: bold;">กรุณากรอกอีเมลของคุณ</label>
                    <input 
                type="email" 
                name="email" 
                id="email" 
                class="form-control"
                placeholder="example@example.com" 
                value="{{ old('email', request('email')) }}" 
                required>
                        <button type="submit" class="btn custom-btn mt-3 w-100">ยืนยัน</button>
                    </div>
            </form>
        </div>
        

        @isset($bookings)
            <h3>ผลการค้นหา:</h3>
            @if ($bookings->isNotEmpty())
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ชื่อกิจกรรม</th>
                            <th>วันที่จอง</th>
                            <th>รอบการเข้าชม</th>
                            <th>ชื่อหน่วยงาน</th>
                            <th>จำนวนผู้เข้าชมทั้งหมด</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $item)
                            <tr>
                                <td>{{ $item->activity->activity_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->booking_date)->locale('th')->translatedFormat('j F') }}
                                    {{ \Carbon\Carbon::parse($item->booking_date)->addYears(543)->year }}
                                </td>
                                <td>
                                    @if ($item->timeslot)
                                        {{ \Carbon\Carbon::parse($item->timeslot->start_time)->format('H:i') }} น. -
                                        {{ \Carbon\Carbon::parse($item->timeslot->end_time)->format('H:i') }} น.
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->institute->instituteName }}</td>
                                <td>{{ $item->totalVisitors > 0 ? $item->totalVisitors . ' คน' : '-' }}</td>
                                <td>
                                    @switch($item->status)
                                        @case(0)
                                            <button type="button" class="status-btn-request">รออนุมัติ</button>
                                        @break

                                        @case(1)
                                            <button type="button" class="status-btn-approved">อนุมัติ</button>
                                        @break

                                        @case(2)
                                            <button type="button" class="status-btn-except">ยกเลิก</button>
                                        @break
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-muted">ไม่มีข้อมูลการจองสำหรับอีเมลนี้</p>
            @endif
        @endisset
    </div>
@endsection
