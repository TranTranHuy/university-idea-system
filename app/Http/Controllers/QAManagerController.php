<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QAManagerController extends Controller
{
    public function index()
{
    // Lấy danh sách Category cho phần sidebar
    $categories = \App\Models\Category::all();

    // Lấy danh sách Idea để QA Manager có thể tải ZIP
    $ideas = \App\Models\Idea::with('user')->latest()->paginate(10);

    return view('qam.dashboard', compact('categories', 'ideas'));
}
}
