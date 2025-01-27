<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รออนุมัติการจอง</title>
</head>
<body>
    <h1>รออนุมัติการจอง</h1>
    <p>เรียนคุณ {{ $booking->visitor->visitorName }}</p>
    <p>ระบบได้รับการจองของคุณเรียบร้อยแล้ว โดยสถานะปัจจุบันคือ "รออนุมัติการจอง"</p>
    <p>รายละเอียดการจองเข้าชมมีดังนี้</p>
    <p>วันที่จอง: {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
    <p>กิจกรรม: {{ $booking->activity->activity_name }} </p>
    <p>คุณสามารถแก้ไขข้อมูลการจองได้ที่ลิงก์ด้านล่าง:</p>
    <a href="{{ $editLink }}">แก้ไขข้อมูลการจอง</a>
    <p>ขอบคุณสำหรับการจอง!</p>
</body>
</html>
