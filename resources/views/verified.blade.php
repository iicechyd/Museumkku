<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันตัวตนสำเร็จ</title>
    <link href="https://fonts.googleapis.com/css2?family=Maitree&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/verified.css') }}">
</head>
<body>
    <div class="container">
        <h1>✅ ยืนยันตัวตนสำเร็จ</h1>
        <p>{{ session('message', 'กรุณากลับไปที่หน้าการจองเข้าชมของคุณ') }}</p>
        <a href="/">หน้าหลัก</a>
    </div>
</body>
</html>