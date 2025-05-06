<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TPet')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Coiny&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/profile.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/user/product.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<div class="content-wrapper">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <div class="d-flex w-100 justify-content-between align-items-center">
                <!-- Logo -->
                <a class="navbar-brand d-flex align-items-center" href="/">
                    <img src="/images/logo.png" alt="TPet" width="65" height="65" class="me-2">
                    <span class="fw-bold text-success">TPet</span>
                </a>

                <div class="search w-100">
                    <form id="search" class="d-flex my-3 my-lg-0 mx-lg-3" action="{{ route('product.search') }}"
                        method="GET">
                        <input type="text" name="query" value="{{ request()->input('query') }}"
                            class="form-control shadow-none me-2" placeholder="Tìm kiếm...">
                        <button type="submit" class="btn btn-outline-success"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="d-flex align-items-center position-relative">
                    <button id="searchToggle" class="btn btn-outline-light me-2 d-lg-none">
                        <i class="bi bi-search"></i>
                    </button>
                    <a id="cart" class="w-90 d-flex align-items-center position-relative" href="/cart">
                        <i class="fas fa-shopping-cart fa-2x text-white"></i>
                        <span id="cart-count"
                            class="badge bg-danger position-absolute top-0 end-0 translate-middle p-1">
                            {{ auth()->check() && auth()->user()->cart ? auth()->user()->cart->cartItems->count() : 0 }}
                        </span>
                    </a>

                    <button class="navbar-toggler ms-3" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav w-100 justify-content-center mt-3 mt-lg-0">
                    <li class="nav-item"><a id="home" class="nav-link" href="/"><i class="fas fa-house"></i> Trang
                            Chủ</a></li>
                    <li class="nav-item dropdown custom-dropdown">
                        <!-- Khi người dùng nhấn vào "Cửa hàng", sẽ chuyển tới route 'product.index' -->
                        <a id="shop" class="nav-link dropdown-toggle" href="{{ route('product.index') }}">
                            <i class="fas fa-shopping-bag"></i> Cửa hàng
                        </a>

                        <ul class="dropdown-menu">
                            @foreach(\App\Models\LargeCategory::with('smallCategories')->get() as $largeCategory)
                            <li class="dropdown-submenu">
                                <a class="dropdown-item"
                                    href="{{ route('product.index', ['large_category_id' => $largeCategory->id]) }}">
                                    {{ $largeCategory->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach($largeCategory->smallCategories as $smallCategory)
                                    <!-- Kiểm tra nếu danh mục nhỏ có sản phẩm -->
                                    @if($smallCategory->products->count() > 0)
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('product.index', ['small_category_id' => $smallCategory->id]) }}">
                                            {{ $smallCategory->name }}
                                        </a>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item"><a id="posts" class="nav-link" href="/post"><i class="fas fa-blog"></i> Bài
                            Viết</a></li>
                    <li class="nav-item"><a id="order" class="nav-link" href="/orders"><i class="fas fa-receipt"></i>
                            Đơn mua</a></li>
                    <li class="nav-item"><a class="nav-link" href="/game"><i class="fas fa-gift"></i> Khuyến mại</a>
                    </li>
                    @guest
                    <li class="nav-item"><a class="nav-link" href="/login"><i class="fas fa-sign-in-alt"></i> Đăng
                            Nhập</a></li>
                    @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <img src="{{Auth::user()->profile_picture ? asset('storage/public/avatars/' . basename(Auth::user()->profile_picture)) : '/images/avt.jpg'}}"
                                id="preview-avatar" alt="Avatar" width="30" height="30" class="rounded-circle">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/profile"><i class="fas fa-user-circle"></i> Hồ Sơ</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('addresses.index') }}"><i
                                        class="bi bi-geo-alt-fill"></i>Địa chỉ</a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Đăng
                                        Xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Overlay để làm mờ background -->
    <div class="overlay" id="overlay"></div>
    <!-- Mobile Menu -->
    <div class="search-bar d-none">
        <div class="mobile-search">
            <form class="mobile-search-form d-flex" action="{{ route('product.search') }}" method="GET"
                onsubmit="return validateSearch()">
                <input type="text" name="queries" class="me-2 search-input" placeholder="Tìm kiếm...">
                <button type="submit" class="mobile-search-button"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>


    <div class="mobile-menu-bottom d-lg-none">
        <a href="/"><i class="fas fa-home"></i> Trang Chủ</a>
        <a href="/product"><i class="fas fa-store"></i> Sản Phẩm</a>
        <a href="/orders"><i class="fas fa-file-alt"></i> Đơn Hàng</a>
    </div>

    <!-- Main Content -->
    <main class="container mt-4">
        @yield('content')
        <!-- Button to open Facebook Messenger -->
        <button id="openFacebookMessenger" class="btn btn-facebook-messenger">
            <i class="fab fa-facebook-messenger"></i>
        </button>

        <!-- Chatbot Widget -->
        <div id="chatbot" class="chatbot-container">
            <!-- Chatbot Header -->
            <div class="chatbot-header">
                <div class="chatbot-logo">
                    🐾 TPet
                </div>
                <strong>TBot - Trợ lý thú cưng</strong>
                <button id="closeChatbot" aria-label="Close">❌</button>
            </div>

            <!-- Nội dung trò chuyện -->
            <div id="chatbot-body" class="chatbot-body"></div>

            <!-- Ô nhập tin nhắn -->
            <div class="chatbot-footer">
                <input type="text" id="userMessage" class="chatbot-input" placeholder="Nhập câu hỏi...">
                <button id="sendMessage">📩</button>
            </div>
        </div>

        <!-- Button mở chatbot -->
        <button id="openChatbot" class="btn-chatbot">
            🐶
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
</div>
 <!-- Footer -->
 <footer style="background-color: #f1f1f1; color: #333;">
    <div class="footer-wrapper container">
        <div class="footer-grid row">
            <!-- Giới thiệu về cửa hàng vật nuôi -->
            <div class="footer-about col-md-8">
                <h5>Giới thiệu về TPet</h5>
                <p>TPet là cửa hàng trực tuyến chuyên cung cấp các sản phẩm chất lượng dành cho vật nuôi. Chúng tôi
                    cung cấp thức ăn, đồ chơi, đồ dùng, phụ kiện và các sản phẩm chăm sóc sức khỏe cho thú cưng, với
                    cam kết chất lượng cao và giá cả hợp lý.</p>
            </div>
            <!-- Liên hệ -->
            <div class="footer-contact col-md-4">
                <h5>Liên hệ</h5>
                <p><strong>Email:</strong> thanhtrungnguyen523@gmail.com</p>
                <p><strong>Địa chỉ:</strong> 123 Đường ABC, Quận XYZ, Thành phố Hà Nội</p>
                <p><strong>Số điện thoại:</strong> 0385770872</p>
                <!-- Mạng xã hội -->
                <div class="d-flex align-items-center">
                    <h5 class="mb-0">Theo dõi chúng tôi:</h5>
                    <a href="https://www.facebook.com/tpet" target="_blank" class="text-decoration-none ms-3">
                        <i class="fab fa-facebook-f" style="font-size: 1.5rem; color: #3b5998;"></i>
                    </a>
                    <a href="https://zalo.me/0123456789" target="_blank" class="text-decoration-none ms-3">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Icon_of_Zalo.svg" alt="Zalo"
                             style="width: 30px; height: 30px;">
                    </a>
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <p>&copy; 2025 TPet</p>
        </div>
    </div>
