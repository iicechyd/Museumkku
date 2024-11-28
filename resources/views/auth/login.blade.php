@extends('layouts.layout')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>เข้าสู่ระบบ | สำหรับเจ้าหน้าที่</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<body>
    @section('content')
        <section class="ftco-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12 col-lg-10">
                        <div class="wrap d-md-flex">
                            <div class="text-wrap p-4 p-lg-5 text-center d-flex align-items-center order-md-last">
                                <div class="text w-100">
                                    <h2>ยินดีต้อนรับเข้าสู่ระบบ</h2>
                                    <p>Don't have an account?</p>
                                    <a href="/register" class="btn btn-white btn-outline-white">Sign Up</a>
                                </div>
                            </div>
                            <div class="login-wrap p-4 p-lg-5">
                                <div class="d-flex">
                                    <div class="w-100">
                                        <h3 class="mb-4">เข้าสู่ระบบ | เจ้าหน้าที่</h3>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="label" for="email">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            placeholder="Email" required name="email" value="{{ old('email') }}" required
                                            autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label" for="password">Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="current-password" placeholder="Password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="form-control btn btn-primary submit px-3">
                                            เข้าสู่ระบบ
                                        </button>
                                    </div>
                                    <div class="form-group d-md-flex">
                                        <div class="w-50 text-left">
                                            <label class="checkbox-wrap checkbox-primary mb-0">จดจำรหัสผ่าน
                                                <input type="checkbox" checked name="remember"
                                                    id="remember"{{ old('remember') ? 'checked' : '' }}>
                                                <span class="checkmark" for="remember"></span>
                                            </label>
                                        </div>
                                        <div class="w-50 text-md-right">
                                            @if (Route::has('password.request'))
                                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                                    ลืมรหัสผ่าน
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <script src="js/login/jquery.min.js"></script>
            <script src="js/login/popper.js"></script>
            <script src="js/login/bootstrap.min.js"></script>
            <script src="js/login/main.js"></script>
        </section>
    @endsection
</body>
