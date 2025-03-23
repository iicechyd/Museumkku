@extends('layouts.layout')
@section('title', 'แบบฟอร์มเข้าชมวอคอิน')
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
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;700&display=swap" rel="stylesheet">
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
        @if (session('warning'))
            <div class="alert alert-warning mt-5">
                {{ session('warning') }}
            </div>
        @endif
        @if (session('showSuccessModal'))
            <script>
                window.addEventListener('DOMContentLoaded', function() {
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    setTimeout(function() {
                        window.location.href = "{{ route('today_bookings.general') }}";
                    }, 3000);
                });
            </script>
        @endif
        @if (session('verification_email'))
            <h2 class="text-center py-3"
                style="color: #C06628; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); ">
                แบบฟอร์มเข้าชมวอคอิน</h2>
            <div class="card shadow p-4">
                <div class="d-flex justify-content-end align-items-center mb-2">
                    <label for="booked_by" class="form-label mb-0 me-2">ดำเนินการจองโดย</label>
                    <input type="text" class="form-control" id="booked_by"
                        value="{{ Auth::check() ? Auth::user()->name : 'ผู้จองเข้าชม' }}" readonly 
                        style="width: auto; max-width: 180px;">
                    <input type="hidden" name="user_id" value="{{ Auth::check() ? Auth::user()->user_id : null }}">
                </div>
                
                <form method="POST" action="{{ route('WalkinBooking') }}" class="row g-3" novalidate>
                    @csrf
                    <div class="col-md-5">
                        <label for="activity_select" class="form-label">ประเภทเข้าชม</label>
                        <input type="hidden" id="fk_activity_id" name="fk_activity_id" value="{{ $activity_id }}">
                        <select class="form-select" id="activity_select" disabled>
                            <option value="{{ $activity_id }}">{{ $selectedActivity->activity_name }}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="booking_date" class="form-label">วันที่เข้าชม</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="booking_date" name="booking_date" readonly
                                required>
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
                    </div>

                    @if ($hasSubactivities)
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
                                                    id="sub_activity_{{ $subactivity->sub_activity_id }}">
                                                <label class="form-check-label"
                                                    for="sub_activity_{{ $subactivity->sub_activity_id }}">
                                                    {{ $subactivity->sub_activity_name }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <p>เลือกได้สูงสุด {{ $maxSubactivities }} หลักสูตร</p>
                        </div>
                    @endif

                    <div class="row pt-2">
                        <div class="col-md-2">
                            <label for="instituteName" class="form-label">ชื่อหน่วยงาน</label>
                            <input type="text" class="form-control @error('instituteName') is-invalid @enderror"
                                id="instituteName" name="instituteName" placeholder="กรอกชื่อหน่วยงาน"
                                value="{{ old('instituteName', $visitorData['instituteName']) }}" required>
                            @error('instituteName')
                                <div class="my-2">
                                    <span class="text-danger">{{ $errors->first('instituteName') }}</span>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="instituteAddress" class="form-label">ที่อยู่หน่วยงาน</label>
                            <input type="text" class="form-control @error('instituteAddress') is-invalid @enderror"
                                id="instituteAddress" name="instituteAddress" placeholder="บ้านเลขที่, ซอย, หมู่, ถนน"
                                value="{{ old('instituteAddress', $visitorData['instituteAddress']) }}">
                            @error('instituteAddress')
                                <div class="my-2">
                                    <span class="text-danger">{{ $errors->first('instituteAddress') }}</span>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="subdistrict" class="form-label">แขวน/ตำบล</label>
                            <input type="text" class="form-control @error('subdistrict') is-invalid @enderror"
                                id="subdistrict" name="subdistrict" placeholder="กรอกแขวน/ตำบล"
                                value="{{ old('subdistrict', $visitorData['subdistrict']) }}" required>
                            @error('subdistrict')
                                <div class="my-2">
                                    <span class="text-danger">{{ $errors->first('subdistrict') }}</span>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="district" class="form-label">เขต/อำเภอ</label>
                            <input type="text" class="form-control @error('district') is-invalid @enderror"
                                id="district" name="district" placeholder="กรอกเขต/อำเภอ"
                                value="{{ old('district', $visitorData['district']) }}" required>
                            @error('district')
                                <div class="my-2">
                                    <span class="text-danger">{{ $errors->first('district') }}</span>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="province" class="form-label">จังหวัด</label>
                            <input type="text" class="form-control @error('province') is-invalid @enderror"
                                id="province" name="province" placeholder="กรอกจังหวัด"
                                value="{{ old('province', $visitorData['province']) }}" required>
                            @error('inputProvince')
                                <div class="my-2">
                                    <span class="text-danger">{{ $errors->first('province') }}</span>
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="zipcode" class="form-label">รหัสไปรษณีย์</label>
                            <input type="text" class="form-control @error('zipcode') is-invalid @enderror"
                                id="zipcode" name="zipcode" placeholder="กรอกรหัสไปรษณีย์"
                                value="{{ old('zipcode', $visitorData['zipcode']) }}" pattern="\d{5}" maxlength="5"
                                minlength="5" inputmode="numeric" required>
                            @error('zipcode')
                                <div class="my-2">
                                    <span class="text-danger">{{ $errors->first('zipcode') }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="visitorName" class="form-label">ชื่อผู้ประสานงาน</label>
                        <input type="text" class="form-control @error('visitorName') is-invalid @enderror"
                            id="visitorName" name="visitorName" placeholder="ชื่อ-นามสกุล"
                            value="{{ old('visitorName', $visitorData['visitorName']) }}">
                        @error('visitorName')
                            <div class="my-2">
                                <span class="text-danger">{{ $errors->first('visitorName') }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="visitorEmail" class="form-label">อีเมล์ผู้ประสานงาน</label>
                        <input type="email" class="form-control @error('visitorEmail') is-invalid @enderror"
                            id="visitorEmail" name="visitorEmail" placeholder="Test@email.com"
                            value="{{ session('verification_email', old('visitorEmail')) }}" readonly>
                        @error('visitorEmail')
                            <div class="my-2">
                                <span class="text-danger">{{ $errors->first('visitorEmail') }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="tel" class="form-label">เบอร์โทรผู้ประสานงาน</label>
                        <input type="text" class="form-control @error('tel') is-invalid @enderror" id="tel"
                            name="tel" placeholder="หมายเลขโทรศัพท์" value="{{ old('tel', $visitorData['tel']) }}"
                            pattern="\d{10}" maxlength="10" minlength="10" inputmode="numeric"
                            title="กรุณากรอกเบอร์โทร 10 หลัก" required autocomplete="tel">
                        @error('tel')
                            <div class="my-2">
                                <span class="text-danger">{{ $errors->first('tel') }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="note" class="form-label">หมายเหตุ</label>
                        <input type="text" class="form-control @error('note') is-invalid @enderror" id="note"
                            name="note" value="วอคอิน" readonly required>
                        @error('note')
                            <div class="my-2">
                                <span class="text-danger">{{ $errors->first('note') }}</span>
                            </div>
                        @enderror
                    </div>

                    <p>ระบุจำนวนผู้เข้าชม</p>
                    <p id="errorMessage" style="color: red; display: none;"></p>
                    <div class="row">
                        <!-- เด็กโต -->
                        <div class="col-md-3 custom-col">
                            <input class="form-check-input" type="checkbox" id="children_qty" name="children_qty"
                                onclick="toggleInput('childrenInput')" {{ old('children_qty') ? 'checked' : '' }}>
                            <label class="form-check-label" for="children_qty">เด็ก ( 3 ขวบ - ประถม ) :
                                {{ $selectedActivity->children_price }} บาท/คน</label>
                            <input type="number" class="form-control mt-2" id="childrenInput" name="children_qty"
                                min="0" value="{{ old('children_qty') }}" 
                                {{ old('children_qty') ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>

                        <!-- มัธยม/นักศึกษา -->
                        <div class="col-md-3 custom-col">
                            <input class="form-check-input" type="checkbox" id="students_qty" name="students_qty"
                                onclick="toggleInput('studentInput')" {{ old('students_qty') ? 'checked' : '' }}>
                            <label class="form-check-label" for="students_qty">มัธยม/นักศึกษา :
                                {{ $selectedActivity->student_price }} บาท/คน</label>
                            <input type="number" class="form-control mt-2" id="studentInput" name="students_qty"
                                min="0" value="{{ old('students_qty') }}" 
                                {{ old('students_qty') ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>

                        <!-- ครู / อาจารย์ -->
                        <div class="col-md-3 custom-col">
                            <input class="form-check-input" type="checkbox" id="adults_qty" name="adults_qty"
                                onclick="toggleInput('adultsInput')" {{ old('adults_qty') ? 'checked' : '' }}>
                            <label class="form-check-label" for="adults_qty">ผู้ใหญ่ / คุณครู :
                                {{ $selectedActivity->adult_price }} บาท/คน</label>
                            <input type="number" class="form-control mt-2" id="adultsInput" name="adults_qty"
                                min="0" value="{{ old('adults_qty') }}" 
                                {{ old('adults_qty') ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>
                    </div>

                    <p>สวัสดิการเข้าชมฟรี</p>
                    <div class="row">
                        <!-- เด็กเล็ก-->
                        <div class="col-md-3 custom-col">
                            <input class="form-check-input" type="checkbox" id="kid_qty" name="kid_qty"
                                onclick="toggleInput('kidInput')" {{ old('kid_qty') ? 'checked' : '' }}>
                            <label class="form-check-label" for="kid_qty">
                                เด็กเล็ก ( ต่ำกว่า 2 ขวบ )</label>
                            <input type="number" class="form-control mt-2" id="kidInput" name="kid_qty"
                                min="0" value="{{ old('kid_qty') }}" 
                                {{ old('kid_qty') ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>

                        <!-- ผู้พิการ -->
                        <div class="col-md-3 custom-col">
                            <input class="form-check-input" type="checkbox" id="disabled_qty" name="disabled_qty"
                                onclick="toggleInput('disabledInput')" {{ old('disabled_qty') ? 'checked' : '' }}>
                            <label class="form-check-label" for="disabled_qty">
                                ผู้พิการ</label>
                            <input type="number" class="form-control mt-2" id="disabledInput" name="disabled_qty"
                                min="0" value="{{ old('disabled_qty') }}" 
                                {{ old('disabled_qty') ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>

                        <!-- ผู้สูงอายุ -->
                        <div class="col-md-3 custom-col">
                            <input class="form-check-input" type="checkbox" id="elderly_qty" name="elderly_qty"
                                onclick="toggleInput('elderlyInput')" {{ old('elderly_qty') ? 'checked' : '' }}>
                            <label class="form-check-label" for="elderly_qty">
                                ผู้สูงอายุ</label>
                            <input type="number" class="form-control mt-2" id="elderlyInput" name="elderly_qty"
                                min="0" value="{{ old('elderly_qty') }}" 
                                {{ old('elderly_qty') ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>

                        <!-- พระภิกษุสงฆ์ /สามเณร -->
                        <div class="col-md-3 custom-col">
                            <input class="form-check-input" type="checkbox" id="monk_qty" name="monk_qty"
                                onclick="toggleInput('monkInput')" {{ old('monk_qty') ? 'checked' : '' }}>
                            <label class="form-check-label" for="monk_qty">
                                พระภิกษุสงฆ์ /สามเณร</label>
                            <input type="number" class="form-control mt-2" id="monkInput" name="monk_qty"
                                min="0" value="{{ old('monk_qty') }}" 
                                {{ old('monk_qty') ? '' : 'disabled' }} oninput="calculateTotal()">
                        </div>
                    </div>
                    <!-- จำนวนผู้เข้าร่วมกิจกรรม -->
                    <div class="col-12 mt-4">
                        <h5>จำนวนผู้เข้าร่วมกิจกรรม: <span id="totalVisitors">0</span> คน</h5>
                        <h5>ราคารวม: <span id="totalPrice">0.00</span> บาท</h5>
                    </div>

                    <input type="hidden" id="children_price" name="children_price"
                        value="{{ $selectedActivity->children_price }}">
                    <input type="hidden" id="student_price" name="student_price"
                        value="{{ $selectedActivity->student_price }}">
                    <input type="hidden" id="adult_price" name="adult_price"
                        value="{{ $selectedActivity->adult_price }}">
                    <input type="hidden" id="kid_price" name="kid_price" value="{{ $selectedActivity->kid_price }}">
                    <input type="hidden" id="disabled_price" name="disabled_price"
                        value="{{ $selectedActivity->disabled_price }}">
                    <input type="hidden" id="elderly_price" name="elderly_price"
                        value="{{ $selectedActivity->elderly_price }}">
                    <input type="hidden" id="monk_price" name="monk_price"
                        value="{{ $selectedActivity->monk_price }}">

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="privacyPolicy" name="privacy_policy"
                            required>
                        <label class="form-check-label" for="privacyPolicy">
                            ฉันได้อ่านและยอมรับข้อตกลงการใช้งาน
                            <a href="#" data-bs-toggle="modal"
                                data-bs-target="#privacyModal">นโยบายความเป็นส่วนตัว</a>
                        </label>
                        <p id="privacyAlert" style="color: red; display: none;"></p>
                    </div>

                    <div class="col-12 d-flex justify-content-center pt-2">
                        <button type="button" class="btn btn-primary btn-lg ms-2" onclick="confirmSubmission()">
                            ยืนยันข้อมูล
                        </button>
                    </div>
                </form>
                <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="successModalLabel">บันทึกแบบฟอร์มเข้าชมวอคอินสำเร็จ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <br>
                                <p>เรากำลังนำคุณกลับไปยังหน้าการจองวันนี้ กรุณารอขณะนี้...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <x-privacy-modal />
    <script>
        window.subactivities = @json($subactivities);
        window.maxSubactivities = @json($maxSubactivities);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="{{ asset('js/admin_bookings.js') }}"></script>
@endsection
