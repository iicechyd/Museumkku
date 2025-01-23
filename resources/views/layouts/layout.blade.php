<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ระบบจองเข้าชมพิพิธภัณฑ์</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    </head>
<style>
    html, body {
        font-family: 'Noto Sans Thai', sans-serif;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
    }
    .custom-padding-top {
    padding-top: 50px;
}

    .footer {
        margin-top: auto;
    }
</style>
<body>
    <nav class="navbar navbar-expand-lg" style="background-color: #ECECEC;">
        <div class="container-fluid">
            <a class="navbar-brand">
                <img src="/img/logo.png" height="50" alt="" loading="logo" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('showPreview') }}">
                            <button class="btn nav-item nav-link">
                                หน้าหลัก
                            </button>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('calendar.showCalendar') }}">
                            <button class="btn nav-item nav-link">
                                ปฏิทินพิพิธภัณฑ์
                            </button>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="info">
                            <button class="btn nav-item nav-link">
                                ข้อมูลพิพิธภัณฑ์
                            </button>
                        </a>
                    </li>
                    <li class="nav-item">
                        @auth
                            @if (Auth::user()->is_approved)
                                @if (Auth::user()->role && Auth::user()->role->role_name === 'Super Admin')
                                    <a class="nav-link" href="{{ url('super_admin/all_users') }}">
                                        <button class="btn btn-menu shadow-sm hover-shadow-lg">
                                            เข้าสู่หน้าผู้ดูแลระบบ
                                        </button>
                                    </a>
                                @elseif (Auth::user()->role && Auth::user()->role->role_name === 'Admin')
                                    <a class="nav-link" href="{{ url('admin/dashboard') }}">
                                        <button class="btn btn-menu shadow-sm hover-shadow-lg">
                                            เข้าสู่หน้าเจ้าหน้าที่
                                        </button></a>
                                @elseif (Auth::user()->role && Auth::user()->role->role_name === 'Executive')
                                    <a class="nav-link" href="{{ url('executive/dashboard') }}">
                                        <button class="btn btn-menu shadow-sm hover-shadow-lg">
                                            เข้าสู่หน้าฝ่ายบริหาร
                                        </button>
                                    </a>
                                @endif
                            @endif
                        @else
                            <a class="nav-link" href="{{ route('login') }}">
                                <button class="btn btn-menu shadow-sm hover-shadow-lg">
                                    เข้าสู่ระบบ
                                </button>
                            </a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="custom-padding-top">
        @yield('content')
    </div>
    <div class="footer">
        <footer class="text-center" style="background-color: #F9F9F9;">
            <div class="text-center text-black p-3">
                Copyright © | ศูนย์พิพิธภัณฑ์ และแหล่งเรียนรู้ตลอดชีวิต || มหาวิทยาลัยขอนแก่น | Natural History Museum
            </div>
        </footer>
    </div>
</body>
</html>