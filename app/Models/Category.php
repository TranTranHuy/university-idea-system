<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description'];
    protected $table = 'categories';
    public function ideas()
{
    // Một danh mục có nhiều ý tưởng
    return $this->hasMany(Idea::class);
}
}
