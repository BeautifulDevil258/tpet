<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(to right, #f06, #00bfff);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-family: 'Arial', sans-serif;
    }

    .form-container {
        background: rgba(255, 255, 255, 0.9);
        color: #333;
        padding: 3rem;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 450px;
        margin-top: 50px;  /* Add top margin */
        margin-bottom: 50px; /* Add bottom margin */
    }

    .form-label {
        font-size: 1rem;
        font-weight: 500;
    }

    .form-control {
        border-radius: 8px;
        padding-right: 2.5rem;
        font-size: 1rem;
    }

    .form-control:focus {
        box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
    }

    .input-group-text {
        background: transparent;
        border: none;
        color: #6a11cb;
    }

    .password-container {
        position: relative;
    }

    .password-container i {
        cursor: pointer;
    }

    .input-group-append {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .btn-primary {
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 1.1rem;
        background-color: #6a11cb;
        border: none;
    }

    .btn-primary:hover {
        background-color: #5c09b8;
    }

    .register-link {
        color: #00bfff;
        text-decoration: none;
    }

    .register-link:hover {
        text-decoration: underline;
    }

    .error-message {
        font-size: 0.875rem;
        color: #dc3545;
    }

    .text-danger {
        font-size: 0.875rem;
    }

    .input-group .input-group-text {
        border-radius: 8px;
    }

    .input-group .form-control {
        border-radius: 8px;
    }
    </style>
</head>

<body>
    <div class="form-container">
        <h2 class="text-center mb-4">Đăng Ký</h2>
        <form action="{{ route('register') }}" method="POST" id="registerForm" autocomplete="off">
            @csrf

            <!-- Full Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <div class="input-group">
                    <div class="input-group-text"><i class="fas fa-user"></i></div>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Nhập họ và tên">
                </div>
                @if ($errors->has('name'))
                <div class="error-message">
                    {{ $errors->first('name') }}
                </div>
                @endif
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <div class="input-group-text"><i class="fas fa-envelope"></i></div>
                    <input type="text" name="email" id="email" class="form-control" placeholder="Nhập email" autocomplete="email">
                </div>
                @if ($errors->has('email'))
                <div class="error-message">
                    {{ $errors->first('email') }}
                </div>
                @endif
            </div>

            <!-- Password -->
            <div class="mb-3 password-container">
                <label for="password" class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <div class="input-group-text"><i class="fas fa-lock"></i></div>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Nhập mật khẩu" autocomplete="new-password">
                    <div class="input-group-append">
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>
                @if ($errors->has('password'))
                <div class="error-message">
                    {{ $errors->first('password') }}
                </div>
                @endif
            </div>

            <!-- Confirm Password -->
            <div class="mb-3 password-container">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                <div class="input-group">
                    <div class="input-group-text"><i class="fas fa-lock"></i></div>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Xác nhận mật khẩu" autocomplete="new-password">
                    <div class="input-group-append">
                        <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>
                    </div>
                </div>
                @if ($errors->has('password_confirmation'))
                <div class="error-message">
                    {{ $errors->first('password_confirmation') }}
                </div>
                @endif
            </div>

            <!-- Phone Number -->
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <div class="input-group">
                    <div class="input-group-text"><i class="fas fa-phone"></i></div>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Nhập số điện thoại">
                </div>
                @if ($errors->has('phone'))
                <div class="error-message">
                    {{ $errors->first('phone') }}
                </div>
                @endif
            </div>

            <!-- Birth Date -->
            <div class="mb-3">
                <label for="birth_date" class="form-label">Ngày sinh</label>
                <div class="input-group">
                    <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                    <input type="date" name="birth_date" id="birth_date" class="form-control">
                </div>
            </div>

            <!-- Gender -->
            <div class="mb-3">
                <label for="gender" class="form-label">Giới tính</label>
                <div class="input-group">
                    <div class="input-group-text"><i class="fas fa-venus-mars"></i></div>
                    <select name="gender" id="gender" class="form-control">
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Đăng Ký</button>
        </form>

        <div class="text-center mt-3">
            <small>Đã có tài khoản? <a href="{{ route('login') }}" class="register-link">Đăng nhập ngay</a></small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');

    togglePassword.addEventListener('click', () => {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
    });

    toggleConfirmPassword.addEventListener('click', () => {
        const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordField.setAttribute('type', type);
        toggleConfirmPassword.classList.toggle('fa-eye');
        toggleConfirmPassword.classList.toggle('fa-eye-slash');
    });
    </script>
</body>

</html>
