<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Bổ sung thư viện liên kết

class Comment extends Model
{
    // Cho phép lưu user_id, idea_id và nội dung bình luận
    protected $fillable = [
        'user_id',
        'idea_id',
        'content',
        'is_anonymous'
    ];


    // Khai báo quan hệ với User (vì bảng của bạn tên là 'user' số ít)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 👇 THÊM HÀM NÀY ĐỂ KHAI BÁO LIÊN KẾT VỚI BÀI VIẾT (IDEA) 👇
    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class, 'idea_id');
    }
}
