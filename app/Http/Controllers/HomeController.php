<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::storefront()->latest()->take(8)->get();
        $categories = Category::orderBy('name')->get();
        $sections = HomeSection::orderBy('sort_order')->orderBy('id')->get();
        $heroImage = SiteSetting::get('hero_image', 'assets/hero.jpg');
        $aboutText = SiteSetting::get('about_text', 'WHATEVERLOVE represents a space where we can express our creativity in total freedom, doing what we love, without having to worry about all the rules of the fashion industry.');

        return view('home', compact('featured', 'categories', 'sections', 'heroImage', 'aboutText'));
    }
}