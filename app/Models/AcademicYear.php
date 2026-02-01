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
        'name', 
        'start_date', 
        'closure_date', 
        'final_closure_date'
    ];
}