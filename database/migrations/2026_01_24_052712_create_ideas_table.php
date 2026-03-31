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
    Schema::create('ideas', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        $table->string('document')->nullable();
        $table->boolean('is_anonymous')->default(false);
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('category_id');
        $table->timestamps();

        // THÊM CÁC DÒNG NÀY ĐỂ KẾT NỐI DATABASE
        $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ideas');
    }
};
