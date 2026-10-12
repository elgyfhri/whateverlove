<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::storefront()->latest()->get();
        $categories = Category::orderBy('name')->get();

        return view('catalog', compact('products', 'categories'));
    }

    public function category(string $category)
    {
        if (!Category::where('slug', $category)->exists()) {
            abort(404);
        }

        $products = Product::storefront()
            ->where('category', $category)
            ->latest()
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('catalog', compact('products', 'category', 'categories'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active && $product->sizes()->exists(), 404);

        $related = Product::storefront()
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('product', compact('product', 'related'));
    }
}