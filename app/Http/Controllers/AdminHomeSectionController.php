<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminHomeSectionController extends Controller
{
    private const DEFAULTS = [
        'contact_us' => ['title' => 'Contact Us', 'link_text' => 'Learn More', 'link' => '/contact'],
        'our_stores' => ['title' => 'Our Stores', 'link_text' => 'View More', 'link' => '/catalog'],
        'our_services' => ['title' => 'Our Services', 'link_text' => 'View More', 'link' => '/#about'],
    ];

    public function index()
    {
        $this->ensureDefaults();

        $sections = HomeSection::orderBy('sort_order')->orderBy('id')->get();
        $heroImage = SiteSetting::get('hero_image', 'assets/hero.jpg');
        $aboutText = SiteSetting::get('about_text', 'WHATEVERLOVE represents a space where we can express our creativity in total freedom, doing what we love, without having to worry about all the rules of the fashion industry.');

        return view('admin.home-sections.index', compact('sections', 'heroImage', 'aboutText'));
    }

    public function updateHomepage(Request $request)
    {
        $data = $request->validate([
            'about_text' => ['required', 'string', 'max:5000'],
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:4096'],
        ]);

        SiteSetting::put('about_text', $data['about_text']);

        if ($request->hasFile('hero_image')) {
            $old = SiteSetting::get('hero_image');
            if ($old && ! str_starts_with($old, 'assets/')) {
                Storage::disk('public')->delete($old);
            }
            SiteSetting::put('hero_image', $request->file('hero_image')->store('homepage', 'public'));
        }

        return redirect()->route('admin.home-sections.index')->with('success', 'Homepage berhasil diupdate.');
    }

    public function update(Request $request, HomeSection $section)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'link_text' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ]);

        $update = [
            'title' => $data['title'],
            'link_text' => $data['link_text'] ?? null,
            'link' => $data['link'] ?? null,
        ];

        if ($request->hasFile('image')) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }

            $update['image'] = $request->file('image')->store('home-sections', 'public');
        }

        if ($request->boolean('remove_image')) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }

            $update['image'] = null;
        }

        $section->update($update);

        return redirect()->route('admin.home-sections.index')
            ->with('success', 'Home section berhasil diupdate.');
    }

    private function ensureDefaults(): void
    {
        foreach (self::DEFAULTS as $key => $defaults) {
            if (!HomeSection::where('key', $key)->exists()) {
                HomeSection::create($defaults + ['key' => $key]);
            }
        }
    }
}