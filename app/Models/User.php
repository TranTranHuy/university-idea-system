<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    //Khai báo đúng tên bảng trong database
    protected $table = 'user';

    // 👇 THÊM DÒNG NÀY ĐỂ TẮT TÍNH NĂNG TỰ ĐỘNG LƯU THỜI GIAN
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role_id',
        'department_id',
        'is_agreed_terms'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function ideas()
    {
        return $this->hasMany(Idea::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class); // Một staff có nhiều bình luận
    }

    // KHU VỰC HÀM HELPER KIỂM TRA QUYỀN HẠN (ROLE)

    /**
     * Kiểm tra User có sở hữu role được truyền vào hay không.
     * Có thể truyền vào 1 chuỗi (VD: 'Admin') hoặc 1 mảng (VD: ['QA Coordinator', 'Staff'])
     */
    public function hasRole($roles)
    {
        // Kiểm tra xem User có role không, nếu không thì return false
        if (!$this->role) {
            return false;
        }

        // Nếu truyền vào một mảng
        if (is_array($roles)) {
            return in_array($this->role->role_name, $roles);
        }

        // Nếu truyền vào một chuỗi
        return $this->role->role_name === $roles;
    }

    // Các hàm viết tắt cho tiện gọi ở Controller và View (Blade)
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    public function isQAManager()
    {
        return $this->hasRole('QA Manager');
    }

    public function isQACoordinator()
    {
        return $this->hasRole('QA Coordinator');
    }

    public function isStaff()
    {
        return $this->hasRole('Staff');
    }
}
