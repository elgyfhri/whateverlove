<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->latest()->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ]);

        $slug = \Illuminate\Support\Str::slug($data['name']);

        while (Category::where('slug', $slug)->exists()) {
            $slug = Str::slug($data['name']) . '-' . rand(10, 99);
        }

        $category = [
            'name' => $data['name'],
            'slug' => $slug,
        ];

        if ($request->hasFile('image')) {
            $category['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($category);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ]);

        $slug = Str::slug($data['name']);

        while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = Str::slug($data['name']) . '-' . rand(10, 99);
        }

        if ($category->slug !== $slug) {
            Product::where('category', $category->slug)
                ->update(['category' => $slug]);
        }

        $update = ['name' => $data['name'], 'slug' => $slug];

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $update['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($update);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy(Category $category)
    {
        if (Product::where('category', $category->slug)->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}