<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        // Trỏ tới file index.blade.php nằm trong thư mục resources/views/admin/users/
        return view('admin.users.index');
    }
}
