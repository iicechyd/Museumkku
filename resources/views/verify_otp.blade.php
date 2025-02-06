@extends('layouts.layout')
@section('title', 'ยืนยันรหัส OTP')

@section('content')
    <div class="container py-5">
        <div class="card border-light-subtle shadow-sm">
            <div class="row justify-content-center">
                <div class="col-10 col-md-6" style="background-color: #ECECEC;">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="col-10 col-xl-8 py-3">
                            <img class="img-fluid rounded mb-4" src="/img/logo_mlc_sim1.png" width="500" height="80">
                        </div>
                    </div>
                </div>
                <div class="col-10 col-md-6 d-flex justify-content-center align-items-center p-4">
                    <div class="card-body p-3 p-md-4 p-xl-5">
                        <div class="row justify-content-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="col-10 justify-content-center">
                                    <div class="mb-5 text-center">
                                        <h2 class="h3">ยืนยัน OTP</h2>
                                        <h3 class="fs-6 fw-normal text-secondary m-0">กรุณากรอกรหัส OTP
                                            ที่ได้รับทางอีเมลเพื่อดำเนินการต่อ
                                        </h3>
                                        <form method="POST" action="{{ route('verifyOtp.submit') }}">
                                            @csrf
                                            <div class="form-group pt-4">
                                                <label for="otp" class="mb-2">รหัส OTP</label>
                                                <div class="d-flex justify-content-center">
                                                    <input type="text" id="otp1" name="otp[]"
                                                        class="form-control mx-1 text-center" maxlength="1" required>
                                                    <input type="text" id="otp2" name="otp[]"
                                                        class="form-control mx-1 text-center" maxlength="1" required>
                                                    <input type="text" id="otp3" name="otp[]"
                                                        class="form-control mx-1 text-center" maxlength="1" required>
                                                    <input type="text" id="otp4" name="otp[]"
                                                        class="form-control mx-1 text-center" maxlength="1" required>
                                                    <input type="text" id="otp5" name="otp[]"
                                                        class="form-control mx-1 text-center" maxlength="1" required>
                                                </div>
                                            </div>
                                            <div class="text-center pt-4">
                                                <button type="submit" class="form-control btn btn-menu submit px-3">
                                                    ยืนยัน OTP
                                                </button>
                                            </div>
                                        </form>
                                        @if (session('error'))
                                        <div class="pt-2">
                                        <p class="text-danger text-center">{{ session('error') }}</p>
                                    </div>
                                        @endif                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('input[name="otp[]"]');
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    if (this.value.length === this.maxLength && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });
            });
        });
    </script>
@endsection
