<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('comments', function (Blueprint $table) {
        // Thêm cột is_anonymous kiểu boolean, mặc định là 0 (không ẩn danh)
        $table->boolean('is_anonymous')->default(false);
    });
}

public function down()
{
    Schema::table('comments', function (Blueprint $table) {
        $table->dropColumn('is_anonymous');
    });
}
};
