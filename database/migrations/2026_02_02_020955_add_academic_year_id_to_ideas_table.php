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
        Schema::table('ideas', function (Blueprint $table) {
            // Thêm cột academic_year_id (Cho phép null để không lỗi dữ liệu cũ)
            // Khi xóa Năm học -> Set cột này về Null chứ không xóa Idea
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('ideas', function (Blueprint $table) {
            // Xóa khóa ngoại và cột khi rollback
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });
    }
};
