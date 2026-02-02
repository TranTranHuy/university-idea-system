<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    // Nếu bảng trong DB đặt tên là 'academicyear' (viết liền không s)
    // thì phải khai báo dòng dưới, còn nếu là 'academic_years' thì không cần.
    protected $table = 'academic_years'; // Ví dụ: tên bảng chuẩn Laravel
    
    protected $fillable = [
        'name',                 // Ví dụ: Spring 2026
        'start_date',           // Ngày bắt đầu
        'closure_date',         // Deadline 1: Đóng nộp Idea
        'final_closure_date'    // Deadline 2: Đóng comment/like
    ];

    // Quan hệ: 1 Năm học có nhiều Idea
    public function ideas()
    {
        return $this->hasMany(Idea::class);
    }
}