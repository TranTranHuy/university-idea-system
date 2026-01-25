<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    // Thêm dòng này để cho phép lưu user_id và idea_id
    protected $fillable = ['user_id', 'idea_id'];

    // Đừng quên khai báo quan hệ nếu sau này cần dùng
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function idea() {
        return $this->belongsTo(Idea::class);
    }
}
