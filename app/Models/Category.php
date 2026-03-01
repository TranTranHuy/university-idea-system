<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description'];

    // Khai báo rõ tên bảng
    protected $table = 'categories';

    // 👇 THÊM HÀM NÀY VÀO ĐỂ KHAI BÁO MỐI QUAN HỆ VỚI IDEAS 👇
    public function ideas()
    {
        // Một danh mục có nhiều ý tưởng
        return $this->hasMany(Idea::class, 'category_id');
    }
}
