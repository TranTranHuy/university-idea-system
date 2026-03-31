<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('likes', function (Blueprint $table) {
        $table->id();
        // Phải có dòng này để liên kết với bảng ideas
        $table->foreignId('idea_id')->constrained('ideas')->onDelete('cascade');
        // Và dòng này để liên kết với bảng user
        $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
