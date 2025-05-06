<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f06, #00bfff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .form-title {
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .form-label {
            font-size: 1rem;
            font-weight: 500;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px;
        }

        .btn-primary {
            border-radius: 8px;
            font-size: 1.1rem;
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            margin-top: 10px;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .text-danger {
            font-size: 0.875rem;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2 class="form-title">Đặt lại mật khẩu</h2>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Password Input -->
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu mới:</label>
                <input id="password" type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới"
                    required>
                @error('password')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password Input -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu:</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                    placeholder="Nhập lại mật khẩu mới" required>
                @error('password_confirmation')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
