@extends('layouts.layout')

<head>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

{{-- @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif --}}

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        });
    </script>
@endif

@section('content')
    <div class="container py-4">
        <div class="wrapper">
            <h2>สมัครสมาชิก</h2>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="input-box">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                        placeholder="กรุณากรอกชื่อของคุณ" name="name" value="{{ old('name') }}" required
                        autocomplete="name" autofocus>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="input-box">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        placeholder="กรุณาอีเมลของคุณ" name="email" value="{{ old('email') }}" required
                        autocomplete="email">

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="input-box">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="กรุณากรอกรหัสผ่านของคุณ" name="password" required autocomplete="new-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="input-box">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                        placeholder="กรุณากรอกรหัสผ่านของคุณอีกครั้ง" autocomplete="new-password">
                </div>
                {{-- <div class="policy">
                    <input type="checkbox">
                    <h3>ฉันยอมรับข้อกำหนดและเงื่อนไขทั้งหมด</h3>
                </div> --}}
                <div class="input-box button">
                    <input type="Submit" value="สมัครสมาชิก">
                </div>
                <div class="text">
                    <h3>มีบัญชีอยู่แล้วใช่หรือไม่? <a href="login">เข้าสู่ระบบ</a></h3>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">แจ้งเตือน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ session('success') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection

