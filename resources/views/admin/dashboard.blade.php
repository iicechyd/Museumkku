@extends(auth()->user()->role->role_name === 'Admin' ? 'layouts.layout_admin' : 'layouts.layout_executive')
@section('title', 'Dashboard')
@section('content')

    <head>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    <title>Dashboard</title>

    <x-layout bodyClass>
        <div class="container">
            <div id="activityCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner p-1">
                    @foreach ($activities as $activity)
                        @if ($activity->activity_id == 1 || $activity->activity_id == 2)
                            <div class="carousel-item @if ($loop->first) active @endif">
                                <div class="row gy-4 pt-4">
                                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                                        <div class="card ">
                                            <div class="card-header p-3 pt-2">
                                                <div
                                                    class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                                    <i class="material-icons opacity-10">weekend</i>
                                                </div>
                                                <div class="text-end pt-1">
                                                    <p class="text-sm mb-0 text-capitalize">
                                                        <strong>จำนวนผู้เข้าชมวันนี้</strong>
                                                    </p>
                                                    <p class="text-sm mb-0 text-capitalize"
                                                        style="color: {{ $activity->activity_id == 1 ? '#489085' : ($activity->activity_id == 2 ? '#C06628' : 'black') }};">
                                                        {{ $activity->activity_name }}</p>
                                                    <h4 class="mb-0">
                                                        {{ $totalVisitorsToday[$activity->activity_id] ?? 0 }} คน</h4>
                                                </div>
                                            </div>
                                            <hr class="dark horizontal my-0">
                                            <div class="card-footer p-3">
                                                <p class="mb-0"><span
                                                        class="text-success text-sm font-weight-bolder">+55%</span>
                                                    จากเมื่อวานนี้</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                                        <div class="card">
                                            <div class="card-header p-3 pt-2">
                                                <div
                                                    class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                                    <i class="material-icons opacity-10">person</i>
                                                </div>
                                                <div class="text-end pt-1">
                                                    <p class="text-sm mb-0 text-capitalize">
                                                        <strong>จำนวนผู้เข้าชมทั้งสัปดาห์</strong>
                                                    </p>
                                                    <p class="text-sm mb-0 text-capitalize"
                                                        style="color: {{ $activity->activity_id == 1 ? '#489085' : ($activity->activity_id == 2 ? '#C06628' : 'black') }};">
                                                        {{ $activity->activity_name }}</p>
                                                    <h4 class="mb-0">
                                                        {{ $totalVisitorsThisWeek[$activity->activity_id] ?? 0 }} คน</h4>
                                                </div>
                                            </div>
                                            <hr class="dark horizontal my-0">
                                            <div class="card-footer p-3">
                                                <p class="mb-0"><span
                                                        class="text-success text-sm font-weight-bolder">+3%</span>
                                                    จากสัปดาห์ที่แล้ว</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                                        <div class="card">
                                            <div class="card-header p-3 pt-2">
                                                <div
                                                    class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                                    <i class="material-icons opacity-10">person</i>
                                                </div>
                                                <div class="text-end pt-1">
                                                    <p class="text-sm mb-0 text-capitalize">
                                                        <strong>จำนวนผู้เข้าชมทั้งเดือน</strong>
                                                    </p>
                                                    <p class="text-sm mb-0 text-capitalize"
                                                        style="color: {{ $activity->activity_id == 1 ? '#489085' : ($activity->activity_id == 2 ? '#C06628' : 'black') }};">
                                                        {{ $activity->activity_name }}</p>
                                                    <h4 class="mb-0">
                                                        {{ $totalVisitorsThisMonth[$activity->activity_id] ?? 0 }} คน</h4>
                                                </div>
                                            </div>
                                            <hr class="dark horizontal my-0">
                                            <div class="card-footer p-3">
                                                <p class="mb-0"><span
                                                        class="text-danger text-sm font-weight-bolder">-2%</span>
                                                    จากเดือนที่แล้ว</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="card">
                                            <div class="card-header p-3 pt-2">
                                                <div
                                                    class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                                    <i class="material-icons opacity-10">weekend</i>
                                                </div>
                                                <div class="text-end pt-1">
                                                    <p class="text-sm mb-0 text-capitalize">
                                                        <strong>ยอดผู้เข้าชมทั้งหมดตลอดปี</strong>
                                                    </p>
                                                    <p class="text-sm mb-0 text-capitalize"
                                                        style="color: {{ $activity->activity_id == 1 ? '#489085' : ($activity->activity_id == 2 ? '#C06628' : 'black') }};">
                                                        {{ $activity->activity_name }}</p>
                                                    <h4 class="mb-0">
                                                        {{ $totalVisitorsThisYear[$activity->activity_id] ?? 0 }} คน</h4>
                                                </div>
                                            </div>
                                            <hr class="dark horizontal my-0">
                                            <div class="card-footer p-3">
                                                <p class="mb-0"><span
                                                        class="text-danger text-sm font-weight-bolder">-2%</span>
                                                    จากเดือนที่แล้ว</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#activityCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#activityCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
            <div class="row mt-4">
                <div class="col-lg-4 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <div class="chart">
                                    <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 ">ยอดผู้เข้าชมทั้งหมดในสัปดาห์นี้</h6>
                            <p class="text-sm">ผลการดำเนินงานล่าสุด</p>
                            <hr class="dark horizontal">
                            <div class="d-flex">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">อัปเดตล่าสุดเมื่อ 2 วันที่แล้ว</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2  ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                                <div class="chart">
                                    <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 ">ยอดผู้เข้าชมทั้งหมดในปีนี้</h6>
                            <p class="text-sm ">ตั้งแต่ มกราคม - ธันวาคม</p>
                            <hr class="dark horizontal">
                            <div class="d-flex ">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm"> อัปเดตเมื่อ 4 นาทีที่ผ่านมา </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mt-4 mb-3">
                    <div class="card z-index-2 ">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart">
                                    <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 ">ยอดการจัดกิจกรรมทั้งหมดในปีนี้</h6>
                            <p class="text-sm ">ตั้งแต่ มกราคม - ธันวาคม </p>
                            <hr class="dark horizontal">
                            <div class="d-flex ">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">อัปเดตเมื่อกี้</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h4 class="mb-4">จำนวนผู้เข้าชมตามการจอง</h4>
            <div class="row mb-4">
                @foreach ($activities->whereIn('activity_id', [1, 2]) as $activity)
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5>{{ $activity->activity_name }}</h5>
                                <h3 class="{{ $activity->activity_id == 1 ? 'text-info' : 'text-danger' }}">
                                    {{ number_format($totalVisitors[$activity->activity_id]) }} คน
                                </h3>
                            </div>
                        </div>
                    </div>
                @endforeach
                @php
                    $totalActivity3Visitors = isset($totalVisitors[3]) ? $totalVisitors[3] : 0;
                @endphp
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>เข้าชมทั้งสองพิพิธภัณฑ์</h5>
                            <h3 class="text-success">{{ number_format($totalActivity3Visitors) }} คน
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>ผู้เข้าชมทั้งหมด</h5>
                            <h3 class="text-warning">{{ number_format(array_sum($totalVisitors)) }} คน</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>ยอดการจัดกิจกรรม และจำนวนผู้เข้าร่วม</h6>
                                </div>
                                <div class="col-lg-6 col-5 my-auto text-end">
                                    <div class="dropdown float-lg-end pe-4">
                                        <a class="cursor-pointer" id="dropdownTable"
                                            data-bs-toggle="dropdown"aria-expanded="false">
                                            <i class="fa fa-ellipsis-v text-secondary"></i>
                                        </a>
                                        <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                            aria-labelledby="dropdownTable">
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a>
                                            </li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Another
                                                    action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Something
                                                    else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                กิจกรรมพิเศษ</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                จำนวนผู้เข้าร่วมกิจกรรม</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                จำนวนครั้ง/ปี</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($specialActivities as $activity)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $activity->activity_name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-xs font-weight-bold">{{ $activity->total_visitors > 0 ? $activity->total_visitors . ' คน' : '-' }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-xs font-weight-bold">{{ $activity->total_bookings > 0 ? $activity->total_bookings . ' ครั้ง' : '-' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>สถิติประเภทผู้เข้าชม</h6>
                        </div>
                    </div>
                    <div class="card align-items-center">
                        <div class="card-body p-3" style="width: 250px" height="250px">
                            <canvas id="visitorPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @push('js')
            <script src="{{ asset('material/assets/js/plugins/chartjs.min.js') }}"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                var children_qty = {{ $visitorStats->children_qty ?? 0 }};
                var students_qty = {{ $visitorStats->students_qty ?? 0 }};
                var adults_qty = {{ $visitorStats->adults_qty ?? 0 }};
                var disabled_qty = {{ $visitorStats->disabled_qty ?? 0 }};
                var elderly_qty = {{ $visitorStats->elderly_qty ?? 0 }};
                var monk_qty = {{ $visitorStats->monk_qty ?? 0 }};
                var ctx = document.getElementById("chart-bars").getContext("2d");
                var data = @json($totalVisitorsPerDayType1);
                var activitiesType2Monthly = @json($activitiesType2Monthly);
                window.totalVisitorsPerDayType1 = @json($totalVisitorsPerDayType1);
                window.totalVisitorsPerMonthThisYear = @json(array_values($totalVisitorsPerMonthThisYear));
            </script>
            <script src="{{ asset('js/dashboard.js') }}"></script>
        @endpush
    </x-layout>
@endsection
