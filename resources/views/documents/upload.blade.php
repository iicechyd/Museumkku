@extends('layouts.layout')
@section('title', 'อัปโหลดไฟล์ขอความอนุเคราะห์')
@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

    <head>
        <link rel="stylesheet" href="{{ asset('css/checkbookingstatus.css') }}">
    </head>
    
    <div class="container mt-5">
        <h1 class="text-center" style="color: #C06628; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
            ตรวจสอบสถานะการจอง
        </h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ชื่อกิจกรรม</th>
                    <th>วันที่จอง</th>
                    <th>รอบการเข้าชม</th>
                    <th>ชื่อหน่วยงาน</th>
                    <th>จำนวนผู้เข้าชมทั้งหมด</th>
                    <th>สถานะ</th>
                    <th>แนบใบขอความอนุเคราะห์</th>
                </tr>
            </thead>
            <tbody>
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
                    <td>{{ $booking->institute->instituteName }}</td>
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
                                <button type="button" class="status-btn-except">ยกเลิก</button>
                            @break
                        @endswitch
                    </td>
                    <td>
                        @if ($booking->documents->isNotEmpty())
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
                                        required style="flex-grow: 1; margin-right: 10px;">
                                        <button type="submit" class="btn btn-primary ml-3" style="min-width: 100px;">อัปโหลด</button>
                                </div>    
                                <label for="document" class="form-label text-danger mt-2">อัปโหลดเอกสาร (PDF เท่านั้น)</label>
                            </div>   
                            </form>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