</footer>

<script>
let lastScrollTop = 0;
const menu = document.querySelector('.mobile-menu-bottom');

window.addEventListener('scroll', function() {
    let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

    if (currentScroll > lastScrollTop) {
        // Scrolling down
        menu.classList.add('hide');
    } else {
        // Scrolling up
        menu.classList.remove('hide');
    }

    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Prevent negative scroll value
});
// Lắng nghe sự kiện khi nhấn vào nút toggle
document.querySelector('.navbar-toggler').addEventListener('click', function() {
    document.getElementById('navbarNav').classList.toggle('show');
    document.getElementById('overlay').classList.toggle('show');
});

// Lắng nghe sự kiện khi click vào nút đóng
document.getElementById('closeMenu').addEventListener('click', function() {
    document.getElementById('navbarNav').classList.remove('show');
    document.getElementById('overlay').classList.remove('show');
});

// Lắng nghe sự kiện khi click vào overlay để đóng menu
document.getElementById('overlay').addEventListener('click', function() {
    document.getElementById('navbarNav').classList.remove('show');
    document.getElementById('overlay').classList.remove('show');
});
</script>

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
<!-- Xử lý khi ấn tìm kiếm -->
<script>
document.querySelector('.search-form').addEventListener('submit', function(event) {
    var query = document.querySelector('input[name="query"]').value.trim();
    if (!query) {
        event.preventDefault(); // Ngừng việc gửi form nếu ô tìm kiếm trống
    }
});
document.querySelector('.mobile-search-form').addEventListener('submit', function(event) {
    var query = document.querySelector('input[name="queries"]').value.trim();
    if (!query) {
        event.preventDefault(); // Ngừng việc gửi form nếu ô tìm kiếm trống
    }
});
</script>
<script>
// Lắng nghe sự kiện khi ấn vào nút tìm kiếm (kính lúp)
document.getElementById('searchToggle').addEventListener('click', function() {
    const searchBar = document.querySelector('.search-bar');
    searchBar.classList.toggle('d-none'); // Hiển thị/ẩn thanh tìm kiếm
});
</script>
<!-- Mess chat -->
<script>
document.getElementById('openFacebookMessenger').addEventListener('click', function() {
    // Mở Messenger của trang Facebook
    window.open('https://m.me/541632572365835', '_blank');
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
    chatbotResponseElement.innerHTML = 'TBot: ';

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
    userMessageElement.innerHTML = 'Bạn: ' + userMessage.replace(/\n/g, '<br>');
    document.getElementById('chatbot-body').appendChild(userMessageElement);
    scrollToBottom();

    // Hiển thị "TBot đang gõ..."
    const typingIndicator = document.createElement('div');
    typingIndicator.classList.add('chatbot-response', 'typing-indicator');
    typingIndicator.id = 'typing-indicator';
    typingIndicator.innerHTML = 'TBot: <span class="dot"></span><span class="dot"></span><span class="dot"></span>';
    document.getElementById('chatbot-body').appendChild(typingIndicator);
    scrollToBottom();

    // Gửi câu hỏi đến OpenRouter
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
            // Xóa dòng "TBot đang gõ..."
            const typing = document.getElementById('typing-indicator');
            if (typing) typing.remove();

            const botResponse = data.choices[0]?.message?.content || 'Xin lỗi, tôi không hiểu.';
            sendBotMessage(botResponse);
        })
        .catch(error => {
            console.error('Lỗi:', error);
            alert('Đã xảy ra lỗi khi kết nối OpenRouter.');
        });

    // Xóa input
    document.getElementById('userMessage').value = '';
}

function sendBotMessage(message) {
    const chatbotResponseElement = document.createElement('div');
    chatbotResponseElement.classList.add('chatbot-response');
    chatbotResponseElement.innerHTML = 'TBot: ' + message.replace(/\n/g, '<br>'); // Thay thế \n bằng <br>
    document.getElementById('chatbot-body').appendChild(chatbotResponseElement);
    scrollToBottom();

    // Cuộn xuống cuối cùng để hiển thị tin nhắn mới
    document.getElementById('chatbot-body').scrollTop = document.getElementById('chatbot-body').scrollHeight;
}

function scrollToBottom() {
    setTimeout(() => {
        const chatBody = document.getElementById('chatbot-body');
        chatBody.scrollTop = chatBody.scrollHeight;
    }, 100); // Đợi một chút để nội dung được render
}
</script>
<script>
document.getElementById('cart').addEventListener('click', function() {
    fetch('/cart/count') // Gọi API lấy số lượng giỏ hàng
        .then(response => response.json())
        .then(data => {
            document.getElementById('cart-count').textContent = data.cartCount;
        });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            let customerId = this.getAttribute('data-id');
            let form = this.closest('.delete-form');

            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa?',
                text: "Hành động này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/pr.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

</body>

</html>