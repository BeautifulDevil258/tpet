<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TPet')</title>
    <link href="https://fonts.googleapis.com/css2?family=Coiny&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/profile.css">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        overflow-y: scroll;
    }

    .card {
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-10px);
    }

    .toast-success {
        background-color: #28a745;
        color: white;
    }

    .toast-error {
        background-color: #dc3545;
        color: white;
    }

    .navbar-brand img {
        border-radius: 50%;
        border: 2px solid #28a745;
    }

    .navbar {
        background-color: #343a40;
        align-items: center;
        margin: 0;
        /* Đảm bảo không có margin thừa */
        padding: 10px;
        /* Đảm bảo không có padding thừa */
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
        /* Đảm bảo navbar chiếm toàn bộ chiều rộng */
    }

    .container {
        width: 100%;
        padding-left: 0;
        padding-right: 0;
    }

    .navbar-nav .nav-link {
        font-weight: bold;
        color: #fff;
        padding: 8px 25px;
        /* Giãn ra thêm một chút */
    }

    .navbar-nav .nav-link:hover {
        background-color: #28a745;
    }

    .navbar-nav .nav-item {
        margin: 0 15px;
        /* Tăng khoảng cách giữa các mục */
    }

    .navbar-nav {
        width: 100%;
        justify-content: center;
        align-items: center;
        /* Căn giữa các phần tử trong navbar */
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 10px 0;
        text-align: center;
    }

    .search-input {
        border-radius: 5px;
        padding: 5px;
    }

    .search-button {
        border-radius: 5px;
        padding: 5px 10px;
        background-color: #28a745;
        border: none;
        color: white;
    }

    .search-form {
        margin-left: auto;
        margin-right: 10px;
    }

    .navbar-nav .nav-item.dropdown {
        margin-left: 15px;
    }

    /* Đảm bảo footer luôn ở dưới cùng */
    .content-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        /* Đảm bảo chiều cao là 100% chiều cao của cửa sổ trình duyệt */
    }

    main {
        flex-grow: 1;
        /* Cho phép main chiếm phần không gian còn lại */
    }

    /* Phong cách cho nút mở chatbot */
    #openChatbot {
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 50%;
        padding: 20px;
        font-size: 18px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    /* Hiệu ứng hover khi rê chuột vào nút */
    #openChatbot:hover {
        transform: scale(1.1);
        /* Phóng to nút */
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
        /* Tăng hiệu ứng bóng */
    }

    /* Phong cách cho logo TBot */
    .chatbot-logo {
        font-size: 18px;
        font-weight: bold;
        color: white;
        font-family: 'Arial', sans-serif;
        transition: all 0.3s ease;
    }

    /* Hiệu ứng chữ khi rê chuột */
    #openChatbot:hover .chatbot-logo {
        color: #f1f1f1;
        /* Làm sáng màu chữ khi hover */
    }

    /* Phong cách cho tin nhắn của người dùng */
    .user-message {
        background-color: #d1f7d1;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 10px;
        max-width: 80%;
        align-self: flex-end;
        /* Tin nhắn của người dùng căn phải */
    }

    /* Phong cách cho tin nhắn của bot */
    .chatbot-response {
        background-color: #f1f1f1;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 10px;
        max-width: 80%;
        align-self: flex-start;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Đảm bảo các tin nhắn của người dùng và bot có khoảng cách rõ ràng */
    #chatbot-body {
        padding: 10px;
        max-height: 350px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        border: 1px solid #ddd;
        /* Viền nhẹ quanh phần chat */
    }

    /* Thiết kế giao diện chatbot đẹp */
    .chatbot-container {
        display: flex;
        flex-direction: column;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .chatbot-header {
        background-color: #28a745;
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .chatbot-footer {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        background-color: #ffffff;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    /* Khi hover vào các tin nhắn */
    .chatbot-response:hover,
    .user-message:hover {
        background-color: #e9ecef;
    }

    /* Mess chat */
    /* Định kiểu cho nút Facebook Messenger */
    #openFacebookMessenger {
        margin: 10px;
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #0084ff;
        color: white;
        font-size: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    /* Hiệu ứng hover khi rê chuột vào nút */
    #openFacebookMessenger:hover {
        transform: scale(1.1);
        /* Phóng to nút */
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
        /* Tăng hiệu ứng bóng */
    }

    /* Thêm biểu tượng Messenger vào nút */
    #openFacebookMessenger i {
        font-size: 24px;
        /* Kích thước biểu tượng */
    }
    </style>
</head>
    <div class="content-wrapper">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="/home">
                    <img src="/images/logo.png" alt="TPet" width="65" height="65" class="me-2">
                    <span class="fw-bold text-success">TPet</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="/home"><i class="fas fa-home"></i> Trang Chủ</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="/product"><i class="fas fa-store"></i> Cửa
                                Hàng</a></li>
                        <li class="nav-item"><a class="nav-link" href="/about"><i class="fas fa-info-circle"></i> Về
                                Chúng Tôi</a></li>
                        <li class="nav-item"><a class="nav-link" href="/contact"><i class="fas fa-phone-alt"></i> Liên
                                Hệ</a></li>
                        <li class="nav-item">
                            <form class="search-form d-flex" action="{{ route('product.search') }}" method="GET">
                                <input type="text" name="query" class="form-control me-2 search-input"
                                    placeholder="Tìm kiếm...">
                                <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
                            </form>
                        </li>
                        @guest
                        <li class="nav-item"><a class="nav-link" href="/login"><i class="fas fa-sign-in-alt"></i> Đăng
                                Nhập</a>
                            <a class="nav-link" href="/register"><i class="fas fa-user-plus"></i> Đăng
                                Ký</a>
                        </li>
                        @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Avatar"
                                    width="30" height="30" class="rounded-circle">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/profile"><i class="fas fa-user"></i> Hồ Sơ</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i>
                                            Đăng Xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container mt-4">
            @yield('content')
            <!-- Button to open Facebook Messenger -->
            <button id="openFacebookMessenger" class="btn btn-facebook-messenger"
                style="position: fixed; bottom: 80px; right: 20px; border-radius: 50%; padding: 15px; font-size: 24px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); transition: all 0.3s ease; background-color: #0084ff; color: white; border: none; cursor: pointer;">
                <i class="fab fa-facebook-messenger"></i>
            </button>

   <!-- Chatbot Widget -->
   <div id="chatbot" class="chatbot-container"
            style="position: fixed; bottom: 20px; right: 20px; width: 350px; display: none; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);">
            <!-- Chatbot Header -->
            <div class="chatbot-header"
                style="background-color: #28a745; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <div class="chatbot-logo"
                    style="width: 60px; height: 60px; background-color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: #28a745; font-weight: bold;">
                    TPet
                </div>
                <strong>TBot</strong>
                <button id="closeChatbot"
                    style="background: none; border: none; color: white; font-size: 20px;">&times;</button>
            </div>

            <div id="chatbot-body" class="chatbot-body"
                style="border: 1px solid #28a745; padding: 15px; max-height: 350px; overflow-y: auto; background-color: #f1f1f1; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                <!-- Nội dung trò chuyện sẽ được hiển thị ở đây -->
            </div>
            <div class="chatbot-footer"
                style="display: flex; justify-content: space-between; padding: 10px; background-color: #f9f9f9; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                <input type="text" id="userMessage" class="chatbot-input" placeholder="Nhập câu hỏi..."
                    style="width: 80%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                <button id="sendMessage"
                    style="width: 15%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">Gửi</button>
            </div>
        </div>

        <!-- Button to open chatbot with stylish logo -->
        <button id="openChatbot" class="btn btn-chatbot"
            style="position: fixed; bottom: 20px; right: 20px; border-radius: 50%; padding: 15px; font-size: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); transition: all 0.3s ease;">
            <span class="chatbot-logo">TBot</span>
        </button>
        </main>
        <!-- Toast Notifications -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
            @if(session('success'))
            <div id="toastSuccess" class="toast toast-success show" role="alert">
                <div class="toast-header">
                    <strong class="me-auto">Thành công!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
            @elseif(session('error'))
            <div id="toastError" class="toast toast-error show" role="alert">
                <div class="toast-header">
                    <strong class="me-auto">Lỗi!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    {{ session('error') }}
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <footer>
            <p>&copy; 2024 TPet - Bản Quyền Thuộc Về Chúng Tôi</p>
        </footer>
    </div>

    <script>
    window.onload = function() {
        const successToast = document.getElementById('toastSuccess');
        const errorToast = document.getElementById('toastError');

        if (successToast) {
            setTimeout(() => new bootstrap.Toast(successToast).hide(), 3000);
        }

        if (errorToast) {
            setTimeout(() => new bootstrap.Toast(errorToast).hide(), 3000);
        }
    }
    </script>
    <!-- Mess chat -->
    <script>
    document.getElementById('openFacebookMessenger').addEventListener('click', function() {
        // Mở Messenger của trang Facebook
        window.open('https://m.me/503285079541633', '_blank');
    });
    </script>
    <!-- JavaScript -->
    <script>
    // Mở chatbot và tự động gửi tin nhắn "Chào bạn tôi có thể giúp gì cho bạn"
    document.getElementById('openChatbot').addEventListener('click', function() {
        document.getElementById('chatbot').style.display = 'block';
        document.getElementById('openChatbot').style.display = 'none';

        // Gửi tin nhắn tự động khi mở chatbot
        setTimeout(() => {
            sendBotMessage("Chào bạn, tôi có thể giúp gì cho bạn?");
        }, 500);
    });

    // Hàm gửi tin nhắn từ từ
    function sendBotMessage(message) {
        const chatbotResponseElement = document.createElement('div');
        chatbotResponseElement.classList.add('chatbot-response');

        let index = 0;
        chatbotResponseElement.innerHTML = 'TBot: '; // Thêm tên bot vào trước tin nhắn

        // Đẩy tin nhắn từ từ
        const interval = setInterval(() => {
            chatbotResponseElement.innerHTML += message.charAt(index);
            index++;

            // Khi tin nhắn đã được hiển thị xong, dừng interval
            if (index === message.length) {
                clearInterval(interval);

                // Thêm tin nhắn vào chat
                document.getElementById('chatbot-body').appendChild(chatbotResponseElement);
                document.getElementById('chatbot-body').scrollTop = document.getElementById('chatbot-body')
                    .scrollHeight;
            }
        }, 100); // Điều chỉnh tốc độ hiển thị ký tự (100ms cho mỗi ký tự)
    }
    document.getElementById('closeChatbot').addEventListener('click', function() {
        document.getElementById('chatbot').style.display = 'none';
        document.getElementById('openChatbot').style.display = 'block';

        // Xóa các tin nhắn cũ khi đóng chatbot
        document.getElementById('chatbot-body').innerHTML = '';
    });

    document.getElementById('sendMessage').addEventListener('click', sendMessage);

    document.getElementById('userMessage').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Ngăn không cho Enter tạo dòng mới
            sendMessage();
        }
    });

    function sendMessage() {
        const userMessage = document.getElementById('userMessage').value;

        if (userMessage.trim() === '') {
            alert('Vui lòng nhập câu hỏi.');
            return;
        }

        // Hiển thị tin nhắn của người dùng
        const userMessageElement = document.createElement('div');
        userMessageElement.classList.add('user-message');
        userMessageElement.innerHTML = 'Bạn: ' + userMessage.replace(/\n/g, '<br>'); // Thay thế \n bằng <br>
        document.getElementById('chatbot-body').appendChild(userMessageElement);

        // Gửi câu hỏi đến API chatbot
        fetch('/chatbot/respond', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    query: userMessage
                })
            })
            .then(response => response.json())
            .then(data => {
                sendBotMessage(data.response);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi.');
            });

        // Xóa input
        document.getElementById('userMessage').value = '';
    }

    function sendBotMessage(message) {
        const chatbotResponseElement = document.createElement('div');
        chatbotResponseElement.classList.add('chatbot-response');
        chatbotResponseElement.innerHTML = 'TBot: ' + message.replace(/\n/g, '<br>'); // Thay thế \n bằng <br>
        document.getElementById('chatbot-body').appendChild(chatbotResponseElement);

        // Cuộn xuống cuối cùng để hiển thị tin nhắn mới
        document.getElementById('chatbot-body').scrollTop = document.getElementById('chatbot-body').scrollHeight;
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/pr.js"></script>
    </body>

</html>