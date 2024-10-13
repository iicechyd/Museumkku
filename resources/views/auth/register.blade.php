@extends('layouts.layout')

<head>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
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
@endsection


{{-- @extends('layouts.layout')

<head>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
@section('content')

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Register') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="name"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password-confirm"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}
