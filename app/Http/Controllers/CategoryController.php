<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
    $categories = Category::all();
    $academicYears = AcademicYear::latest()->get();
    return view('qamanager.categories', compact('categories', 'academicYears'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
    $request->validate(['name' => 'required|unique:categories']);
    Category::create($request->all());
    return back()->with('success', 'Category created!');
}

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(Category $category)
{
    return view('qamanager.categories_edit', compact('category'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
{
    $request->validate([
        'name' => 'required|unique:categories,name,' . $category->id
    ]);

    $category->update($request->all());

    return redirect()->route('qam.categories.index')->with('success', 'Category update successful!');
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Category $category) 
    {
        // --- 1. KIỂM TRA AN TOÀN ---
        // Đếm xem trong danh mục này có bài Idea nào không
        // Lưu ý: Cần đảm bảo Model Category đã có hàm ideas()
        if ($category->ideas()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục "' . $category->name . '" vì đã có sinh viên nộp bài!');
        }

        // --- 2. NẾU TRỐNG THÌ MỚI XÓA ---
        $category->delete();
        
        return back()->with('success', 'Xóa danh mục thành công!');
    }
}
