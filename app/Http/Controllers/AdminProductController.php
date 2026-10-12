<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with('sizes')->withSum('sizes', 'stock')->latest()->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $articles = Article::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'articles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'integer', 'min:1', 'max:100000'],
            'category' => ['required', 'exists:categories,slug'],
            'article_id' => ['nullable', 'exists:articles,id'],
            'badge' => ['nullable', 'in:new,sold_out'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:3200'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:3200'],
            'sizes' => ['nullable', 'array'],
            'sizes.*.size' => ['nullable', 'string', 'max:32'],
            'sizes.*.custom' => ['nullable', 'string', 'max:32'],
            'sizes.*.stock' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        if (!$this->hasValidSizes($request)) {
            return back()->withErrors(['sizes' => 'Produk wajib memiliki minimal satu ukuran beserta stoknya.'])
                ->withInput();
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        $this->syncSizes($product, $request->input('sizes', []));

        $this->storeGalleryImages($product, $request->file('images', []));

        if (!$product->image && $product->productImages()->exists()) {
            $product->update(['image' => $product->productImages()->first()->image]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $articles = Article::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'articles'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'integer', 'min:1', 'max:100000'],
            'category' => ['required', 'exists:categories,slug'],
            'article_id' => ['nullable', 'exists:articles,id'],
            'badge' => ['nullable', 'in:new,sold_out'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:3200'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:3200'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:product_images,id'],
            'is_active' => ['nullable', 'boolean'],
            'sizes' => ['nullable', 'array'],
            'sizes.*.id' => ['nullable', 'integer'],
            'sizes.*.size' => ['nullable', 'string', 'max:32'],
            'sizes.*.custom' => ['nullable', 'string', 'max:32'],
            'sizes.*.stock' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $this->removeGalleryImages($product, $request->input('remove_images', []));

        $this->storeGalleryImages($product, $request->file('images', []));

        if (!$product->image && $product->productImages()->exists()) {
            $data['image'] = $product->productImages()->first()->image;
        }

        $data['is_active'] = $request->boolean('is_active');

        if (!$this->hasValidSizes($request)) {
            return back()->withErrors(['sizes' => 'Produk wajib memiliki minimal satu ukuran beserta stoknya.'])
                ->withInput();
        }

        $product->update($data);

        $this->syncSizes($product, $request->input('sizes', []));

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diupdate.');
    }

    protected function storeGalleryImages(Product $product, array $files): void
    {
        $sortOrder = $product->productImages()->count();

        foreach ($files as $file) {
            $product->productImages()->create([
                'image' => $file->store('products', 'public'),
                'sort_order' => $sortOrder++,
            ]);
        }
    }

    protected function removeGalleryImages(Product $product, array $ids): void
    {
        $images = $product->productImages()
            ->whereIn('id', $ids)
            ->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image);
            $image->delete();
        }
    }

    protected function hasValidSizes(Request $request): bool
    {
        return collect($request->input('sizes', []))
            ->contains(function ($row) {
                $standard = trim((string) ($row['size'] ?? ''));
                $custom = trim((string) ($row['custom'] ?? ''));

                return $standard !== '' || $custom !== '';
            });
    }

    protected function syncSizes(Product $product, array $sizes): void
    {
        $submittedIds = [];

        foreach ($sizes as $row) {
            $standard = trim((string) ($row['size'] ?? ''));
            $custom = trim((string) ($row['custom'] ?? ''));

            $sizeName = $custom !== '' ? $custom : $standard;

            $stock = max(0, (int) ($row['stock'] ?? 0));

            if (!empty($row['id']) && ($existing = ProductSize::find((int) $row['id']))) {
                if ($existing->product_id !== $product->id) {
                    continue;
                }

                if ($sizeName === '') {
                    $existing->delete();
                    continue;
                }

                $conflict = ProductSize::where('product_id', $product->id)
                    ->where('size', $sizeName)
                    ->where('id', '!=', $existing->id)
                    ->first();

                if ($conflict) {
                    $conflict->update(['stock' => $stock]);
                    $existing->delete();
                    $submittedIds[] = $conflict->id;
                    continue;
                }

                $submittedIds[] = $existing->id;
                $existing->update(['size' => $sizeName, 'stock' => $stock]);

                continue;
            }

            if ($sizeName === '') {
                continue;
            }

            ProductSize::updateOrCreate(
                ['product_id' => $product->id, 'size' => $sizeName],
                ['stock' => $stock]
            );

            $submittedIds[] = ProductSize::where('product_id', $product->id)
                ->where('size', $sizeName)
                ->value('id');
        }

        ProductSize::where('product_id', $product->id)
            ->whereNotIn('id', $submittedIds)
            ->delete();
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        foreach ($product->productImages as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}