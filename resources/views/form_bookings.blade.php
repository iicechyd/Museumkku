@extends('layouts.layout')
@section('title', 'กรอกข้อมูลเพื่อจองเข้าชมพิพิธภัณฑ์')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/form_bookings.css') }}">
        <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        {{-- Dependencies Thailand location  --}}
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/JQL.min.js"></script>
        <script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/typeahead.bundle.js"></script>
        {{-- jquery.Thailand.js --}}
        <link rel="stylesheet" href="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.css">
        <script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js"></script>
        
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;700&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/th.global.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </head>

    <div class="container mt-4 pb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
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

        <h2 class="text-center py-3"
            style="color: #C06628; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); ">
            แบบฟอร์มจองเข้าชมพิพิธภัณฑ์</h2>
        <div class="card shadow p-4">
            <div class="pb-3">
                <button id="toggleButton" type="button" class="toggle-btn" onclick="toggleCalendar()">
                    <span id="toggleText">แสดงปฏิทินการจอง</span>
                    <span id="arrowIcon" class="arrow">&#9660;</span>
                </button>
                <div id="calendar" class="calendar hidden">
                </div>
            </div>

            <form method="POST" action="/InsertBooking" class="row g-3" novalidate>
                @csrf
                <div class="col-md-5">
                    <label for="fk_activity_id" class="form-label">ประเภทเข้าชม</label>
                    <input type="hidden" id="fk_activity_id" name="fk_activity_id" value="{{ $activity_id }}">
                    <select class="form-select" disabled>
                        <option value="{{ $activity_id }}">{{ $selectedActivity->activity_name }}</option>
                    </select>
                </div>

                <div class="form-group col-4">
                    <label for="booking_date" class="form-label">วันที่จอง</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="booking_date" name="booking_date"
                            value="{{ old('booking_date', $booking_date ?? '') }}"
                            min="{{ date('Y-m-d', strtotime('+3 days')) }}" required
                            placeholder="กรุณาเลือกวันที่ต้องการจอง (วัน/เดือน/ปี)">
                        <div class="input-group-append">
                            <label for="booking_date" class="input-group-text" style="cursor: pointer;">
                                <i class="fas fa-calendar-alt" style="font-size: 1.5rem;"></i>
                            </label>
                        </div>
                    </div>
                    @error('booking_date')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                    <p>*หมายเหตุ กรุณาเลือกวันที่ต้องการจองล่วงหน้า 3 วัน</p>
                </div>

                @if ($hasSubactivities)
                    <div class="form-group col-3">
                        <label for="fk_subactivity_id" class="form-label">หลักสูตร</label>
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

                @if ($timeslots->isNotEmpty())
                    <div class="form-group col-3">
                        <label for="fk_timeslots_id" class="form-label">รอบการเข้าชม:</label>
                        <select id="fk_timeslots_id" class="form-select @error('fk_timeslots_id') is-invalid @enderror"
                            name="fk_timeslots_id">
                            <option value="">เลือกรอบการเข้าชม</option>
                        </select>
                        @error('fk_timeslots_id')
                            <div class="my-2">
                                <span class="text-danger">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>
                @endif

                <div class="col-4">
                    <label for="instituteName" class="form-label">ชื่อหน่วยงาน</label>
                    <input type="text" class="form-control @error('instituteName') is-invalid @enderror"
                        id="instituteName" name="instituteName" placeholder="กรอกชื่อหน่วยงาน"
                        value="{{ old('instituteName') }}" required>
                    @error('instituteName')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="col-4">
                    <label for="instituteAddress" class="form-label">ที่อยู่หน่วยงาน</label>
                    <input type="text" class="form-control @error('instituteAddress') is-invalid @enderror"
                        id="instituteAddress" name="instituteAddress" placeholder="บ้านเลขที่, ซอย, หมู่, ถนน"
                        value="{{ old('instituteAddress') }}">
                    @error('instituteAddress')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="zipcode" class="form-label">รหัสไปรษณีย์</label>
                    <input type="text" class="form-control @error('zipcode') is-invalid @enderror" id="zipcode"
                        name="zipcode" placeholder="กรอกรหัสไปรษณีย์" value="{{ old('zipcode') }}" pattern="\d{5}"
                        maxlength="5" minlength="5" inputmode="numeric" required>
                    @error('zipcode')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="subdistrict" class="form-label">แขวน/ตำบล</label>
                    <input type="text" class="form-control @error('subdistrict') is-invalid @enderror"
                        id="subdistrict" name="subdistrict"
                        value="{{ old('subdistrict') }}" disabled required>
                    @error('subdistrict')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="district" class="form-label">เขต/อำเภอ</label>
                    <input type="text" class="form-control @error('district') is-invalid @enderror" id="district"
                        name="district" value="{{ old('district') }}" disabled required>
                    @error('district')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="province" class="form-label">จังหวัด</label>
                    <input type="text" class="form-control @error('province') is-invalid @enderror" id="province"
                        name="province" value="{{ old('province') }}" disabled required>
                    @error('inputProvince')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="visitorName" class="form-label">ชื่อผู้ประสานงาน</label>
                    <input type="text" class="form-control @error('visitorName') is-invalid @enderror"
                        id="visitorName" name="visitorName" placeholder="ชื่อ-นามสกุล"
                        value="{{ old('visitorName') }}">
                    @error('visitorName')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="visitorEmail" class="form-label">อีเมล์ผู้ประสานงาน</label>
                    <input type="email" class="form-control @error('visitorEmail') is-invalid @enderror"
                        id="visitorEmail" name="visitorEmail" placeholder="Test@email.com"
                        value="{{ old('visitorEmail') }}">
                    @error('visitorEmail')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="tel" class="form-label">เบอร์โทรผู้ประสานงาน</label>
                    <input type="text" class="form-control @error('tel') is-invalid @enderror" id="tel"
                        name="tel" placeholder="หมายเลขโทรศัพท์" value="{{ old('tel') }}" pattern="\d{10}"
                        maxlength="10" minlength="10" inputmode="numeric" title="กรุณากรอกเบอร์โทร 10 หลัก" required>
                    @error('tel')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <label for="text-center p-5">ระบุจำนวนผู้เข้าชม</label>
                <div id="errorMessage" style="color: red; display: none;"></div>
                <label for="text-center" class="custom-gray-text">
                    @if ($selectedActivity->max_capacity)
                        <span class="indent-line">จำกัดจำนวนผู้เข้าชมไม่เกิน {{ $selectedActivity->max_capacity }} คน และ
                            ไม่ต่ำกว่า 50 คนต่อการจอง</span>
                        <span class="new-line">(หากผู้เข้าชมเกิน {{ $selectedActivity->max_capacity }} คน
                            กรุณาติดต่อเจ้าหน้าที่ 0XX-XXXX )</span>
                    @else
                        <span>ไม่จำกัดจำนวนผู้เข้าชม และ ไม่ต่ำกว่า 50 คนต่อการจอง</span>
                    @endif
                </label>
                <div class="row pt-3">
                    <!-- เนอสเซอรี่ - อนุบาล -->
                    <div class="col-3">
                        <input class="form-check-input" type="checkbox" id="children_qty" name="children_qty"
                            onclick="toggleInput('childrenInput')">
                        <label class="form-check-label" for="children_qty">เด็ก : {{ $selectedActivity->children_price }}
                            บาท/คน</label>
                        <input type="number" class="form-control mt-2" id="childrenInput" name="children_qty"
                            min="0" disabled oninput="calculateTotal()">
                    </div>

                    <!-- นักเรียน/นักศึกษา -->
                    <div class="col-3">
                        <input class="form-check-input" type="checkbox" id="students_qty" name="students_qty"
                            onclick="toggleInput('studentInput')">
                        <label class="form-check-label" for="students_qty">นักเรียน/นักศึกษา :
                            {{ $selectedActivity->student_price }} บาท/คน</label>
                        <input type="number" class="form-control mt-2" id="studentInput" name="students_qty"
                            min="0" disabled oninput="calculateTotal()">
                    </div>

                    <!-- ครู / อาจารย์ -->
                    <div class="col-3">
                        <input class="form-check-input" type="checkbox" id="adults_qty" name="adults_qty"
                            onclick="toggleInput('adultsInput')">
                        <label class="form-check-label" for="adults_qty">ผู้ใหญ่ / คุณครู :
                            {{ $selectedActivity->adult_price }} บาท/คน</label>
                        <input type="number" class="form-control mt-2" id="adultsInput" name="adults_qty"
                            min="0" disabled oninput="calculateTotal()">
                    </div>
                </div>

                <label for="text-center">สวัสดิการเข้าชมฟรี</label>
                <div class="row pt-3">
                    <!-- ผู้พิการ -->
                    <div class="col-3">
                        <input class="form-check-input" type="checkbox" id="disabled_qty" name="disabled_qty"
                            onclick="toggleInput('disabledInput')">
                        <label class="form-check-label" for="disabled_qty">
                            ผู้พิการ</label>
                        <input type="number" class="form-control mt-2" id="disabledInput" name="disabled_qty"
                            min="0" disabled oninput="calculateTotal()">
                    </div>

                    <!-- ผู้สูงอายุ -->
                    <div class="col-3">
                        <input class="form-check-input" type="checkbox" id="elderly_qty" name="elderly_qty"
                            onclick="toggleInput('elderlyInput')">
                        <label class="form-check-label" for="elderly_qty">
                            ผู้สูงอายุ</label>
                        <input type="number" class="form-control mt-2" id="elderlyInput" name="elderly_qty"
                            min="0" disabled oninput="calculateTotal()">
                    </div>

                    <!-- พระภิกษุสงฆ์ /สามเณร -->
                    <div class="col-3">
                        <input class="form-check-input" type="checkbox" id="monk_qty" name="monk_qty"
                            onclick="toggleInput('monkInput')">
                        <label class="form-check-label" for="monk_qty">
                            พระภิกษุสงฆ์ /สามเณร</label>
                        <input type="number" class="form-control mt-2" id="monkInput" name="monk_qty" min="0"
                            disabled oninput="calculateTotal()">
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
                <input type="hidden" id="adult_price" name="adult_price" value="{{ $selectedActivity->adult_price }}">
                <input type="hidden" id="disabled_price" name="disabled_price"
                    value="{{ $selectedActivity->disabled_price }}">
                <input type="hidden" id="elderly_price" name="elderly_price"
                    value="{{ $selectedActivity->elderly_price }}">
                <input type="hidden" id="monk_price" name="monk_price" value="{{ $selectedActivity->monk_price }}">

                <div class="col-12 d-flex justify-content-center pt-2">
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
                            <h5 class="modal-title" id="successModalLabel">ส่งข้อมูลสำเร็จ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            กรุณารอการติดต่อกลับจากเจ้าหน้าที่ทางอีเมล ภายใน 24 ชั่วโมง<br> หากมีข้อสงสัย กรุณาติดต่อ 096-XXX-XXXX เจ้าหน้าที่ฝ่ายกิจกรรม
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
    <!-- Modal Calendar -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">รายละเอียดการจอง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4 id="eventTitle"></h4>
                    <p><strong id="eventTimeslotLabel"></strong>
                    <p id="eventTimeslot"></p>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        window.subactivities = @json($subactivities);
        window.maxSubactivities = {{ $maxSubactivities }};
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/form_bookings.js') }}"></script>
@endsection
