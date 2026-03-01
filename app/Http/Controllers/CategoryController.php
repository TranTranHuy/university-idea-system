<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Idea;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $ideas = Idea::with('user')->latest()->paginate(10);

        // Lấy danh sách Năm học gửi ra View để hiển thị Dropdown tải file ZIP
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('qamanager.categories', compact('categories', 'ideas', 'academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name'
        ]);

        Category::create($request->all());
        return back()->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        return view('qamanager.categories_edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id
        ]);

        $category->update($request->all());
        return redirect()->route('qam.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        // --- RÀNG BUỘC XÓA CATEGORY ---
        if ($category->ideas()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục "' . $category->name . '" vì đã có ý tưởng sử dụng danh mục này!');
        }

        $category->delete();
        return back()->with('success', 'Xóa danh mục thành công!');
    }
}
