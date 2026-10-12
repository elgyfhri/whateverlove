<?php

namespace App\Http\Controllers;

use App\Models\Look;
use App\Models\LookImage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminLookController extends Controller
{
    public function index()
    {
        $looks = Look::withCount('products')->latest()->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.looks.index', compact('looks', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $slug = Str::slug($data['name']);

        while (Look::where('slug', $slug)->exists()) {
            $slug = Str::slug($data['name']) . '-' . rand(10, 99);
        }

        $look = [
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $look['image'] = $request->file('image')->store('looks', 'public');
        }

        $look = Look::create($look);
        $look->products()->sync($data['product_ids'] ?? []);
        $this->storeImages($request, $look);

        return redirect()->route('admin.looks.index')
            ->with('success', 'Look berhasil ditambahkan.');
    }

    public function edit(Look $look)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.looks.edit', compact('look', 'products'));
    }

    public function update(Request $request, Look $look)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:look_images,id'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($data['name']);

        while (Look::where('slug', $slug)->where('id', '!=', $look->id)->exists()) {
            $slug = Str::slug($data['name']) . '-' . rand(10, 99);
        }

        $update = [
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($look->image) {
                Storage::disk('public')->delete($look->image);
            }

            $update['image'] = $request->file('image')->store('looks', 'public');
        }

        $look->update($update);
        $look->products()->sync($data['product_ids'] ?? []);

        if ($request->has('remove_images')) {
            LookImage::whereIn('id', $request->input('remove_images'))->get()->each(function (LookImage $image) {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            });
        }

        $this->storeImages($request, $look);

        return redirect()->route('admin.looks.index')
            ->with('success', 'Look berhasil diupdate.');
    }

    public function destroy(Look $look)
    {
        if ($look->image) {
            Storage::disk('public')->delete($look->image);
        }

        foreach ($look->lookImages as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $look->delete();

        return redirect()->route('admin.looks.index')
            ->with('success', 'Look berhasil dihapus.');
    }

    private function storeImages(Request $request, Look $look): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

        $sort = $look->lookImages()->max('sort_order') ?? -1;

        foreach ($request->file('images') as $file) {
            $sort++;
            LookImage::create([
                'look_id' => $look->id,
                'image' => $file->store('looks', 'public'),
                'sort_order' => $sort,
            ]);
        }
    }
}