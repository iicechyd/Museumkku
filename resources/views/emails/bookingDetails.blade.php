<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/bookingPending.css') }}">
    <title>รายละเอียดการจองเข้าชม</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <div class="logo"></div>
            </div>
            <h2 style="color: #489085;">
                ระบบจองเข้าชมศูนย์พิพิธภัณฑ์ <br>
                <span style="color: #E6A732;">และแหล่งเรียนรู้ตลอดชีวิต</span>
                <span style="color: #C06628;">มหาวิทยาลัยขอนแก่น</span>
            </h2>
            <div class="flex items-center space-x-6 text-sm">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-phone"></i>
                    <span>094-278-4222, 0-4300-9700 ต่อ 45596</span>
                    <i class="fa-solid fa-globe"></i>
                    <span>museum.kku.ac.th</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-envelope"></i>
                    <span>kasaba@kku.ac.th, rapeka@kku.ac.th, supaco@kku.ac.th</span>
                </div>
            </div>
        </div>

        <div class="details-container">
            <div class="details">
                <h3 style="color: #489085;">รายละเอียดการจองเข้าชม</h3>
                <p>วันที่จอง: {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
                <p>ประเภทการเข้าชม: {{ $booking->activity->activity_name }}</p>
                @if ($booking->timeslot)
                <p>รอบการเข้าชม
                {{ \Carbon\Carbon::parse($booking->timeslot->start_time)->format('H:i') }} น. -
                {{ \Carbon\Carbon::parse($booking->timeslot->end_time)->format('H:i') }} น.</p>
                </p>
            @endif
                @if (!$booking->subActivities->isEmpty())
                    <p>หลักสูตร:
                        {{ $booking->subActivities->pluck('sub_activity_name')->implode(', ') }}
                    </p>
                @endif
                <p>ชื่อหน่วยงาน: {{ $booking->institute->instituteName }}</p>
                <p>ที่อยู่หน่วยงาน: {{ $booking->institute->instituteAddress }} {{ $booking->institute->subdistrict }}
                    {{ $booking->institute->district }} {{ $booking->institute->province }} {{ $booking->institute->zipcode }}</p>
                @if (!empty($booking->note))
                    <p>*หมายเหตุ: {{ $booking->note }}</p>
                @endif
            </div>
            <div class="details booking-info">
                @if ($booking->status == 0)
                    <h1 style="color: #E6A732;">รออนุมัติการจอง</h1>
                @elseif ($booking->status == 1)
                    <h1 style="color: #489085;">อนุมัติการจอง</h1>
                @elseif ($booking->status == 3)
                    <h1 style="color: #ff0000;">ยกเลิกการจอง</h1>
                @endif
            </div>
        </div>
        <div class="details-container">
            <div class="details">
                <h3 style="color: #489085;">ผู้ประสานงาน</h3>
                <p>ชื่อ-นามสกุล: {{ $booking->visitor->visitorName }}</p>
                <p>อีเมล: {{ $booking->visitor->visitorEmail }}</p>
                <p>เบอร์โทรศัพท์: {{ $booking->visitor->tel }}</p>
            </div>

            <div class="details booking-info">
                <h3 style="color: #489085;">ข้อมูลการจองเข้าชม</h3>
                <p>จองเมื่อ: {{ \Carbon\Carbon::parse($booking->visitor->created_at)->format('d/m/Y') }}</p>
                <p>สถานะ: @if ($booking->status == 0)
                    <span>รออนุมัติการจอง</span>
                @elseif ($booking->status == 1)
                    <span>อนุมัติการจอง</span>
                @elseif ($booking->status == 3)
                    <span>ยกเลิกการจอง</span>
                @endif</p>
            </div>
        </div>

        <table class="table-container">
            <tr>
                <th>ประเภทผู้เข้าชม</th>
                <th>จำนวนคน</th>
                <th>ราคา/คน</th>
                <th>จำนวนเงิน (THB)</th>
            </tr>
            @php
                $totalPrice = 0;
            @endphp
            @if ($booking->children_qty > 0)
                <tr>
                    <td>เด็ก</td>
                    <td>{{ $booking->children_qty }} คน</td>
                    <td>{{ number_format($booking->activity->children_price, 2) }} บาท</td>
                    <td>{{ number_format($booking->children_qty * $booking->activity->children_price, 2) }} บาท</td>
                </tr>
                @php $totalPrice += $booking->children_qty * $booking->activity->children_price; @endphp
            @endif
            @if ($booking->students_qty > 0)
                <tr>
                    <td>นร / นศ</td>
                    <td>{{ $booking->students_qty }} คน</td>
                    <td>{{ number_format($booking->activity->student_price, 2) }} บาท</td>
                    <td>{{ number_format($booking->students_qty * $booking->activity->student_price, 2) }} บาท</td>
                </tr>
                @php $totalPrice += $booking->students_qty * $booking->activity->student_price; @endphp
            @endif
            @if ($booking->adults_qty > 0)
                <tr>
                    <td>ผู้ใหญ่ / คุณครู</td>
                    <td>{{ $booking->adults_qty }} คน</td>
                    <td>{{ number_format($booking->activity->adult_price, 2) }} บาท</td>
                    <td>{{ number_format($booking->adults_qty * $booking->activity->adult_price, 2) }} บาท</td>
                </tr>
                @php $totalPrice += $booking->adults_qty * $booking->activity->adult_price; @endphp
            @endif
            @if ($booking->kid_qty > 0)
                <tr>
                    <td>เด็กเล็ก</td>
                    <td>{{ $booking->kid_qty }} คน</td>
                    <td>{{ number_format($booking->activity->kid_price, 2) }} บาท</td>
                    <td>{{ number_format($booking->kid_qty * $booking->activity->kid_price, 2) }} บาท</td>
                </tr>
                @php $totalPrice += $booking->kid_qty * $booking->activity->kid_price; @endphp
            @endif
            @if ($booking->disabled_qty > 0)
                <tr>
                    <td>ผู้พิการ</td>
                    <td>{{ $booking->disabled_qty }} คน</td>
                    <td>{{ number_format($booking->activity->disabled_price, 2) }} บาท</td>
                    <td>{{ number_format($booking->disabled_qty * $booking->activity->disabled_price, 2) }} บาท</td>
                </tr>
                @php $totalPrice += $booking->disabled_qty * $booking->activity->disabled_price; @endphp
            @endif
            @if ($booking->elderly_qty > 0)
                <tr>
                    <td>ผู้สูงอายุ</td>
                    <td>{{ $booking->elderly_qty }} คน</td>
                    <td>{{ number_format($booking->activity->elderly_price, 2) }} บาท</td>
                    <td>{{ number_format($booking->elderly_qty * $booking->activity->elderly_price, 2) }} บาท</td>
                </tr>
                @php $totalPrice += $booking->elderly_qty * $booking->activity->elderly_price; @endphp
            @endif
            @if ($booking->monk_qty > 0)
                <tr>
                    <td>พระภิกษุสงฆ์ / สามเณร</td>
                    <td>{{ $booking->monk_qty }} รูป</td>
                    <td>{{ number_format($booking->activity->monk_price, 2) }} บาท</td>
                    <td>{{ number_format($booking->monk_qty * $booking->activity->monk_price, 2) }} บาท</td>
                </tr>
                @php
                    $totalPrice += $booking->monk_qty * $booking->activity->monk_price;
                @endphp
            @endif
        </table>

        <div class="total">
            <p style="color: #489085;"><strong>ยอดรวมทั้งหมด: {{ number_format($totalPrice, 2) }} บาท</strong></p>
        </div>

        <div class="footer">
            <h3 style="color: #489085;">ข้อมูลเพิ่มเติม</h3>
            <p>เวลาทำการ 08.30 น. - 16.30 น.</p>
            <p>เวลาดำเนินกิจกรรม 08.30 น. - 15.00 น.</p>
        </div>
    </div>
</body>

</html>
