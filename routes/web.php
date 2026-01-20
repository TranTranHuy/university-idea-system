<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
// Chỉ Admin mới vào được dashboard
Route::get('/admin/dashboard', function() {
    return "Chào mừng sếp Admin!";
})->middleware(['auth', 'role:admin']);

Route::get('/test-admin', function() {
    return "<h1>Chào sếp Admin! (Nếu thấy dòng này là vào được)</h1>";
})->middleware(['auth', 'role:admin']);