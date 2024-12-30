<!DOCTYPE html>
<html>
<head>
    <title>สถานะการจองเข้าชมพิพิธภัณฑ์</title>
</head>
<body>
    <h2>การจองของคุณได้รับการอนุมัติแล้ว</h2>
    <p>เรียน {{ $booking->visitor->visitorName }},</p>
    <p>การจองเข้าชมพิพิธภัณฑ์ได้รับการอนุมัติแล้ว</p>
    <p>รายละเอียดการจองเข้าชมมีดังนี้</p>
    <p>วันที่จอง: {{ $booking->booking_date }}</p>
    <p>ประเภทการเข้าชม: {{ $booking->activity->activity_name }} </p>
    <p>ชื่อหน่วยงาน: {{ $booking->institute->instituteName }}</p>
    <p>ที่อยู่หน่วยงาน: {{$booking->institute->instituteAddress}} {{$booking->institute->subdistrict}} {{$booking->institute->district}} {{$booking->institute->province}} {{$booking->institute->zipcode}}</p>
    <p>โปรดแนบเอกสารใบขอความอนุเคราะห์โดยคลิกที่ลิงก์ด้านล่าง:</p>
    <p><a href="{{ $uploadLink }}">คลิกที่นี่เพื่อแนบเอกสารขอความอนุเคราะห์</a></p>
    <p>หากมีข้อสงสัยใดๆ โปรดติดต่อเจ้าหน้าที่ได้ที่หมายเลขโทรศัพท์ 06X-XXX-XXXX</p>
    <p>ขอแสดงความนับถือ</p>
    <p>ศูนย์พิพิธภัณฑ์และแหล่งเรียนรู้ตลอดชีวิต มหาวิทยาลัยขอนแก่น</p>
</body>
</html>
