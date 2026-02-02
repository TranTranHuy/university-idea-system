<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
    $categories = Category::all();
    return view('qamanager.categories', compact('categories'));
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
   public function destroy(Category $category) {
    $category->delete();
    return back()->with('success', 'Category deleted!');
}
}
