@extends(auth()->user()->role->role_name === 'Admin' ? 'layouts.layout_admin' : 'layouts.layout_executive')
@section('title', 'Dashboard')
@section('content')

    <head>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <style>
        #targetSection .table-responsive {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
    
    <title>Dashboard</title>

    <x-layout bodyClass>
        <div class="container">
            <div id="activityCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner p-3">
                    @foreach ($activities as $activity)
                        @if ($activity->activity_id == 1 || $activity->activity_id == 2)
                            <div class="carousel-item @if ($loop->first) active @endif">
                                <div class="row gy-4 pt-4">
                                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                                        <div class="card">
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
                                                        {{ $activity->activity_name }}
                                                    </p>
                                                    <h4 class="mb-0">
                                                        {{ $totalVisitorsToday[$activity->activity_id] ?? 0 }} คน
                                                    </h4>
                                                </div>
                                            </div>
                                            <hr class="dark horizontal my-0">
                                            <div class="card-footer p-3">
                                                <p class="mb-0">
                                                    <span
                                                        class="text-sm font-weight-bolder 
                                                                    @if ($percentageChangeToday[$activity->activity_id] > 0) text-success
                                                                    @elseif ($percentageChangeToday[$activity->activity_id] < 0)
                                                                        text-danger
                                                                    @else
                                                                    text-muted @endif
                                                                        ">
                                                        @if ($percentageChangeToday[$activity->activity_id] > 0)
                                                            +{{ number_format($percentageChangeToday[$activity->activity_id]) }}%
                                                        @elseif ($percentageChangeToday[$activity->activity_id] < 0)
                                                            {{ number_format($percentageChangeToday[$activity->activity_id]) }}%
                                                        @else
                                                            0%
                                                        @endif
                                                    </span> จากเมื่อวานนี้
                                                </p>
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
                                                        {{ $activity->activity_name }}
                                                    </p>
                                                    <h4 class="mb-0">
                                                        {{ $totalVisitorsThisWeek[$activity->activity_id] ?? 0 }} คน
                                                    </h4>
                                                </div>
                                            </div>
                                            <hr class="dark horizontal my-0">
                                            <div class="card-footer p-3">
                                                <p class="mb-0">
                                                    <span
                                                        class="text-sm font-weight-bolder 
                                                    @if ($percentageChangeThisWeek[$activity->activity_id] > 0) text-success
                                                    @elseif ($percentageChangeThisWeek[$activity->activity_id] < 0)
                                                        text-danger
                                                    @else
                                                    text-muted @endif
                                                    ">
                                                        @if ($percentageChangeThisWeek[$activity->activity_id] > 0)
                                                            +{{ number_format($percentageChangeThisWeek[$activity->activity_id]) }}%
                                                        @elseif ($percentageChangeThisWeek[$activity->activity_id] < 0)
                                                            {{ number_format($percentageChangeThisWeek[$activity->activity_id]) }}%
                                                        @else
                                                            0%
                                                        @endif
                                                    </span> จากสัปดาห์ที่แล้ว
                                                </p>
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
                                                        {{ $activity->activity_name }}
                                                    </p>
                                                    <h4 class="mb-0">
                                                        {{ $totalVisitorsThisMonth[$activity->activity_id] ?? 0 }} คน
                                                    </h4>
                                                </div>
                                            </div>
                                            <hr class="dark horizontal my-0">
                                            <div class="card-footer p-3">
                                                <p class="mb-0">
                                                    <span
                                                        class="text-sm font-weight-bolder
                                                                @if ($percentageChangeMonth[$activity->activity_id] > 0) text-success
                                                                @elseif ($percentageChangeMonth[$activity->activity_id] < 0)
                                                                    text-danger
                                                                @else
                                                                text-muted @endif
                                                                    ">
                                                        @if ($percentageChangeMonth[$activity->activity_id] > 0)
                                                            +{{ number_format($percentageChangeMonth[$activity->activity_id]) }}%
                                                        @elseif ($percentageChangeMonth[$activity->activity_id] < 0)
                                                            {{ number_format($percentageChangeMonth[$activity->activity_id]) }}%
                                                        @else
                                                            0%
                                                        @endif
                                                    </span> จากเดือนที่แล้ว
                                                </p>
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
                                                        {{ $activity->activity_name }}
                                                    </p>
                                                    <h4 class="mb-0">
                                                        {{ $totalVisitorsThisYear[$activity->activity_id] ?? 0 }} คน
                                                    </h4>
                                                </div>
                                            </div>
                                            <hr class="dark horizontal my-0">
                                            <div class="card-footer p-3">
                                                <p class="mb-0">
                                                    <span
                                                        class="text-sm font-weight-bolder
                                                @if ($percentageChangeYear[$activity->activity_id] > 0) text-success
                                                @elseif ($percentageChangeYear[$activity->activity_id] < 0) 
                                                    text-danger
                                                @else 
                                                    text-muted @endif
                                                ">
                                                        @if ($percentageChangeYear[$activity->activity_id] > 0)
                                                            +{{ number_format($percentageChangeYear[$activity->activity_id]) }}%
                                                        @elseif ($percentageChangeYear[$activity->activity_id] < 0)
                                                            {{ number_format($percentageChangeYear[$activity->activity_id]) }}%
                                                        @else
                                                            0%
                                                        @endif
                                                    </span> จากปีที่แล้ว
                                                </p>
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
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <div class="chart">
                                    <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0">ยอดผู้เข้าชมทั้งหมดในปีนี้</h6>
                            <p class="text-sm">ปีงบประมาณ {{ $startMonthThai }} - {{ $endMonthThai }}</p>
                            <hr class="dark horizontal">
                            <div class="d-flex">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">อัปเดตเมื่อกี้</p>
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
                            <h6 class="mb-0 ">รายได้การเข้าชมทั้งหมดในปีนี้</h6>
                            <p class="text-sm">ปีงบประมาณ {{ $startMonthThai }} - {{ $endMonthThai }}</p>
                            <hr class="dark horizontal">
                            <div class="d-flex ">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">อัปเดตเมื่อกี้</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mt-4 mb-3">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                <div class="chart">
                                    <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0">รายได้การจัดกิจกรรมทั้งหมดในปีนี้</h6>
                            <p class="text-sm">ปีงบประมาณ {{ $startMonthThai }} - {{ $endMonthThai }}</p>
                            <hr class="dark horizontal">
                            <div class="d-flex ">
                                <i class="material-icons text-sm my-auto me-1">schedule</i>
                                <p class="mb-0 text-sm">อัปเดตเมื่อกี้</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $totalVisitorsActivityBooked = isset($totalVisitorsBooked[3]) ? $totalVisitorsBooked[3] : 0;
                $totalWalkinActivityBooked = isset($totalWalkinBooked[3]) ? $totalWalkinBooked[3] : 0;
            @endphp

            <h4 id="visitorTitle" class="mb-4" data-start-month="{{ $startMonthThai }}"
                data-end-month="{{ $endMonthThai }}">จำนวนผู้เข้าชมตามการจองในปีนี้
                <p class="text-sm">ปีงบประมาณ {{ $startMonthThai }} - {{ $endMonthThai }}</p>
            </h4>

            <div id="visitorCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner p-2">
                    <!-- จำนวนผู้เข้าชมตามการจอง -->
                    <div class="carousel-item active">
                        <div class="row mb-4">
                            @foreach ($activities->whereIn('activity_id', [1, 2]) as $activity)
                                <div class="col-md-3 pt-3">
                                    <div class="card text-center" style="min-height: 11vh; display: flex; flex-direction: column;">
                                        <div class="card-body">
                                            <h5>{{ $activity->activity_name }}</h5>
                                            <h3 class="{{ $activity->activity_id == 1 ? 'text-info' : 'text-danger' }}">
                                                {{ number_format($totalVisitorsBooked[$activity->activity_id]) }} คน
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-3 pt-3">
                                <div class="card text-center" style="min-height: 11vh; display: flex; flex-direction: column;">
                                    <div class="card-body">
                                        <h5>เข้าชมทั้งสองพิพิธภัณฑ์</h5>
                                        <h3 class="text-success">{{ number_format($totalVisitorsActivityBooked) }} คน</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 pt-3">
                                <div class="card text-center" style="min-height: 11vh; display: flex; flex-direction: column;">
                                    <div class="card-body">
                                        <h5>ผู้เข้าชมทั้งหมด</h5>
                                        <h3 class="text-warning">{{ number_format(array_sum($totalVisitorsBooked)) }} คน
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- จำนวนผู้เข้าชมวอคอิน -->
                    <div class="carousel-item">
                        <div class="row mb-4">
                            @foreach ($activities->whereIn('activity_id', [1, 2]) as $activity)
                                <div class="col-md-3 pt-3">
                                    <div class="card text-center" style="min-height: 11vh; display: flex; flex-direction: column;">
                                        <div class="card-body">
                                            <h5>{{ $activity->activity_name }}</h5>
                                            <h3 class="{{ $activity->activity_id == 1 ? 'text-info' : 'text-danger' }}">
                                                {{ number_format($totalWalkinBooked[$activity->activity_id]) }} คน
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-3 pt-3">
                                <div class="card text-center" style="min-height: 11vh; display: flex; flex-direction: column;">
                                    <div class="card-body">
                                        <h5>เข้าชมทั้งสองพิพิธภัณฑ์</h5>
                                        <h3 class="text-success">{{ number_format($totalWalkinActivityBooked) }} คน</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 pt-3">
                                <div class="card text-center" style="min-height: 11vh; display: flex; flex-direction: column;">
                                    <div class="card-body">
                                        <h5>ผู้เข้าชมทั้งหมด</h5>
                                        <h3 class="text-warning">{{ number_format(array_sum($totalWalkinBooked)) }} คน
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#visitorCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#visitorCarousel"
                    data-bs-slide="next" >
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        <div class="row mb-4">
            <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-lg-6 col-7">
                                <h6>ยอดการจัดกิจกรรม และจำนวนผู้เข้าร่วมปีนี้</h6>
                                <p class="text-sm">ปีงบประมาณ {{ $startMonthThai }} - {{ $endMonthThai }}</p>
                            </div>
                            <div class="col-lg-6 col-5 my-auto text-end">
                                <div class="dropdown float-lg-end pe-4">
                                    <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa fa-ellipsis-v text-secondary"></i>
                                    </a>
                                    <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
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
                    <div class="card-body px-0 pb-2" id="targetSection">
                        <div class="table-responsive max-h-48 overflow-y-auto">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
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
                                    @foreach ($totalSpecialActivity as $activity)
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
                                                    class="text-xs font-weight-bold">{{ $activity->total_bookings > 0 ? $activity->total_bookings . '' : '-' }}
                                                    {{ $activity->target_yearly_count > 0 ? '/ ' . $activity->target_yearly_count . '' : '' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="align-middle text-center text-sm" colspan="2">
                                            <h6 class="mb-0 text-sm">จำนวนผู้เข้าร่วมกิจกรรมทั้งหมด</h6>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-xs font-weight-bold">
                                                <h6 class="mb-0 text-sm">{{ $totalSpecialActivity->sum('total_visitors') }} คน</h6>
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>สถิติประเภทผู้เข้าชมปีนี้</h6>
                        <p class="text-sm">ปีงบประมาณ {{ $startMonthThai }} - {{ $endMonthThai }}</p>
                    </div>
                <div class="card align-items-center">
                    <div class="card-body p-3" style="width: 250px; height: 325px;">
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
                var kid_qty = {{ $visitorStats->kid_qty ?? 0 }};
                var disabled_qty = {{ $visitorStats->disabled_qty ?? 0 }};
                var elderly_qty = {{ $visitorStats->elderly_qty ?? 0 }};
                var monk_qty = {{ $visitorStats->monk_qty ?? 0 }};
                var free_teachers_qty = {{ $visitorStats->free_teachers_qty ?? 0 }};
                var totalVisitorsPerMonthThisYear = @json($totalVisitorsPerMonthThisYear);
                var yearlyRevenueGeneral = @json($yearlyRevenueGeneral);
                var yearlyRevenueActivity = @json($yearlyRevenueActivity);
            </script>
            <script src="{{ asset('js/dashboard.js') }}"></script>
        @endpush
    </x-layout>
@endsection
