<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Idea; // Đã thêm: Để truy xuất dữ liệu Idea
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Hiển thị danh sách Category và Ideas cho QA Manager
     */
    public function index()
    {
        // Lấy tất cả danh mục
        $categories = Category::all();

        // Lấy danh sách Ideas kèm thông tin người dùng (Author) để hiển thị ở bảng phía dưới
        // Phân trang 10 items/trang để giao diện gọn gàng
        $ideas = Idea::with('user')->latest()->paginate(10);

        // Truyền cả 2 biến sang View để fix lỗi 'Undefined variable $ideas'
        return view('qamanager.categories', compact('categories', 'ideas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name'
        ]);

        Category::create($request->all());

        return back()->with('success', 'Category created successfully!');
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

        return redirect()->route('qam.categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Có thể thêm kiểm tra nếu Category đang có Idea thì không cho xóa
        $category->delete();

        return back()->with('success', 'Category deleted successfully!');
    }

    // Các hàm không dùng đến có thể để trống hoặc xóa bớt cho gọn
    public function create() {}
    public function show(Category $category) {}
}
