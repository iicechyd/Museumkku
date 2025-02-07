<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/bookingPending.css') }}">
    <title>Booking Confirmation</title>
</head>

<body>
    <div class="container">
        <h2>ระบบได้รับการจองของคุณเรียบร้อยแล้ว โดยสถานะปัจจุบันคือ <span class="status">รออนุมัติการจอง</span></h2>
        <p>เรียนคุณ <span class="highlight">{{ $booking->visitor->visitorName }}</span></p>
        <p>ขอแจ้งให้ท่านทราบว่าการจองเข้าชมพิพิธภัณฑ์ของคุณได้รับการตอบรับการจองแล้ว</p>
        <a href="{{ $detailsLink }}" 
     style="background-color: #3673cf; color: white; padding: 10px 20px; 
            text-decoration: none; border-radius: 5px; display: inline-block;">
    รายละเอียดการจอง
  </a>
    <p>คุณสามารถแก้ไขข้อมูลการจองได้ที่:</p>
    <a href="{{ $editLink }}" class="button">แก้ไขข้อมูลการจอง</a>
    <p>หากต้องการยกเลิกการจอง คลิกที่นี่:</p>
    <a href="{{ $cancelLink }}" class="button cancel">ยกเลิกการจอง</a>

    <p>หากมีข้อสงสัยใดๆ โปรดติดต่อเจ้าหน้าที่</p>
    <br>
    <p>ขอแสดงความนับถือ</p>
    <p><strong>ศูนย์พิพิธภัณฑ์และแหล่งเรียนรู้ตลอดชีวิต มหาวิทยาลัยขอนแก่น</strong></p>
    <p>หมายเลขโทรศัพท์ 06X-XXX-XXXX เจ้าหน้าที่ฝ่ายกิจกรรม</p>
</body>

</html>
