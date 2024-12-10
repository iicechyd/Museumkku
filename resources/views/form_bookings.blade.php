@extends('layouts.layout')
@section('title', 'กรอกข้อมูลเพื่อจองเข้าชมพิพิธภัณฑ์')
@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/form_bookings.css') }}">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/JQL.min.js"></script>
    <script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/typeahead.bundle.js"></script>
    <link rel="stylesheet" href="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.css">
    <script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js"></script>    
</head>

    <div class="container mt-5">
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
        <h2 class="text-center py-3">แบบฟอร์มจองเข้าชมพิพิธภัณฑ์</h2>
        <div class="card shadow p-4">
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
                    <label for="booking_date"  class="form-label">วันที่จอง:</label>
                    <input type="date" class="form-control" id="booking_date" name="booking_date"
                        value="{{ old('booking_date') }}" min="{{ date('Y-m-d', strtotime('+3 days')) }}" required>
                    @error('booking_date')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                    <p>*หมายเหตุ กรุณาเลือกวันที่ต้องการจองล่วงหน้า 3 วัน</p>
                </div>
                
                @if ($timeslots->isNotEmpty())
                    <div class="form-group col-3">
                        <label for="fk_timeslots_id" class="form-label">รอบการเข้าชม:</label>
                        <select id="fk_timeslots_id" class="form-select @error('fk_timeslots_id') is-invalid @enderror"
                            name="fk_timeslots_id">
                            <option value="">เลือกรอบการเข้าชม</option>
                            @foreach ($timeslots as $timeslot)
                                <option value="{{ $timeslot->timeslots_id }}" {{ old('fk_timeslots_id') == $timeslot->timeslots_id ? 'selected' : '' }}>
                                    {{ $timeslot->start_time }} - {{ $timeslot->end_time }}
                                </option>
                            @endforeach
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
                    <input type="text" class="form-control @error('subdistrict') is-invalid @enderror" id="subdistrict"
                    name="subdistrict" placeholder="กรอกแขวน/ตำบล" value="{{ old('subdistrict') }}" required>
                    @error('subdistrict')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="district" class="form-label">เขต/อำเภอ</label>
                    <input type="text" class="form-control @error('district') is-invalid @enderror" id="district"
                    name="district" placeholder="กรอกเขต/อำเภอ" value="{{ old('district') }}" required>
                    @error('district')
                        <div class="my-2">
                            <span class="text-danger">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="province" class="form-label">จังหวัด</label>
                    <input type="text" class="form-control @error('province') is-invalid @enderror" id="province"
                    name="province" placeholder="กรอกจังหวัด" value="{{ old('province') }}" required>
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
                <!-- เนอสเซอรี่ - อนุบาล -->
                <div class="col-3">
                    <input class="form-check-input" type="checkbox" id="children_qty" name="children_qty"
                        onclick="toggleInput('childrenInput')">
                    <label class="form-check-label" for="children_qty">เด็ก : {{ $selectedActivity->children_price }} บาท/คน</label>
                    <input type="number" class="form-control mt-2" id="childrenInput" name="children_qty"
                        min="0" disabled oninput="calculateTotal()">
                </div>

                <!-- นักเรียน/นักศึกษา -->
                <div class="col-3">
                    <input class="form-check-input" type="checkbox" id="students_qty" name="students_qty"
                        onclick="toggleInput('studentInput')">
                    <label class="form-check-label" for="students_qty">นักเรียน/นักศึกษา : {{ $selectedActivity->student_price }} บาท/คน</label>
                    <input type="number" class="form-control mt-2" id="studentInput" name="students_qty"
                        min="0" disabled oninput="calculateTotal()">
                </div>

                <!-- ครู / อาจารย์ -->
                <div class="col-3">
                    <input class="form-check-input" type="checkbox" id="adults_qty" name="adults_qty"
                        onclick="toggleInput('adultsInput')">
                    <label class="form-check-label" for="adults_qty">ครู/อาจารย์ : {{ $selectedActivity->adult_price }} บาท/คน</label>
                    <input type="number" class="form-control mt-2" id="adultsInput" name="adults_qty" min="0"
                        disabled oninput="calculateTotal()">
                </div>

                <!-- ผู้พิการ -->
                <div class="col-3">
                    <input class="form-check-input" type="checkbox" id="disabled_qty" name="disabled_qty"
                        onclick="toggleInput('disabledInput')">
                    <label class="form-check-label" for="disabled_qty">ผู้พิการ : ฟรี</label>
                    <input type="number" class="form-control mt-2" id="disabledInput" name="disabled_qty" min="0"
                        disabled oninput="calculateTotal()">
                </div>

                 <!-- ผู้สูงอายุ -->
                 <div class="col-3">
                    <input class="form-check-input" type="checkbox" id="elderly_qty" name="elderly_qty"
                        onclick="toggleInput('elderlyInput')">
                    <label class="form-check-label" for="elderly_qty">ผู้สูงอายุ : ฟรี</label>
                    <input type="number" class="form-control mt-2" id="elderlyInput" name="elderly_qty" min="0"
                        disabled oninput="calculateTotal()">
                </div>

                <!-- พระภิกษุสงฆ์ /สามเณร -->
                <div class="col-3">
                    <input class="form-check-input" type="checkbox" id="monk_qty" name="monk_qty"
                        onclick="toggleInput('monkInput')">
                    <label class="form-check-label" for="monk_qty">พระภิกษุสงฆ์ /สามเณร : ฟรี</label>
                    <input type="number" class="form-control mt-2" id="monkInput" name="monk_qty" min="0"
                        disabled oninput="calculateTotal()">
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
                <input type="hidden" id="disabled_price" name="disabled_price" 
                    value="{{ $selectedActivity->disabled_price }}">
                <input type="hidden" id="elderly_price" name="elderly_price" 
                    value="{{ $selectedActivity->elderly_price }}">
                <input type="hidden" id="monk_price" name="monk_price" 
                    value="{{ $selectedActivity->monk_price }}">

                <div class="col-12 d-flex justify-content-center py-4">
                    <button type="submit" class="btn btn-primary btn-lg ms-2">
                        ยืนยันข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/form_bookings.js') }}"></script>


@endsection
