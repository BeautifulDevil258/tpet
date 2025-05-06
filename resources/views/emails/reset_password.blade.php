<!DOCTYPE html>
<html>
<head>
    <title>Đặt lại mật khẩu</title>
</head>
<body>
    <h1>Xin chào!</h1>
    <p>Nhấp vào liên kết dưới đây để đặt lại mật khẩu của bạn:</p>
    <a href="{{ url('password/reset/'.$token) }}">Đặt lại mật khẩu</a>
    <p>Nếu bạn không yêu cầu đặt lại mật khẩu, bạn có thể bỏ qua email này.</p>
</body>
</html>
