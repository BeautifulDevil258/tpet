<?php
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckInCheckOutController;
use App\Http\Controllers\CheckInCheckOutReportController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportHistoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\VoucherGameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

// Route đăng ký
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/upload-image', [ProductController::class, 'uploadImage']);
Route::get('checkout/retry-payment/{orderId}', [CheckoutController::class, 'retryPayment'])->name('checkout.retryPayment');

// Route đăng nhập
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
// Route cho quên mật khẩu
Route::get('/password/reset', [AuthController::class, 'showResetForm'])->name('password.request');                // Hiển thị form nhập email
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');            // Gửi email có liên kết
Route::get('/password/reset/{token}', [AuthController::class, 'showResetFormWithToken'])->name('password.reset'); // Hiển thị form đặt lại mật khẩu
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');                        // Xử lý cập nhật mật khẩu

// Route đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/chatbot/respond', function (Request $request) {
    $userMessage = $request->input('query');

    // Xử lý nếu người dùng hỏi về sản phẩm hoặc link sản phẩm
    if (preg_match('/(sản phẩm|mặt hàng|shop còn|bán gì|link sản phẩm|xem sản phẩm|danh sách sản phẩm)/i', $userMessage)) {
        $products = Product::limit(10)->get(); // Có thể phân trang hoặc lọc theo keyword trong tương lai

        if ($products->isEmpty()) {
            return response()->json([
                'choices' => [[
                    'message' => ['content' => 'Hiện tại chúng tôi chưa có sản phẩm nào trong hệ thống.']
                ]]
            ]);
        }

        $productList = $products->map(function ($product) {
            $url = url("/product/details/{$product->id}");
            return "-<a href='{$url}' target='_blank'>{$product->name}</a> - Giá: " . number_format($product->price, 0, ',', '.') . "₫";
        })->implode("<br>");
        $reply = "Dưới đây là một số sản phẩm hiện có, bạn có thể bấm vào tên để xem chi tiết:\n\n" . $productList;

        return response()->json([
            'choices' => [[
                'message' => ['content' => $reply]
            ]]
        ]);
    }

    // Trả về qua OpenRouter nếu không phải hỏi về sản phẩm
    $response = Http::withHeaders([
        'Content-Type'  => 'application/json',
        'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
    ])->post('https://openrouter.ai/api/v1/chat/completions', [
        'model'    => 'openai/gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'Bạn là trợ lý chatbot của TPet, chuyên tư vấn khách hàng về thú cưng.'],
            ['role' => 'user', 'content' => $userMessage],
        ],
        'max_tokens' => 1000,
    ]);

    return response()->json($response->json());
});
Route::get('/api/search-products', [ProductController::class, 'searchProducts']);
// Route chính
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product', [ProductController::class, 'showProducts'])->name('product.index');
Route::get('/product/details/{id}', [ProductController::class, 'details'])->name('product.details');
Route::get('/search', [ProductController::class, 'search'])->name('product.search');
Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
//route bai viet
Route::get('/post', [PostController::class, 'postindex'])->name('post.index');
Route::get('/post/{post}', [PostController::class, 'show'])->name('post.show');
// Các route yêu cầu đăng nhập
Route::middleware('auth')->group(function () {
// Route dành cho người dùng thông thường
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update');
        Route::post('/avatar/upload', [ProfileController::class, 'uploadAvatar'])->name('upload-avatar');
        Route::delete('/avatar/remove', [ProfileController::class, 'removeAvatar'])->name('remove-avatar');
        Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
    });
    //giỏ hàng
    Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/cart/update/{cartItemId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItemId}', [CartController::class, 'remove'])->name('cart.remove');
    // Route cho trang thanh toán
    // Route để hiển thị trang thanh toán
    Route::get('checkout', [CheckoutController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout/applyVoucher', [CheckoutController::class, 'applyVoucher'])->name('checkout.applyVoucher');

    Route::post('checkout', [CheckoutController::class, 'processPayment'])->name('checkout.process');
    Route::get('checkout/success', function () {
        return view('checkout.success');
    })->name('checkout.success');
    Route::get('vnpay-return', [CheckoutController::class, 'vnpayReturn'])->name('checkout.vnpay_return');

    Route::post('/checkout/update-address', [CheckoutController::class, 'updateAddress'])->name('checkout.updateAddress');
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::get('districts/{cityId}', [AddressController::class, 'getDistricts'])->name('districts.get');
    Route::get('wards/{districtId}', [AddressController::class, 'getWards'])->name('wards.get');
    //order
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('reorder/{order}', [OrderController::class, 'reOrder'])->name('reorder');
    //đánh giá
    Route::get('orders/{order}/reviews', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('orders/{order}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/get-voucher', [VoucherController::class, 'getRandomVoucher']);

    Route::get('/game', [VoucherGameController::class, 'index'])->name('game.index');
    Route::post('/game/claim', [VoucherGameController::class, 'claimVoucher'])->name('game.claim')->middleware('auth');

    Route::get('/spin-voucher', [VoucherController::class, 'spinForm'])->name('voucher.spin.form');
    Route::post('/spin-voucher', [VoucherController::class, 'spin'])->name('voucher.spin');
    Route::get('/vong-quay-voucher', [VoucherController::class, 'spinView'])->name('voucher.spin.view');

});
// Các route yêu cầu đăng nhập
Route::middleware('auth:admin')->group(function () {
    // Route dành cho admin

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/products', [AdminController::class, 'products'])->name('products');
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
        Route::get('/brands', [AdminController::class, 'brands'])->name('brands');
        Route::get('/revenue', [AdminController::class, 'revenue'])->name('revenue');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/order', [OrderController::class, 'adminIndex'])->name('order.index');
        Route::patch('/order/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::get('/order/{order}', [OrderController::class, 'adminShow'])->name('order.show');
        // Admin profile routes
        Route::prefix('profile')->name('adminprofile.')->group(function () {
            Route::get('/', [AdminProfileController::class, 'index'])->name('index');
            Route::post('/update', [AdminProfileController::class, 'updateProfile'])->name('update');
            Route::post('/avatar/upload', [AdminProfileController::class, 'uploadAvatar'])->name('upload-avatar');
            Route::delete('/avatar/remove', [AdminProfileController::class, 'removeAvatar'])->name('remove-avatar');
            Route::post('/update-password', [AdminProfileController::class, 'updatePassword'])->name('update-password');
        });
    });
    Route::resource('posts', PostController::class);
    Route::resource('article-categories', ArticleCategoryController::class);
    Route::resource('customers', CustomerController::class);
    Route::post('/admin/profile/avatar/upload', [AdminProfileController::class, 'uploadAvatar'])->name('admin.adminprofile.upload-avatar');
    // Danh mục lớn
    Route::get('/large-categories', [CategoryController::class, 'index'])->name('large_categories.index');
    Route::get('/large-category/{id}/edit', [CategoryController::class, 'edit'])->name('large_categories.edit');
    Route::put('/large-category/{id}', [CategoryController::class, 'update'])->name('large_categories.update');
    Route::delete('/large-category/{id}', [CategoryController::class, 'destroy'])->name('large_categories.destroy');
    Route::get('/large-category/create', [CategoryController::class, 'create'])->name('large_categories.create');
    Route::post('/large-category', [CategoryController::class, 'store'])->name('large_categories.store');

    Route::get('/small-categories', [CategoryController::class, 'smallCategoryIndex'])->name('small_categories.index');
    Route::get('/small-category/{id}/edit', [CategoryController::class, 'smallCategoryEdit'])->name('small_categories.edit');
    Route::put('/small-category/{id}', [CategoryController::class, 'smallCategoryUpdate'])->name('small_categories.update');
    Route::delete('/small-category/{id}', [CategoryController::class, 'smallCategoryDestroy'])->name('small_categories.destroy');
    // Route cho thêm danh mục nhỏ
    Route::get('/small-category/create', [CategoryController::class, 'smallCategoryCreate'])->name('small_categories.create');
    Route::post('/small-category', [CategoryController::class, 'smallCategoryStore'])->name('small_categories.store');
    Route::post('small-categories/import', [CategoryController::class, 'import'])->name('small_categories.import');
    // Routes cho trang quản lý sản phẩm
    Route::resource('products', ProductController::class);
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');

    // Route cho nhân viên
    Route::resource('employees', EmployeeController::class);

// Định nghĩa route cho check-in/check-out
    Route::get('/checkincheckout', [CheckInCheckOutController::class, 'index'])->name('checkincheckout.index');
    Route::get('/report', [CheckInCheckOutReportController::class, 'index'])->name('reports.checkin_checkout');

    Route::get('report/export-check', [CheckInCheckOutReportController::class, 'export'])->name('report.export');
    Route::get('statistics/export-statistic', [StatisticsController::class, 'exports'])->name('export.statistic');

    Route::prefix('checkincheckout')->group(function () {
        Route::post('checkincheckout/check-in/{employee_id}', [CheckInCheckOutController::class, 'checkIn'])->name('checkincheckout.checkIn');
        Route::post('checkincheckout/check-out/{employee_id}', [CheckInCheckOutController::class, 'checkOut'])->name('checkincheckout.checkOut');

        Route::post('/checkin-reason/{employeeId}', [CheckInCheckOutController::class, 'storeCheckInReason'])->name('checkincheckout.storeCheckInReason');
        Route::post('/checkout-reason/{employeeId}', [CheckInCheckOutController::class, 'storeCheckOutReason'])->name('checkincheckout.storeCheckOutReason');
    });
    Route::post('employees/{id}/check-in-late', [CheckInCheckOutController::class, 'checkInLate'])->name('employees.check-in-late');
    Route::post('employees/{id}/check-out', [CheckInCheckOutController::class, 'checkOut'])->name('employees.check-out');
    Route::post('employees/{id}/check-out-early', [CheckInCheckOutController::class, 'checkOutEarly'])->name('employees.check-out-early');
    Route::get('/admin/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/admin/import-history', [ImportHistoryController::class, 'index'])->name('import_history.index');
    Route::get('/export-excel', [StatisticsController::class, 'exportExcel']);
    Route::get('/admin/voucher', [VoucherController::class, 'index'])->name('vouchers.index');
    Route::get('/create', [VoucherController::class, 'create'])->name('vouchers.create');
    Route::post('/store', [VoucherController::class, 'store'])->name('vouchers.store');
    Route::get('/edit/{id}', [VoucherController::class, 'edit'])->name('vouchers.edit');
    Route::put('/update/{id}', [VoucherController::class, 'update'])->name('vouchers.update');
    Route::delete('/destroy/{id}', [VoucherController::class, 'destroy'])->name('vouchers.destroy');

});
