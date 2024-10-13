@extends('layouts.layout')
@section('title', 'เบิ่งบ่ ระบบจองเข้าชมพิพิธภัณฑ์')

<head>
    <link rel="stylesheet" href="{{ asset('css/card.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Maitree&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

@section('content')
    <div class="container-fluid p-0">
        <div class="row align-items-center"
            style="background-image: url('/img/bg_cover.png'); background-color: #C5C6C7; background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh; width: 100%; margin: 0; padding: 0;">
            <div class="col-12 col-md-8 offset-md-2 text-left text-md-left" style="padding-left: 2rem;">
                <h1 class="display-4 font-weight-bold"
                    style="font-family: 'Maitree', serif; color: white; text-shadow: 3px 2px 2px rgba(0, 0, 0, 0.3);">
                    ศูนย์พิพิธภัณฑ์
                </h1>
                <h1 class="display-4 font-weight-bold"
                    style="font-family: 'Maitree', serif; color: white; text-shadow: 3px 2px 2px rgba(0, 0, 0, 0.3);">
                    และแหล่งเรียนรู้ตลอดชีวิต
                </h1>
                <h2 class="display-4 font-weight-bold"
                    style="font-family: 'Maitree', serif; color: white; text-shadow: 3px 2px 2px rgba(0, 0, 0, 0.3);">
                    มหาวิทยาลัยขอนแก่น
                </h2>
                <a href="{{ url('/preview') }}" class="btn btn-lg"
                    style="font-family: 'Noto Sans Thai', sans-serif; background-color: black; color: white; font-size: 1.6rem; padding: 0.45rem 3rem; border-radius: 0; transition: all 0.3s ease;"
                    onmouseover="this.style.backgroundColor='white'; this.style.color='black';"
                    onmouseout="this.style.backgroundColor='black'; this.style.color='white';">
                    จองเข้าชม
                </a>
            </div>
        </div>
    </div>
@endsection
