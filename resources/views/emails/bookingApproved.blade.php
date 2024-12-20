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
    <p>กิจกรรม: {{ $booking->activity->activity_name }} </p>
    <p>หากมีข้อสงสัยใดๆ โปรดติดต่อเจ้าหน้าที่ได้ที่หมายเลขโทรศัพท์ 06X-XXX-XXXX</p>
</body>
</html>
