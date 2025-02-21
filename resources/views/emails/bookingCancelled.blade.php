<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>ยกเลิกการจอง</title>
</head>

<body style="font-family: Noto Sans Thai, sans-serif; background-color: #f9f9f9; padding: 20px; text-align: center;">
    <div class="container">
        <h2>การจองของท่านได้รับการ<span class="status" style="color: red;">ยกเลิกการจอง</span>
        </h2>
        <p>เรียน <span class="highlight"></span>{{ $booking->visitor->visitorName }}</p>
        <p>ขอแจ้งให้ท่านทราบว่าการจองเข้าชมพิพิธภัณฑ์ของท่านได้รับการยกเลิกการจองเป็นที่เรียบร้อย</p>
    </div>

    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table width="400"
                    style="background: #ffffff; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0px 2px 5px rgba(0,0,0,0.1);">
                    <tr>
                        <td>
                            <img src="cid:cancel_icon.png" alt="CancelIcon" width="50" height="50" style="margin-left: 2px;">
                            <h2 style="margin: 20px 0; font-size: 24px; color: red;">ยกเลิกการจอง</h2>
                            <a href="{{ $detailsLink }}"
                                style="color: red; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-size: 16px; border: 2px solid red; display: inline-block;">
                                รายละเอียดการจอง</a>
                            <p>หากมีข้อสงสัยใดๆ โปรดติดต่อเจ้าหน้าที่</p>
                            <img src="cid:phone_icon.png" alt="PhoneIcon">         
                            <span>094-278-4222, 0-4300-9700 ต่อ 45596</span>
                            <p style="color: #489085;">
                                <strong>ศูนย์พิพิธภัณฑ์<span style="color: #E6A732;">และแหล่งเรียนรู้ตลอดชีวิต</span>
                                    <br> <span style="color: #C06628;">มหาวิทยาลัยขอนแก่น</span></strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
