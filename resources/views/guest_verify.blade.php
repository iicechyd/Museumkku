<!DOCTYPE html>
<html lang="en">
@extends('layouts.layout')
@section('title', 'ยืนยันตัวตนผู้จองเข้าชม')

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/guest_verify.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ยืนยันตัวตนผู้จองเข้าชม</title>
</head>

@section('content')
<section class="ftco-section">
    <div class="container">
        <div class="card border-light-subtle shadow-sm">
            <div class="row justify-content-center">
                <div class="col-10 col-md-6" style="background-color: #ECECEC;">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="col-10 col-xl-8 py-3">
                            <img class="img-fluid rounded mb-4" src="/img/logo_mlc_sim1.png" width="500" height="80">
                        </div>
                    </div>
                </div>
                <div class="col-10 col-md-6">
                    <div class="card-body p-3 p-md-4 p-xl-5">
                        <div class="row justify-content-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="col-10 justify-content-center">
                                    <div class="mb-5 text-center ">
                                        <h2 class="h3">ยืนยันตัวตน</h2>
                                        <h3 class="fs-6 fw-normal text-secondary m-0">
                                            กรุณายืนยันด้วยอีเมลเพื่อดำเนินการต่อ</h3>
                                        <div class="pt-5">
                                            <img src="/img/user.png" style="width: 150px;">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="label" for="email">Email</label>
                                            <input type="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="Email" required name="email" value="{{ old('email') }}"
                                                required autocomplete="email" autofocus>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="form-control btn btn-menu submit px-3">
                                                ส่งรหัสผ่าน
                                            </button>
                                        </div>
                                        <div>
                                            <div class="text-center pt-4">
                                                <button class="gsi-material-button">
                                                    <div class="gsi-material-button-state"></div>
                                                    <div class="gsi-material-button-content-wrapper">
                                                        <div class="gsi-material-button-icon">
                                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 48 48"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                style="display: block;">
                                                                <path fill="#EA4335"
                                                                    d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z">
                                                                </path>
                                                                <path fill="#4285F4"
                                                                    d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z">
                                                                </path>
                                                                <path fill="#FBBC05"
                                                                    d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z">
                                                                </path>
                                                                <path fill="#34A853"
                                                                    d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z">
                                                                </path>
                                                                <path fill="none" d="M0 0h48v48H0z"></path>
                                                            </svg>
                                                        </div>
                                                        <span class="gsi-material-button-contents">Continue with
                                                            Google</span>
                                                        <span style="display: none;">Continue with Google</span>
                                                    </div>
                                                </button>
                                                <button class="gsi-material-button">
                                                    <div class="gsi-material-button-state"></div>
                                                    <div class="gsi-material-button-content-wrapper">
                                                        <div class="gsi-material-button-icon">
                                                            <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg"
                                                                alt="Microsoft Logo">
                                                        </div>
                                                        <span class="gsi-material-button-contents">Sign in with
                                                            Microsoft</span>
                                                        <span style="display: none;">Sign in with Microsoft</span>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector('.gsi-material-button').addEventListener('click', function () {
            window.location.href = 'http://127.0.0.1:8000/auth/redirect';
        });
    });
</script>
@endsection

</html>