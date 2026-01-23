<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;

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
