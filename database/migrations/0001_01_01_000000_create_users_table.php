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
        // 1. Tên bảng là 'user' 
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            
            $table->string('full_name'); // Khớp form đăng ký
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // --- PHẦN LIÊN KẾT KHÓA NGOẠI ---

            // 2. role_id -> Trỏ vào bảng 'roles' 
            $table->unsignedBigInteger('role_id')->default(1)->comment('1: Staff, 2: Admin...');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles') 
                ->onDelete('cascade');

            // 3. department_id -> Trỏ vào bảng 'departments' 
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id')
                ->references('id')
                ->on('departments') 
                ->onDelete('set null');

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
