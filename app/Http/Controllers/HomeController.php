<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::storefront()->latest()->take(8)->get();
        $categories = Category::orderBy('name')->get();
        $sections = HomeSection::orderBy('sort_order')->orderBy('id')->get();

        return view('home', compact('featured', 'categories', 'sections'));
    }
}