@extends('layouts.layout')
@section('title', 'ตรวจสอบสถานะการจอง')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/checkbookingstatus.css') }}">
    </head>

    <div class="container pt-5">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="title p-5 text-center">
        <h1 style="color: #C06628; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
            ตรวจสอบสถานะการจอง
        </h1>
    
        <div class="container d-flex justify-content-center">
            <form action="{{ route('searchBookingByEmail') }}" method="POST" class="mb-4" style="max-width: 600px; width: 100%;">
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
                autocomplete="email" required>
                        <button type="submit" class="btn custom-btn mt-3 w-100">ยืนยัน</button>
                    </div>
            </form>
        </div>
    </div>
        @isset($bookings)
            <h3>ผลการค้นหา</h3>
            @if ($bookings->isNotEmpty())
                @component('components.table_checkbookings') 
                        @foreach ($bookings as $item)
                            <tr>
                                <td>{{ $item->activity->activity_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->booking_date)->locale('th')->translatedFormat('j F') }}
                                    {{ \Carbon\Carbon::parse($item->booking_date)->addYears(543)->year }}
                                </td>
                                <td>
                                    @if ($item->tmss)
                                        {{ \Carbon\Carbon::parse($item->tmss->start_time)->format('H:i') }} น. -
                                        {{ \Carbon\Carbon::parse($item->tmss->end_time)->format('H:i') }} น.
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->institute->instituteName }}</td>
                                <td>{{ $item->children_qty + $item->students_qty + $item->adults_qty + $item->kid_qty + $item->disabled_qty + $item->elderly_qty + $item->monk_qty}} คน</td>
                                <td>
                                    @switch($item->status)
                                        @case(0)
                                            <button type="button" class="status-btn-request">รออนุมัติ</button>
                                        @break
                                        @case(1)
                                            <button type="button" class="status-btn-approved">อนุมัติ</button>
                                        @break
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    @endcomponent
            @else
                <p class="text-center text-muted">ไม่มีข้อมูลการจองสำหรับอีเมลนี้</p>
            @endif
        @endisset
    </div>
@endsection
