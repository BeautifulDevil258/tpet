<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class ArticleCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.article-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.article-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name|max:255'
        ]);

        Category::create([
            'name' => $request->name
        ]);

        return redirect()->route('article-categories.index')->with('success', 'Danh mục đã được thêm.');
    }

    public function edit(Category $category)
    {
        return view('admin.article-categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id . '|max:255'
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return redirect()->route('article-categories.index')->with('success', 'Danh mục đã được cập nhật.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('article-categories.index')->with('success', 'Danh mục đã bị xóa.');
    }
}
