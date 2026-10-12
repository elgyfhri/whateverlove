<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index()
    {
        $articles = Article::withCount('products')->latest()->get();

        return view('admin.articles.index', compact('articles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ]);

        $slug = Str::slug($data['name']);

        while (Article::where('slug', $slug)->exists()) {
            $slug = Str::slug($data['name']) . '-' . rand(10, 99);
        }

        $article = [
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $article['image'] = $request->file('image')->store('articles', 'public');
        }

        Article::create($article);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($data['name']);

        while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
            $slug = Str::slug($data['name']) . '-' . rand(10, 99);
        }

        $update = [
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }

            $update['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($update);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diupdate.');
    }

    public function destroy(Article $article)
    {
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }
}