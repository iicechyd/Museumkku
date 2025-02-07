<!DOCTYPE html>
<html lang="en">
@extends('layouts.layout')
@section('title', 'กำลังรอการยืนยัน')

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>กำลังรอการยืนยัน</title>
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
                <div class="card-body p-3 p-md-4 p-xl-5 d-flex justify-content-center align-items-center" style="height: 100%;">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <h4 class="alert-heading mt-3">กรุณาดำเนินการยืนยันตัวตนในอีเมลของคุณ</h4>
                        <p>เรากำลังตรวจสอบการยืนยันของคุณ กรุณารอขณะนี้...</p>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>
</div>
<script>
    function checkVerificationStatus() {
        const email = "{{ session('verification_email') }}";
        if (!email) return;

        fetch(`/check-verification/${email}`)
            .then(response => response.json())
            .then(data => {
                if (data.verified === 1) {
                    window.location.href = "{{ session('redirect_url') ?? route('form_bookings.activity', ['activity_id' => 1]) }}";
                }
            });
    }

    setInterval(checkVerificationStatus, 5000);
</script>

@endsection
</html>
