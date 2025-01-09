<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminProfileController;

// Route đăng ký
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Route đăng nhập
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
// Route cho quên mật khẩu
Route::get('/password/reset', [AuthController::class, 'showResetForm'])->name('password.request'); // Hiển thị form nhập email
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email'); // Gửi email có liên kết
Route::get('/password/reset/{token}', [AuthController::class, 'showResetFormWithToken'])->name('password.reset'); // Hiển thị form đặt lại mật khẩu
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update'); // Xử lý cập nhật mật khẩu

// Route đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
use App\Http\Controllers\ChatbotController;

Route::post('/chatbot/respond', [ChatbotController::class, 'respond']);

Route::get('/api/search-products', [ProductController::class, 'searchProducts']);
// Route chính
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/product', [ProductController::class, 'showProducts'])->name('product.index');
Route::get('/search', [ProductController::class, 'search'])->name('product.search');
Route::get('/category/{id}', [ProductController::class, 'showProductsBySmallCategory'])->name('shop.category');
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
});
// Các route yêu cầu đăng nhập
Route::middleware('auth:admin')->group(function () {
    // Route dành cho admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/products', [AdminController::class, 'products'])->name('products');
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
        Route::get('/brands', [AdminController::class, 'brands'])->name('brands');
        Route::get('/employees', [AdminController::class, 'employees'])->name('employees');
        Route::get('/revenue', [AdminController::class, 'revenue'])->name('revenue');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        
        // Admin profile routes
        Route::prefix('profile')->name('adminprofile.')->group(function () {
            Route::get('/', [AdminProfileController::class, 'index'])->name('index');
            Route::post('/update', [AdminProfileController::class, 'updateProfile'])->name('update');
            Route::post('/avatar/upload', [AdminProfileController::class, 'uploadAvatar'])->name('upload-avatar');
            Route::delete('/avatar/remove', [AdminProfileController::class, 'removeAvatar'])->name('remove-avatar');
            Route::post('/update-password', [AdminProfileController::class, 'updatePassword'])->name('update-password');
        });
    });
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
     // Routes cho trang quản lý sản phẩm
     Route::resource('products', ProductController::class);
});