<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;

// --- 1. AUTHENTICATION ---
// 1. Route MỞ form Đăng ký
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
// 2. Route LƯU dữ liệu Đăng ký
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// 3. Các route Login & Logout
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- 2. PUBLIC ROUTES ---
Route::get('/', [IdeaController::class, 'index'])->name('home');
Route::get('/ideas/{id}', [IdeaController::class, 'show'])->name('ideas.show');
Route::get('/terms', function () { return view('terms'); })->name('terms.index');
Route::get('/privacy', function () { return view('privacy'); })->name('privacy.index');


// --- 3. LOGGED IN USERS ---
Route::middleware(['auth'])->group(function () {
    // Nộp Idea
    Route::get('/create-idea', [IdeaController::class, 'create'])->name('ideas.create');
    Route::post('/create-idea', [IdeaController::class, 'store'])->name('ideas.store');

    // 1. Like
    Route::get('/idea/like/{id}/{type}', [InteractionController::class, 'like'])->name('idea.like');
    // 2. Comment
    Route::post('/ideas/{id}/comment', [InteractionController::class, 'comment'])->name('comments.store');
});


// --- 4. KHU VỰC QUẢN LÝ (Sprint 3) ---

// ==================== QA MANAGER ====================
Route::middleware(['auth', 'role:qam'])->prefix('qa-manager')->name('qam.')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tải file
    Route::get('/export-csv', [IdeaController::class, 'exportCsv'])->name('ideas.export');
    Route::get('/download-zip', [IdeaController::class, 'downloadZip'])->name('ideas.downloadZip');
    Route::get('/download-all-zip', [IdeaController::class, 'downloadZip'])->name('download.all');
    Route::get('/download-idea-zip/{id}', [IdeaController::class, 'downloadSingleZip'])->name('download.single');
    Route::get('/qa/download-zip/{year_id}', [IdeaController::class, 'downloadZipByYear'])->name('qa.download_zip_by_year');

    // Quản lý Deadline
    Route::get('/deadlines', [App\Http\Controllers\QAManagerController::class, 'deadlinesIndex'])->name('deadlines.index');
    Route::put('/deadlines/{id}', [App\Http\Controllers\QAManagerController::class, 'deadlinesUpdate'])->name('deadlines.update');
});


// ==================== ADMIN ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function() { return view('admin.dashboard'); })->name('dashboard');

    // Quản lý hệ thống cốt lõi bằng Route::resource (Đã bao gồm thêm, sửa, xóa)
    Route::resource('users', UserController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('academic-years', AcademicYearController::class);

    // Thống kê
    Route::get('/statistics', function () { return view('admin.statistics'); })->name('statistics');

    // Quản lý Idea của Admin (Đã đưa vào trong group bảo vệ)
    Route::get('/manage-ideas', [IdeaController::class, 'adminIndex'])->name('ideas.index');
    Route::delete('/delete-idea/{id}', [IdeaController::class, 'adminDestroy'])->name('ideas.destroy');
});


// ==================== QA COORDINATOR ====================
Route::middleware(['auth', 'role:coordinator'])
    ->prefix('qa-coordinator')
    ->as('coordinator.')
    ->group(function () {
        // Dashboard (Xem danh sách + Thống kê)
        Route::get('/dashboard', [CoordinatorController::class, 'index'])->name('dashboard');
        // Export CSV & ZIP
        Route::get('/export-csv', [CoordinatorController::class, 'exportCsv'])->name('export');
        Route::get('/download-zip', [CoordinatorController::class, 'downloadZip'])->name('download.zip');
    });


// ==================== RESET PASSWORD ====================
// 1. Form nhập email
Route::get('forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
// 2. Xử lý check email
Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
// 3. Form nhập mật khẩu mới
Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
// 4. Xử lý lưu mật khẩu mới
Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.update');


// ==================== STAFF PROFILE ====================
Route::middleware(['auth'])->group(function () {
    // Trang xem profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('staff.profile');
    // Cập nhật thông tin
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('staff.profile.update');
    // Cập nhật mật khẩu
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('staff.profile.password');

    // Chỉnh sửa Idea cá nhân
    Route::get('/ideas/{id}/edit', [IdeaController::class, 'edit'])->name('ideas.edit');
    Route::put('/ideas/{id}', [IdeaController::class, 'update'])->name('ideas.update');
});
