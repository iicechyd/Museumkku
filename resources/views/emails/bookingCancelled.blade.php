<!DOCTYPE html>
<html>
<head>
    <title>การจองเข้าชมพิพิธภัณฑ์ถูกยกเลิก</title>
</head>
<body>
    <h2>การจองเข้าชมพิพิธภัณฑ์ของคุณ<span style="color: red;">ถูกยกเลิกแล้ว</span></h2>
    <p>เรียน {{ $booking->visitor->visitorName }},</p>
    <p>ขอแจ้งให้ท่านทราบว่าการจองเข้าชมพิพิธภัณฑ์ของท่าน <span style="color: red;">ถูกยกเลิกแล้ว</span></p>
    <p>รายละเอียดการจองเข้าชมมีดังนี้</p>
    <p>วันที่จอง: {{ $booking->booking_date }}</p>
    <p>ประเภทการเข้าชม: {{ $booking->activity->activity_name }} </p>
    <p>ชื่อหน่วยงาน: {{ $booking->institute->instituteName }}</p>
    <p>ที่อยู่หน่วยงาน: {{$booking->institute->instituteAddress}} {{$booking->institute->subdistrict}} {{$booking->institute->district}} {{$booking->institute->province}} {{$booking->institute->zipcode}}</p>
    <p>หากมีข้อสงสัยใดๆ โปรดติดต่อเจ้าหน้าที่</p>
    <br>
    <p>ขอแสดงความนับถือ</p>
    <p>ศูนย์พิพิธภัณฑ์และแหล่งเรียนรู้ตลอดชีวิต มหาวิทยาลัยขอนแก่น</p>
    <p>หมายเลขโทรศัพท์ 06X-XXX-XXXX เจ้าหน้าที่ฝ่ายกิจกรรม</p>
</body>
</html>
