<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
{
    // Trỏ tới file index.blade.php nằm trong thư mục resources/views/admin/departments/
    return view('admin.departments.index');
}
}
