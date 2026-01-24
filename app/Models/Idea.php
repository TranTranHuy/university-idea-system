<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idea extends Model
{
    protected $fillable = ['title', 'content', 'category_id', 'user_id', 'is_anonymous', 'document'];

}
