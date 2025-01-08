@extends('layouts.layout')

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>เปลี่ยนรหัสผ่านบัญชีผู้ใช้งาน</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link
        rel="stylesheet"href="https://unpkg.com/bs-brain@2.0.4/components/password-resets/password-reset-5/assets/css/password-reset-5.css">
</head>
<style>
    .custom-card-size {
        max-width: 500px;
        margin: auto;
    }
</style>

@section('content')
    <section class="p-3 p-md-4 p-xl-5">
        <div class="container">
            <div class="card border-light-subtle shadow-sm custom-card-size">
                <div class="card-body p-3 p-md-4 p-xl-5">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-4">
                                <h2 class="h3">Password Reset</h2>
                                <h3 class="fs-6 fw-normal text-secondary m-0">
                                    กรุณากรอกอีเมลที่เชื่อมโยงกับบัญชีผู้ใช้งานของคุณเพื่อเปลี่ยนรหัสผ่าน</h3>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <div class="row gy-3 gy-md-4 overflow-hidden">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="col-12">
                                Email
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div>
                                Password
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required
                                    autocomplete="new-password" placeholder="กรุณากรอกรหัสผ่านใหม่">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div>
                                Confirm Password
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation" required autocomplete="new-password" placeholder="กรุณายืนยันรหัสผ่านใหม่อีกครั้ง">
                            </div>
                            <div class="col-12">
                                <div class="d-grid">
                                    <button class="btn bsb-btn-xl" style="background-color: #E6A732; color: #fff;"
                                        type="submit">เปลี่ยนรหัสผ่าน
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
