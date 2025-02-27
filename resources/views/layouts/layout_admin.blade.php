<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin_page.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo_page.png') }}">
    <title>ระบบจองเข้าชมพิพิธภัณฑ์ | สำหรับเจ้าหน้าที่</title>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar">
            <div class="h-100">
                <div class="sidebar-logo">
                    <a href="#">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid">
                    </a>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">
                        <h3 class="text-center">ยินดีต้อนรับ <br> {{ Auth::user()->name }}</h3>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ url('/admin/dashboard') }}" class="sidebar-link">
                            <i class="fa-solid fa-list pe-2"></i>
                            แดชบอร์ด
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                            data-bs-target="#multi" aria-expanded="false" aria-controls="multi">
                            <i class="fa-regular fa-file-lines pe-2"></i>
                            จัดการการจอง
                        </a>

                        <ul id="multi" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                data-bs-target="#multi-two" aria-expanded="false" aria-controls="multi-two">
                                สถานะการจอง
                            </a>
                            <ul id="multi-two" class="sidebar-dropdown list-unstyled collapse">
                                <li class="sidebar-item">
                                    <a href="{{ route('today_bookings.general') }}"
                                        class="sidebar-link">การจองเข้าชมทั่วไป</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{ route('today_bookings.activity') }}"
                                        class="sidebar-link">การจองเข้าร่วมกิจกรรม</a>
                                </li>
                            </ul>
                            <li class="sidebar-item">
                                <a href="/admin/admin_calendar" class="sidebar-link">ปฏิทินพิพิธภัณฑ์</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{route('booking.history.all')}}" class="sidebar-link">ประวัติการจองทั้งหมด</a>
                            </li>
                    </li>
                </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                        data-bs-target="#dashboard" aria-expanded="false" aria-controls="dashboard">
                        <i class="fa-solid fa-sliders pe-2"></i>
                        จัดการพิพิธภัณฑ์
                    </a>
                    <ul id="dashboard" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="/admin/activity_list" class="sidebar-link">จัดการรายละเอียดกิจกรรม</a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('showTimeslots') }}" class="sidebar-link">จัดการรอบการเข้าชม</a>
                        </li>
                    </ul>
                </li>
                <li class="border-top my-3" style="border-top: 1px solid gray !important;"></li>
                <div class="sidebar-footer">
                    <a href="{{ route('logout') }}" class="sidebar-link"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket pe-2"></i>
                        <span>ออกจากระบบ</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
                </ul>
            </div>
        </aside>
        <!-- Main Component -->
        <div class="main">
            <nav class="navbar-expand px-3 border-bottom">
                <!-- Button for sidebar toggle -->
                <button class="btn" type="button" data-bs-theme="dark">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </nav>
            <main class="content px-3 py-2">
                <div class="container-fluid">
                    <div class="mb-3">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <script>
        const toggler = document.querySelector(".btn");
        toggler.addEventListener("click", function() {
            document.querySelector("#sidebar").classList.toggle("collapsed");
        });
    </script>
</body>
</html>
