<!DOCTYPE html>
<html lang="en">
@extends('layouts.layout')
@section('title', 'แก้ไขข้อมูลการจองเข้าชมพิพิธภัณฑ์')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/form_bookings.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        {{-- Dependencies Thailand location --}}
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script type="text/javascript"
            src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/JQL.min.js"></script>
        <script type="text/javascript"
            src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/typeahead.bundle.js"></script>
        {{-- jquery.Thailand.js --}}
        <link rel="stylesheet"
            href="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.css">
        <script type="text/javascript"
            src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js"></script>
    </head>

    <div class="container mt-4 pb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mt-5">
                {{ session('error') }}
            </div>
        @endif
        @if (session('showSuccessModal'))
            <script>
                window.addEventListener('DOMContentLoaded', function() {
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                });
            </script>
        @endif
        <h2 class="text-center py-3 pt-5"
            style="color: #C06628; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); ">
            แก้ไขข้อมูลการจองเข้าชมพิพิธภัณฑ์</h2>
            <div class="card shadow p-4">
                <form method="POST" action="{{ route('bookings.update', $booking->booking_id) }}" class="row g-3">
                    @csrf
                    @method('PUT')
                    <div class="col-md-5">
                        <label for="activity_select" class="form-label">ประเภทเข้าชม</label>
                        <input type="hidden" id="fk_activity_id" name="fk_activity_id" value="{{ $booking->activity_id }}">
                        <select class="form-select" id="activity_select" disabled>
                            <option value="{{ $booking->activity_id }}">{{ $booking->activity->activity_name }}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="booking_date" class="form-label">วันที่จอง</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="booking_date" name="booking_date"
                                value="{{ old('booking_date', $booking->booking_date ?? '') }}" required
                                placeholder="กรุณาเลือกวันที่ต้องการจอง (วัน/เดือน/ปี)">
                            <div class="input-group-append">
                                <button type="button" class="input-group-text"
                                    onclick="document.getElementById('booking_date').focus();">
                                    <i class="fas fa-calendar-alt" style="font-size: 1.5rem;"></i>
                                </button>
                            </div>
                        </div>
                        @error('booking_date')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                        <p>*หมายเหตุ กรุณาเลือกวันที่ต้องการจองล่วงหน้า 3 วัน</p>
                    </div>

                    @if ($subactivities->isNotEmpty())
                        <div class="form-group col-md-3">
                            <label class="form-label">หลักสูตร</label>
                            <div class="dropdown">
                                <button class="btn btn-white border dropdown-toggle" type="button" id="subactivityDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    เลือกหลักสูตร
                                </button>
                                <ul class="dropdown-menu p-2" aria-labelledby="subactivityDropdown">
                                    @foreach ($subactivities as $subactivity)
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sub_activity_id[]"
                                                    value="{{ $subactivity->sub_activity_id }}"
                                                    id="sub_activity_{{ $subactivity->sub_activity_id }}"
                                                    {{ in_array($subactivity->sub_activity_id, $booking->subactivities->pluck('sub_activity_id')->toArray()) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="sub_activity_{{ $subactivity->sub_activity_id }}">
                                                    {{ $subactivity->sub_activity_name }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if ($timeslots->isNotEmpty())
                        <div class="form-group col-md-3">
                            <label for="fk_timeslots_id" class="form-label">รอบการเข้าชม</label>
                            <select id="fk_timeslots_id" class="form-select @error('fk_timeslots_id') is-invalid @enderror"
                                name="fk_timeslots_id"
                                data-selected="{{ old('fk_timeslots_id', $booking->timeslots_id ?? '') }}">
                                <option value="">เลือกรอบการเข้าชม</option>
                            </select>
                            @error('fk_timeslots_id')
                                <div class="my-2">
                                    <span class="text-danger">{{ $errors->first('fk_timeslots_id') }}</span>
                                </div>
                            @enderror
                        </div>
                        @else
                            <div class="w-100"></div>
                    @endif

                    <div class="col-md-2">
                        <label for="instituteName" class="form-label">ชื่อหน่วยงาน</label>
                        <input type="text" class="form-control @error('instituteName') is-invalid @enderror"
                            id="instituteName" name="instituteName" placeholder="กรอกชื่อหน่วยงาน"
                            value="{{ old('instituteName', $institutes->instituteName ?? $booking->instituteName) }}"
                            required>
                        @error('instituteName')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="instituteAddress" class="form-label">ที่อยู่หน่วยงาน</label>
                        <input type="text" class="form-control @error('instituteAddress') is-invalid @enderror"
                            id="instituteAddress" name="instituteAddress" placeholder="บ้านเลขที่, ซอย, หมู่, ถนน"
                            value="{{ old('instituteAddress', $institutes->instituteAddress ?? $booking->instituteAddress) }}">
                        @error('instituteAddress')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="subdistrict" class="form-label">แขวน/ตำบล</label>
                        <input type="text" class="form-control @error('subdistrict') is-invalid @enderror"
                            id="subdistrict" name="subdistrict" placeholder="กรอกแขวน/ตำบล"
                            value="{{ old('subdistrict', $institutes->subdistrict ?? $booking->subdistrict) }}" required>
                        @error('subdistrict')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="district" class="form-label">เขต/อำเภอ</label>
                        <input type="text" class="form-control @error('district') is-invalid @enderror" id="district"
                            name="district" placeholder="กรอกเขต/อำเภอ"
                            value="{{ old('district', $institutes->district ?? $booking->district) }}" required>
                        @error('district')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="province" class="form-label">จังหวัด</label>
                        <input type="text" class="form-control @error('province') is-invalid @enderror" id="province"
                            name="province" placeholder="กรอกจังหวัด"
                            value="{{ old('province', $institutes->province ?? $booking->province) }}" required>
                        @error('inputProvince')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="zipcode" class="form-label">รหัสไปรษณีย์</label>
                        <input type="text" class="form-control @error('zipcode') is-invalid @enderror" id="zipcode"
                            name="zipcode" placeholder="กรอกรหัสไปรษณีย์"
                            value="{{ old('zipcode', $institutes->zipcode ?? $booking->zipcode) }}" pattern="\d{5}"
                            maxlength="5" minlength="5" inputmode="numeric" required>
                        @error('zipcode')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="visitorName" class="form-label">ชื่อผู้ประสานงาน</label>
                        <input type="text" class="form-control @error('visitorName') is-invalid @enderror"
                            id="visitorName" name="visitorName" placeholder="ชื่อ-นามสกุล"
                            value="{{ old('visitorName', $visitors->visitorName ?? $booking->visitorName) }}">
                        @error('visitorName')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="visitorEmail" class="form-label">อีเมล์ผู้ประสานงาน</label>
                        <input type="email" class="form-control @error('visitorEmail') is-invalid @enderror"
                            id="visitorEmail" name="visitorEmail"
                            value="{{ old('visitorEmail', $visitors->visitorEmail ?? $booking->visitorEmail) }}" readonly>
                        @error('visitorEmail')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="tel" class="form-label">เบอร์โทรผู้ประสานงาน</label>
                        <input type="text" class="form-control @error('tel') is-invalid @enderror" id="tel"
                            name="tel" placeholder="หมายเลขโทรศัพท์"
                            value="{{ old('tel', $visitors->tel ?? $booking->tel) }}" pattern="\d{10}" maxlength="10"
                            minlength="10" inputmode="numeric" title="กรุณากรอกเบอร์โทร 10 หลัก" required
                            autocomplete="tel">
                        @error('tel')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="note" class="form-label">หมายเหตุ</label>
                        <input type="text" class="form-control @error('note') is-invalid @enderror" 
                            id="note" name="note" placeholder="กรอกหมายเหตุ (ถ้ามี)"
                            value="{{ old('note', $booking->note ?? $booking->note) }}" pattern="\d{10}" maxlength="10"
                            minlength="10" title="ระบุหมายเหตุ (ถ้ามี)" required autocomplete="note">
                        @error('note')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <p>ระบุจำนวนผู้เข้าชม</p>
                    <p class="custom-gray-text mt-0">
                        @if ($booking->activity->max_capacity)
                            <span class="indent-line">จำกัดจำนวนผู้เข้าชมไม่เกิน {{ $booking->activity->max_capacity }} คน
                                และ
                                ไม่ต่ำกว่า 50 คนต่อการจอง</span>
                            <span class="new-line">(หากผู้เข้าชมเกิน {{ $booking->activity->max_capacity }} คน
                                กรุณาติดต่อเจ้าหน้าที่ 0XX-XXXX )</span>
                        @else
                            <span>ไม่จำกัดจำนวนผู้เข้าชม และ ไม่ต่ำกว่า 50 คนต่อการจอง</span>
                        @endif
                    </p>
                    <p id="errorMessage" style="color: red; display: none;"></p>

                    <div class="row">
                        <!-- เด็กโต -->
                        <div class="col-md-3">
                            <input class="form-check-input" type="checkbox" id="children_qty" name="children_qty"
                                onclick="toggleInput('childrenInput')" {{ $booking->children_qty > 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="children_qty">เด็ก ( 3 ขวบ - ประถม ) :
                                {{ $booking->activity->children_price }} บาท/คน</label>
                            <input type="number" class="form-control mt-2" id="childrenInput" name="children_qty"
                                min="0" value="{{ $booking->children_qty > 0 ? $booking->children_qty : '' }}"
                                {{ $booking->children_qty > 0 ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>

                        <!-- นักเรียน/นักศึกษา -->
                        <div class="col-md-3">
                            <input class="form-check-input" type="checkbox" id="students_qty" name="students_qty"
                                onclick="toggleInput('studentInput')" {{ $booking->students_qty > 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="students_qty">นักเรียนมัธยม/นักศึกษา :
                                {{ $booking->activity->student_price }} บาท/คน</label>
                            <input type="number" class="form-control mt-2" id="studentInput" name="students_qty"
                                min="0" value="{{ $booking->students_qty > 0 ? $booking->students_qty : '' }}"
                                {{ $booking->students_qty > 0 ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>

                        <!-- ครู / อาจารย์  -->
                        <div class="col-md-3">
                            <input class="form-check-input" type="checkbox" id="adults_qty" name="adults_qty"
                                onclick="toggleInput('adultsInput')" {{ $booking->adults_qty > 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="adults_qty">ผู้ใหญ่ / คุณครู :
                                {{ $booking->activity->adult_price }} บาท/คน</label>
                            <input type="number" class="form-control mt-2" id="adultsInput" name="adults_qty"
                                min="0" value="{{ $booking->adults_qty > 0 ? $booking->adults_qty : '' }}"
                                {{ $booking->adults_qty > 0 ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>
                    </div>
                    <p>สวัสดิการเข้าชมฟรี</p>
                    <div class="row">
                    <!-- เด็กเล็ก-->
                    <div class="col-md-3">
                        <input class="form-check-input" type="checkbox" id="kid_qty" name="kid_qty"
                            onclick="toggleInput('kidInput')">
                        <label class="form-check-label" for="kid_qty">
                            เด็กเล็ก ( ต่ำกว่า 2 ขวบ )</label>
                        <input type="number" class="form-control mt-2" id="kidInput" name="kid_qty"
                            min="0" disabled oninput="calculateTotal()">
                    </div>

                        <!-- ผู้พิการ -->
                        <div class="col-md-3">
                            <input class="form-check-input" type="checkbox" id="disabled_qty" name="disabled_qty"
                                onclick="toggleInput('disabledInput')" {{ $booking->disabled_qty > 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="disabled_qty">ผู้พิการ</label>
                            <input type="number" class="form-control mt-2" id="disabledInput" name="disabled_qty"
                                min="0" value="{{ $booking->disabled_qty > 0 ? $booking->disabled_qty : '' }}"
                                {{ $booking->disabled_qty > 0 ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>
                        <!-- ผู้สูงอายุ -->
                        <div class="col-md-3">
                            <input class="form-check-input" type="checkbox" id="elderly_qty" name="elderly_qty"
                                onclick="toggleInput('elderlyInput')" {{ $booking->elderly_qty > 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="elderly_qty">ผู้สูงอายุ</label>
                            <input type="number" class="form-control mt-2" id="elderlyInput" name="elderly_qty"
                                min="0" value="{{ $booking->elderly_qty > 0 ? $booking->elderly_qty : '' }}"
                                {{ $booking->elderly_qty > 0 ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>
                        <!-- พระภิกษุสงฆ์ /สามเณร -->
                        <div class="col-md-3">
                            <input class="form-check-input" type="checkbox" id="monk_qty" name="monk_qty"
                                onclick="toggleInput('monkInput')" {{ $booking->monk_qty > 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="monk_qty">ผู้สูงอายุ</label>
                            <input type="number" class="form-control mt-2" id="monkInput" name="monk_qty"
                                min="0" value="{{ $booking->monk_qty > 0 ? $booking->monk_qty : '' }}"
                                {{ $booking->monk_qty > 0 ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>

                        <!-- จำนวนผู้เข้าร่วมกิจกรรม -->
                        <div class="col-12 mt-4">
                            <h5>จำนวนผู้เข้าร่วมกิจกรรม: <span id="totalVisitors">0</span> คน</h5>
                            <h5>ราคารวม: <span id="totalPrice">0.00</span> บาท</h5>
                        </div>

                        <input type="hidden" id="children_price" name="children_price"
                            value="{{ $booking->activity->children_price }}">
                        <input type="hidden" id="student_price" name="student_price"
                            value="{{ $booking->activity->student_price }}">
                        <input type="hidden" id="adult_price" name="adult_price"
                            value="{{ $booking->activity->adult_price }}">
                        <input type="hidden" id="kid_price" name="kid_price"
                            value="{{ $booking->activity->kid_price }}">
                        <input type="hidden" id="disabled_price" name="disabled_price"
                            value="{{ $booking->activity->disabled_price }}">
                        <input type="hidden" id="elderly_price" name="elderly_price"
                            value="{{ $booking->activity->elderly_price }}">
                        <input type="hidden" id="monk_price" name="monk_price"
                            value="{{ $booking->activity->monk_price }}">

                        <div class="col-12 d-flex justify-content-center pt-2">
                            <button type="button" class="btn btn-secondary btn-lg ms-2" onclick="goBack()">
                                ย้อนกลับ
                            </button>                          
                            <button type="button" class="btn btn-primary btn-lg ms-2" onclick="confirmSubmission()">
                                ยืนยันข้อมูล
                            </button>
                        </div>
                </form>
                <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="successModalLabel">แก้ไขข้อมูลสำเร็จ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                แก้ไขข้อมูลการจองของผู้เข้าชมสำเร็จ
                            </div>
                            <div class="modal-footer">
                                <a href="/checkBookingStatus" class="btn btn-primary">ตรวจสอบสถานะการจอง</a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <script>
        window.subactivities = @json($subactivities);
        window.maxSubactivities = {{ $maxSubactivities }};
    </script>
    <script src="{{ asset('js/EditBooking.js') }}"></script>
    <script>
        function goBack() {
            var activityTypeId = {{ $booking->activity->activity_type_id }};
            if (activityTypeId === 1) {
                window.location = '{{ route('approved_bookings.general') }}';
            } else if (activityTypeId === 2) {
                window.location = '{{ route('approved_bookings.activity') }}';
            }
        }
    </script>
@endsection

</html>
