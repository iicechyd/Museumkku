@extends('layouts.layout')
@section('title', 'เบิ่งบ่ ระบบจองเข้าชมพิพิธภัณฑ์')
@section('content')
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
    <div class="container pt-3">
        <div class="title p-5 text-center">
            <h1 style="color: #489085; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); 
">
                ระบบจองเข้าชมศูนย์พิพิธภัณฑ์</h1>
            <h2 style="color: #E6A732; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); 
">
                และแหล่งเรียนรู้ตลอดชีวิต <span style="color: #C06628;">มหาวิทยาลัยขอนแก่น</span>
            </h2>
            <div class="d-flex flex-column flex-md-row justify-content-center gap-2 mt-3">
                <a href="{{ url('/preview_general') }}" class="btn"
                    style="background-color: #489085; color: #fff; font-size: 1.6rem; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); font-family: 'Noto Sans Thai', sans-serif; font-weight: 700;">+
                    จองเข้าชมพิพิธภัณฑ์</a>
                <a href="{{ url('/preview_activity') }}" class="btn"
                    style="background-color: #E6A732; color: #fff; font-size: 1.6rem; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); font-family: 'Noto Sans Thai', sans-serif; font-weight: 700;">+
                    จองเข้าร่วมกิจกรรม</a>
            </div>
            <div class="d-flex flex-column flex-md-row justify-content-center mt-2">
                <a href="{{ url('/checkBookingStatus') }}" class="btn"
                    style="background-color: #C06628; color: #fff; font-size: 1.6rem; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); font-family: 'Noto Sans Thai', sans-serif; font-weight: 700;">
                    ตรวจสอบสถานะการจอง</a>

            </div>
            <div class="d-flex flex-column flex-md-row justify-content-center mt-3">
                <h5>
                    <i class="fa-solid fa-arrow-right" style="color: #ff0000;"></i>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#pdfModal">ขั้นตอนการจองเข้าชม</a>
                </h5>
            </div>
            <!-- Modal แสดง PDF -->
            <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="pdfModalLabel">ขั้นตอนการจองเข้าชม</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <embed src="/assets/pdf/ManualBooking.pdf" width="100%" height="500px"></embed>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myModal = new bootstrap.Modal(document.getElementById('pdfModal'));
            myModal.show();
        });
    </script>
@endsection
