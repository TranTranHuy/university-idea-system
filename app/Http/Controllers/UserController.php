<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Trỏ tới file index.blade.php nằm trong thư mục resources/views/admin/users/
        return view('admin.users.index');
    }
}
