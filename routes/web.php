<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\Admin\AcademicYearController; // <--- Nhớ import cái này

// --- 1. AUTHENTICATION (Đăng ký/Đăng nhập) ---
Route::get('/register', function () { return view('register'); })->name('register');
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- 2. PUBLIC ROUTES (Ai cũng xem được) ---
Route::get('/', [IdeaController::class, 'index'])->name('home');
Route::get('/ideas/{id}', [IdeaController::class, 'show'])->name('ideas.show');

// --- 3. LOGGED IN USERS (Phải đăng nhập mới làm được) ---
Route::middleware(['auth'])->group(function () {
    // Nộp Idea
    Route::get('/create-idea', [IdeaController::class, 'create'])->name('ideas.create');
    Route::post('/create-idea', [IdeaController::class, 'store'])->name('ideas.store');
    
    // Tương tác (Like/Comment)
    Route::post('/ideas/{id}/like/{type}', [InteractionController::class, 'like'])->name('ideas.like');
    Route::post('/ideas/{id}/comment', [InteractionController::class, 'comment'])->name('ideas.comment');
});

// --- 4. KHU VỰC CẤM (Phân quyền Admin/QAM) ---

/**
 * GROUP 1: Dành cho QA MANAGER
 * Nhiệm vụ: Quản lý Category, Download CSV
 */
Route::middleware(['auth', 'role:qam'])->prefix('qa-manager')->name('qam.')->group(function () {
    // Quản lý Categories
    Route::resource('categories', CategoryController::class);
    // (Lệnh resource ở trên tự tạo ra index, store, update, destroy... cho gọn code)

    // Export CSV (Route bạn mới thêm logic lúc nãy)
    Route::get('/export-csv', [IdeaController::class, 'exportCsv'])->name('ideas.export');
});

/**
 * GROUP 2: Dành cho ADMIN
 * Nhiệm vụ: Quản lý User, Deadline (Academic Year)
 */
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', function() { return view('admin.dashboard'); })->name('dashboard');
    
    // Quản lý Idea (Xóa bài vi phạm)
    Route::get('/manage-ideas', [IdeaController::class, 'adminIndex'])->name('ideas.index');
    Route::delete('/delete-idea/{id}', [IdeaController::class, 'adminDestroy'])->name('ideas.destroy');

    // Quản lý Deadline (Academic Year) - Sprint 3
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::delete('/academic-years/{id}', [AcademicYearController::class, 'destroy'])->name('academic-years.delete');
});