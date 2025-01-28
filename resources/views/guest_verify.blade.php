@extends('layouts.layout')

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ยืนยันตัวตน</title>
</head>

@section('content')
    <section class="ftco-section">
        <div class="container">
            <div class="card border-light-subtle shadow-sm">
                <div class="row justify-content-center">
                    <div class="col-10 col-md-6" style="background-color: #ECECEC;">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="col-10 col-xl-8 py-3">
                                <img class="img-fluid rounded mb-4" src="/img/logo_mlc_sim1.png" width="500"
                                    height="80">
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
                                            <div class="text-center pt-4 ">
                                                <img src="/img/web_light_rd_ctn.svg" class="img-fluid" id="google-login-img"
                                                    style="cursor: pointer;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </section>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('google-login-img').addEventListener('click', function() {
            window.location.href = 'http://127.0.0.1:8000/auth/redirect';
        });
    });
</script>
