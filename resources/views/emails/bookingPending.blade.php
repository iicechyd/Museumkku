<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รออนุมัติการจอง</title>
</head>

<body>
    <h2>ระบบได้รับการจองของคุณเรียบร้อยแล้ว โดยสถานะปัจจุบันคือ<span style="color: rgb(228, 168, 3);">รออนุมัติการจอง</span></h2>
    <p>เรียนคุณ {{ $booking->visitor->visitorName }}</p>
    <p>ขอแจ้งให้ท่านทราบว่าการจองเข้าชมพิพิธภัณฑ์ของท่านได้รับการตอบรับการจองแล้ว</p>
    <p>รายละเอียดการจองเข้าชมมีดังนี้</p>
    <p>วันที่จอง: {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
    <p>ประเภทการเข้าชม: {{ $booking->activity->activity_name }} </p>
    <p>ชื่อหน่วยงาน: {{ $booking->institute->instituteName }}</p>
    <p>ที่อยู่หน่วยงาน: {{ $booking->institute->instituteAddress }} {{ $booking->institute->subdistrict }}
        {{ $booking->institute->district }} {{ $booking->institute->province }} {{ $booking->institute->zipcode }}</p>
    <p>อีเมลผู้ประสานงาน: {{ $booking->visitor->visitorEmail }}</p>
    <p>เบอร์โทรศัพท์: {{ $booking->visitor->tel }}</p>
    <p>เด็ก :
        {{ $booking->children_qty > 0 ? $booking->children_qty . ' คน' : '-' }}</p>
    <p>นร / นศ :
       {{ $booking->students_qty > 0 ? $booking->students_qty . ' คน' : '-' }}</p>
    <p>ผู้ใหญ่ / คุณครู :
        {{ $booking->adults_qty > 0 ? $booking->adults_qty . ' คน' : '-' }}</p>
    <p>ผู้พิการ :
        {{ $booking->disabled_qty > 0 ? $booking->disabled_qty . ' คน' : '-' }}</p>
    <p>ผู้สูงอายุ :
        {{ $booking->elderly_qty > 0 ? $booking->elderly_qty . ' คน' : '-' }}</p>
    <p>พระภิกษุสงฆ์ / สามเณร :
        {{ $booking->monk_qty > 0 ? $booking->monk_qty . ' รูป' : '-' }}</p>
    <p>ยอดรวมราคาทั้งหมด: {{ number_format($totalPrice, 2) }} บาท</p>
    <p>คุณสามารถแก้ไขข้อมูลการจองได้ที่ลิงก์ด้านล่าง:</p>
    <a href="{{ $editLink }}">แก้ไขข้อมูลการจอง</a>
    <p>หากต้องการยกเลิกการจอง คลิกที่นี่:</p>
    <a href="{{ $cancelLink }}" style="color: red;">
        ยกเลิกการจอง
    </a>
    <p>หากมีข้อสงสัยใดๆ โปรดติดต่อเจ้าหน้าที่</p>
    <br>
    <p>ขอแสดงความนับถือ</p>
    <p>ศูนย์พิพิธภัณฑ์และแหล่งเรียนรู้ตลอดชีวิต มหาวิทยาลัยขอนแก่น</p>
    <p>หมายเลขโทรศัพท์ 06X-XXX-XXXX เจ้าหน้าที่ฝ่ายกิจกรรม</p>
</body>

</html>
