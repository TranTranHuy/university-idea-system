<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bảng user (Số ít theo logic của bạn)
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            // Đổi 'name' thành 'full_name' để khớp với Form đăng ký của bạn
            $table->string('full_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Bổ sung các cột mà Controller đang yêu cầu
            $table->unsignedBigInteger('role_id')->default(1)->comment('1: Staff, 2: Admin, ...');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->boolean('is_agreed_terms')->default(false);

            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Bảng password_reset_tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. Bảng sessions (Lưu ý: foreignId trỏ về bảng 'user')
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // Khai báo rõ ràng references tới bảng 'user' số ít
            $table->foreignId('user_id')->nullable()->index()->constrained('user')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sửa lại thành 'user' cho đúng với hàm up
        Schema::dropIfExists('user');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
