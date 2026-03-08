<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Thêm thư viện khai báo quan hệ

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = ['department_name'];

    // Quan hệ: 1 Department có nhiều User
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // 👇 THÊM MỚI: Quan hệ: 1 Department có nhiều Ý tưởng (Ideas) 👇
    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class, 'department_id');
    }
}
