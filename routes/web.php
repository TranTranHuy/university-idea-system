<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InteractionController;

// --- 1. HIỂN THỊ FORM (Method GET) ---
// 👇 Đây là 2 dòng bạn bị thiếu 👇
Route::get('/register', function () { return view('register'); })->name('register');
Route::get('/login', function () { return view('login'); })->name('login');

// --- 2. XỬ LÝ DỮ LIỆU (Method POST) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- 3. CÁC TRANG KHÁC ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// Chỉ Admin mới vào được dashboard
Route::get('/admin/dashboard', function() {
    return "Chào mừng sếp Admin!";
})->middleware(['auth', 'role:admin']);

Route::get('/test-admin', function() {
    return "<h1>Chào sếp Admin! (Nếu thấy dòng này là vào được)</h1>";
})->middleware(['auth', 'role:admin']);

Route::middleware(['auth'])->group(function () {
    Route::get('/create-idea', [IdeaController::class, 'create'])->name('ideas.create');
    Route::post('/create-idea', [IdeaController::class, 'store'])->name('ideas.store');
});
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
});

Route::middleware(['auth'])->group(function () {
   
Route::post('/ideas/{id}/like/{type}', [InteractionController::class, 'like'])->name('ideas.like');
    Route::post('/ideas/{id}/comment', [InteractionController::class, 'comment'])->name('ideas.comment');
});
