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
                    <span>094-512-9458, 094-278-4222</span>
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
                @if ($booking->tmss)
                    <p>รอบการเข้าชม
                        {{ \Carbon\Carbon::parse($booking->tmss->start_time)->format('H:i') }} น. -
                        {{ \Carbon\Carbon::parse($booking->tmss->end_time)->format('H:i') }} น.</p>
                    </p>
                @endif
                @if (!$booking->subActivities->isEmpty())
                    <p>หลักสูตร:
                        {{ $booking->subActivities->pluck('sub_activity_name')->implode(', ') }}
                    </p>
                @endif
                <p>ชื่อหน่วยงาน: {{ $booking->institute->instituteName }}</p>
                <p>ที่อยู่หน่วยงาน: {{ $booking->institute->instituteAddress }} {{ $booking->institute->subdistrict }}
                    {{ $booking->institute->district }} {{ $booking->institute->province }}
                    {{ $booking->institute->zipcode }}</p>
                @if (!empty($booking->note))
                    <p>*หมายเหตุ: {{ $booking->note }}</p>
                @endif
            </div>
            <div class="details booking-info">
                @if ($booking->status == 0)
                    <h1 style="color: #E6A732;">รออนุมัติการจอง</h1>
                @elseif ($booking->status == 1)
                    <h1 style="color: #489085;">อนุมัติการจอง</h1>
                @elseif ($booking->status == 2)
                    <h1 style="color: #2b5ff9;">
                        @if ($booking->activity->activity_type_id == 1)
                            เข้าชมพิพิธภัณฑ์
                        @elseif ($booking->activity->activity_type_id == 2)
                            เข้าร่วมกิจกรรม
                        @endif
                    </h1>
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
                <p>จองเมื่อ: {{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y') }}</p>
                <p>สถานะ: @if ($booking->status == 0)
                        <span>รออนุมัติการจอง</span>
                    @elseif ($booking->status == 1)
                        <span>อนุมัติการจอง</span>
                    @elseif ($booking->status == 2)
                        <span>
                            @if ($booking->activity->activity_type_id == 1)
                            เข้าชมพิพิธภัณฑ์
                            @elseif ($booking->activity->activity_type_id == 2)
                                เข้าร่วมกิจกรรม
                            @endif
                        </span>
                    @elseif ($booking->status == 3)
                        <span>ยกเลิกการจอง</span>
                    @endif
                </p>
            </div>
        </div>

        @php
            $prices = [
                'children_price' => $booking->activity->children_price,
                'student_price' => $booking->activity->student_price,
                'adult_price' => $booking->activity->adult_price,
                'kid_price' => $booking->activity->kid_price,
                'disabled_price' => $booking->activity->disabled_price,
                'elderly_price' => $booking->activity->elderly_price,
                'monk_price' => $booking->activity->monk_price,
            ];
        @endphp
        @php
            $visitorTypes = [
                'children_qty' => 'เด็ก ( 3 ขวบ - ประถม )',
                'students_qty' => 'มัธยม / นักศึกษา',
                'adults_qty' => 'ผู้ใหญ่ / คุณครู',
                'kid_qty' => 'เด็กเล็ก',
                'disabled_qty' => 'ผู้พิการ',
                'elderly_qty' => 'ผู้สูงอายุ',
                'monk_qty' => 'พระภิกษุสงฆ์ /สามเณร',
                'free_teachers_qty' => 'ครูเข้าชมฟรี',
            ];
        @endphp
        <table class="table-container">
            <tr>
                <th>ประเภทผู้เข้าชม</th>
                <th>จำนวนคน</th>
                <th>ราคา/คน</th>
                <th>จำนวนเงิน (THB)</th>
            </tr>

            @foreach ($quantities as $type => $qty)
                @if ($qty > 0 && $type !== 'free_teachers_qty')
                    @php
                        $priceMapping = [
                            'children_qty' => 'children_price',
                            'students_qty' => 'student_price',
                            'adults_qty' => 'adult_price',
                            'kid_qty' => 'kid_price',
                            'disabled_qty' => 'disabled_price',
                            'elderly_qty' => 'elderly_price',
                            'monk_qty' => 'monk_price',
                        ];

                        $priceKey = $priceMapping[$type] ?? null;
                    @endphp


                    @if ($priceKey && isset($prices[$priceKey]))
                        <tr>
                            <td>{{ $visitorTypes[$type] ?? $type }}</td>
                            <td>{{ $qty }} คน</td>
                            <td>{{ number_format($prices[$priceKey], 2) }} บาท</td>
                            <td>{{ number_format($qty * $prices[$priceKey], 2) }} บาท</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $visitorTypes[$type] ?? $type }}</td>
                            <td>{{ $qty }} คน</td>
                            <td>0.00 บาท</td>
                            <td>0.00 บาท</td>
                        </tr>
                    @endif
                @endif
            @endforeach

            @if ($quantities['free_teachers_qty'] > 0)
                <tr>
                    <td>คุณครูเข้าชมฟรี</td>
                    <td>{{ $quantities['free_teachers_qty'] }} คน</td>
                    <td>0.00 บาท</td>
                    <td>0.00 บาท</td>
                </tr>
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
