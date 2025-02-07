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
    <div class="container py-5">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
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
                                    <div class="mb-5 text-center">
                                        <h2 class="h3">ยืนยันตัวตน</h2>
                                        <h3 class="fs-6 fw-normal text-secondary m-0">
                                            กรุณายืนยันด้วยอีเมลเพื่อดำเนินการต่อ</h3>
                                        <div class="pt-5">
                                            <img src="/img/user.png" style="width: 150px;">
                                        </div>
                                        <form method="POST" action="{{ route('sendVerificationLink') }}">
                                            @csrf
                                            <div class="form-group pt-4">
                                                <label for="email">Email</label>
                                                <input type="email" id="email" name="email" class="form-control"
                                                    placeholder="กรุณากรอกอีเมลของคุณ" required>
                                            </div>
                                            <div class="text-center pt-4">
                                                <button type="submit" class="form-control btn btn-menu submit px-3">
                                                    ส่งลิงก์ยืนยัน
                                                </button>
                                            </div>
                                        </form>
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
@endsection
</html>
