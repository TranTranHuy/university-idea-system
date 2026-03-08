<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Idea extends Model
{
    // Khai báo thêm thuộc tính có thể lưu vào DB
    protected $fillable = [
        'title', 'content', 'category_id', 'department_id', 'user_id', 'academic_year_id', 'is_anonymous', 'document'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // 👇 THÊM LIÊN KẾT ĐẾN DEPARTMENT 👇
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function likes(): HasMany {
        return $this->hasMany(Like::class, 'idea_id');
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    public function isLikedBy($user) {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    protected $casts = [
        'document' => 'array',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
