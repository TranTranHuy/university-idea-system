<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// DÒNG QUAN TRỌNG NHẤT:
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Idea extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category_id',
        'user_id',
        'is_anonymous',
        'document'
    ];

    // Định nghĩa để lấy được tên người đăng
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Định nghĩa để lấy được tên danh mục
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Một ý tưởng có nhiều lượt thích
// Trong file Idea.php
public function likes(): HasMany {
    // Nếu bạn đặt tên cột khác chuẩn, hãy khai báo rõ ở tham số thứ 2
    return $this->hasMany(Like::class, 'idea_id');
}


// Một ý tưởng có nhiều bình luận
public function comments(): HasMany {
    return $this->hasMany(Comment::class);
}
// Kiểm tra xem user hiện tại đã like chưa
public function isLikedBy($user) {
    return $this->likes()->where('user_id', $user->id)->exists();
}
protected $casts = [
    'document' => 'array',
];

public function academicYear()
{
    // Liên kết với bảng academic_years qua khóa ngoại academic_year_id
    return $this->belongsTo(AcademicYear::class, 'academic_year_id');
}

}
