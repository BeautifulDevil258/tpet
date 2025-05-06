<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo: Đổi Mật Khẩu Thành Công</title>
</head>
<body>
    <p>Xin chào {{ $account->name }},</p>

    <p>
        Mật khẩu của bạn đã được thay đổi thành công.
        <br>
        Loại tài khoản: {{ $accountType }}
    </p>

    <p>Nếu bạn không thực hiện thay đổi này, vui lòng liên hệ với quản trị viên ngay lập tức.</p>

    <p>Trân trọng,<br>Đội ngũ hỗ trợ</p>
</body>
</html>
