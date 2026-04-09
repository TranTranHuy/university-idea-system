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
        Schema::table('ideas', function (Blueprint $table) {
            // Thêm cột view_count, kiểu số nguyên không âm, mặc định là 0
            $table->unsignedInteger('view_count')->default(0)->after('content');
            // Lưu ý: ->after('content') chỉ giúp sắp xếp cột cho đẹp trong DB,
            // bạn có thể đổi 'content' thành tên một cột nào đó đang có sẵn trong bảng ideas.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ideas', function (Blueprint $table) {
            // Xóa cột nếu lỡ chạy rollback
            $table->dropColumn('view_count');
        });
    }
};
