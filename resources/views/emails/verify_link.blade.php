<!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ยืนยันตัวตน</title>
    </head>
    <body style="font-family: Noto Sans Thai, sans-serif; background-color: #f9f9f9; padding: 20px; text-align: center;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center">
                    <table width="400" style="background: #ffffff; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0px 2px 5px rgba(0,0,0,0.1);">
                        <tr>
                            <td>
                                <h2 style="margin: 10px 0; font-size: 24px; color: #000;">ยืนยันตัวตน</h2>
                                <p style="color: #555; font-size: 14px;">คลิกปุ่มด้านล่างเพื่อยืนยันอีเมล ปุ่มนี้จะหมดอายุใน 20 นาที</p>
                                <a href="{{ $url }}" style="display: inline-block; background-color: #736EFE; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-size: 16px; margin-top: 10px;">ยืนยัน</a>
                                <p style="margin-top: 20px; font-size: 14px; color: #555;">การยืนยันตัวตนนี้จะตรวจสอบอีเมล<br> <strong style="color: #736EFE;">{{ $email }}</strong></p>
                                <p style="font-size: 12px; color: #999;">หากคุณไม่ได้เป็นผู้ดำเนินการ กรุณาเพิกเฉยต่ออีเมลฉบับนี้ </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
</body>
</html>
