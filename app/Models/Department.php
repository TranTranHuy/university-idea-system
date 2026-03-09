<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = ['department_name'];

    // Quan hệ: 1 Department có nhiều User
    public function users()
    {
        return $this->hasMany(User::class);
    }
    // 2. Lấy tất cả Idea của Khoa đó
    public function ideas()
    {
        // Department -> có nhiều User -> mỗi User có nhiều Idea
        return $this->hasManyThrough(Idea::class, User::class);
    }
}
