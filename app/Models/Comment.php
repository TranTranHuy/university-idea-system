<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
// Thêm dòng này để cho phép lưu user_id, idea_id và nội dung bình luận
    protected $fillable = [
        'user_id',
        'idea_id',
        'content',
        'is_anonymous'
    ];


    // Khai báo quan hệ với User (vì bảng của bạn tên là 'user' số ít)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}

