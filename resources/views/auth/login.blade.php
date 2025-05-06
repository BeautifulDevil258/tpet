<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(to right, #f06, #00bfff);
        /* Vibrant gradient background */
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-family: 'Arial', sans-serif;
    }

    .form-container {
        background: rgba(255, 255, 255, 0.8);
        /* Semi-transparent white background */
        color: #333;
        padding: 3rem;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        /* Enhanced shadow */
        width: 100%;
        max-width: 450px;
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

    .toggle-password {
        cursor: pointer;
    }

    .input-group-append {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .password-container {
        position: relative;
    }

    .password-container i {
        cursor: pointer;
    }

    .btn-primary {
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 1.1rem;
        background-color: #6a11cb;
        /* Match form accent color */
        border: none;
    }

    .btn-primary:hover {
        background-color: #5c09b8;
    }

    .register-link {
        color: #00bfff;
        /* Light blue accent for links */
        text-decoration: none;
    }

    .register-link:hover {
        text-decoration: underline;
    }

    .text-danger {
        font-size: 0.875rem;
        transition: background-color 0.3s, transform 0.3s;
    }

    .error-message {
        display: none;
    }

    /* Add some smooth transitions for interactive elements */
    .btn-primary,
    .input-group-text,
    .toggle-password {
        transition: background-color 0.3s, transform 0.3s;
    }

    .btn-primary:hover,
    .input-group-text:hover,
    .toggle-password:hover {
        transform: scale(1.05);
    }
    </style>
</head>

<body>
    <!-- Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if(session('success'))
        <div id="toastSuccess" class="toast toast-success show" role="alert">
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
        @elseif(session('error'))
        <div id="toastError" class="toast toast-error show" role="alert">
            <div class="toast-body">
                {{ session('error') }}
            </div>
        </div>
        @endif
    </div>
    <div class="form-container">
        <h2 class="text-center mb-4">Đăng Nhập</h2>
        <form action="{{ route('login') }}" method="POST" id="loginForm">
            @csrf
            <!-- Email Input -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="text" name="email" id="email" class="form-control" placeholder="Nhập email"
                        value="{{ old('email') }}">
                </div>
                @error('email')
                <div class="text-danger" style="font-size: 0.875rem; margin-top: 0.25rem;">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="mb-3 password-container">
                <label for="password" class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Nhập mật khẩu">
                    <div class="input-group-append">
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>
                @error('password')
                <div class="text-danger" style="font-size: 0.875rem; margin-top: 0.25rem;">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
        </form>
        <div class="text-center mt-3">
        <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
        </div>
        <!-- Register Link -->
        <div class="text-center mt-3">
            <small>Chưa có tài khoản? <a href="{{ route('register') }}" class="register-link">Đăng ký ngay</a></small>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.onload = function() {
        const successToast = document.getElementById('toastSuccess');
        const errorToast = document.getElementById('toastError');

        if (successToast) {
            setTimeout(() => {
                const toast = new bootstrap.Toast(successToast);
                toast.hide();
            }, 3000);
        }

        if (errorToast) {
            setTimeout(() => {
                const toast = new bootstrap.Toast(errorToast);
                toast.hide();
            }, 3000);
        }
    }
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Toggle the icon
        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
    });

    // Form validation
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    form.addEventListener('submit', function(event) {
        let valid = true;

        // Email validation
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(emailInput.value)) {
            emailError.style.display = 'block';
            valid = false;
        } else {
            emailError.style.display = 'none';
        }

        // Password validation
        if (passwordInput.value.trim() === '') {
            passwordError.style.display = 'block';
            valid = false;
        } else {
            passwordError.style.display = 'none';
        }

        // Prevent form submission if validation fails
        if (!valid) {
            event.preventDefault();
        }
    });
    </script>
</body>

</html>