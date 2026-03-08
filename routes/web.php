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
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProfileController;


// --- 1. AUTHENTICATION ---
Route::get('/register', function () { return view('register'); })->name('register');
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/register', [AuthController::class, 'register']);
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

    // --- KHU VỰC SỬA LỖI (Quan trọng) ---

    // 1. Like (Sửa thành GET và đổi tên thành 'idea.like' để khớp với thẻ <a> bên Frontend)
    // Lưu ý: Dùng GET để like là không chuẩn bảo mật, nhưng để chạy được code cũ của nhóm thì tạm chấp nhận.
    Route::get('/idea/like/{id}/{type}', [InteractionController::class, 'like'])->name('idea.like');

    // 2. Comment (Đổi tên thành 'comments.store' để khớp với Form bên Frontend)
    Route::post('/ideas/{id}/comment', [InteractionController::class, 'comment'])->name('comments.store');
});

// --- 4. KHU VỰC QUẢN LÝ (Sprint 3) ---

// QA MANAGER
Route::middleware(['auth', 'role:qam'])->prefix('qa-manager')->name('qam.')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::get('/export-csv', [IdeaController::class, 'exportCsv'])->name('ideas.export');
    Route::get('/download-zip', [IdeaController::class, 'downloadZip'])->name('ideas.downloadZip');
    Route::get('/qa/download-zip/{year_id}', [IdeaController::class, 'downloadZipByYear'])
    ->name('qa.download_zip_by_year');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/download-all-zip', [IdeaController::class, 'downloadZip'])->name('download.all');
Route::get('/download-idea-zip/{id}', [IdeaController::class, 'downloadSingleZip'])->name('download.single');
// 👇 THÊM DÒNG NÀY ĐỂ FIX LỖI TẢI ZIP THEO NĂM HỌC 👇
    Route::get('/qa/download-zip/{year_id}', [IdeaController::class, 'downloadZipByYear'])->name('qa.download_zip_by_year');
// 👇 2 DÒNG MỚI ĐỂ QUẢN LÝ DEADLINE 👇
    Route::get('/deadlines', [App\Http\Controllers\QAManagerController::class, 'deadlinesIndex'])->name('deadlines.index');
    Route::put('/deadlines/{id}', [App\Http\Controllers\QAManagerController::class, 'deadlinesUpdate'])->name('deadlines.update');
});


// ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function() { return view('admin.dashboard'); })->name('dashboard');

    // Quản lý Academic Year
    Route::resource('academic-years', AcademicYearController::class);

    // Tạm thời trỏ thẳng ra View để test giao diện
    Route::get('/statistics', function () {
        return view('admin.statistics');
    })->name('statistics'); // Group sẽ tự động ghép thành 'admin.statistics'
    Route::resource('users', UserController::class);
    Route::resource('departments', DepartmentController::class);
});

    // Quản lý Deadline
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::delete('/academic-years/{id}', [AcademicYearController::class, 'destroy'])->name('academic-years.delete');

    // Quản lý Idea
    Route::get('/manage-ideas', [IdeaController::class, 'adminIndex'])->name('ideas.index');
    Route::delete('/delete-idea/{id}', [IdeaController::class, 'adminDestroy'])->name('ideas.destroy');


// COORDINATOR

Route::middleware(['auth', 'role:coordinator']) // Check đúng middleware role
    ->prefix('qa-coordinator')
    ->as('coordinator.')
    ->group(function () {

        // Dashboard (Xem danh sách + Thống kê)
        Route::get('/dashboard', [CoordinatorController::class, 'index'])->name('dashboard');

        // Export CSV
        Route::get('/export-csv', [CoordinatorController::class, 'exportCsv'])->name('export');
        // Download ZIP
        Route::get('/download-zip', [CoordinatorController::class, 'downloadZip'])->name('download.zip');
    });

    //RESET PASSWORD
    // 1. Form nhập email
Route::get('forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
// 2. Xử lý check email (không gửi mail, chuyển thẳng sang form đặt pass mới)
Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');

// 3. Form nhập mật khẩu mới
Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
// 4. Xử lý lưu mật khẩu mới vào database
Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

//STAFF
Route::middleware(['auth'])->group(function () {
    // Trang xem profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('staff.profile');
    // Cập nhật thông tin
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('staff.profile.update'); 
    // Cập nhật mật khẩu
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('staff.profile.password');
    
    Route::get('/ideas/{id}/edit', [IdeaController::class, 'edit'])->name('ideas.edit');
    Route::put('/ideas/{id}', [IdeaController::class, 'update'])->name('ideas.update');
});
