<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $posts = Post::when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $imagePath = $request->file('image') ? $request->file('image')->store('posts', 'public') : null;

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được tạo.');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
            $post->update(['image' => $imagePath]);
        }

        $post->update($request->except('image'));

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được cập nhật.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Bài viết đã được xóa.');
    }
    public function postindex(Request $request)
    {
        $categories = Category::all();
        $categoryId = $request->input('category_id');
    
        $posts = Post::when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(6);
    
        return view('post.index', compact('posts', 'categories', 'categoryId'));
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);

        // Lấy danh sách bài viết liên quan cùng danh mục (trừ bài viết hiện tại)
        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->latest()
            ->limit(4)
            ->get();

        return view('post.show', compact('post', 'relatedPosts'));
    }
}