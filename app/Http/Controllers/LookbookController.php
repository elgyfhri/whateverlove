<?php

namespace App\Http\Controllers;

use App\Models\Look;

class LookbookController extends Controller
{
    public function index()
    {
        $looks = Look::with('lookImages')
            ->withCount('activeProducts')
            ->where('is_active', true)
            ->latest()
            ->paginate(8);

        return view('lookbook', compact('looks'));
    }

    public function show(Look $look)
    {
        abort_unless($look->is_active, 404);

        $look->load('lookImages');
        $products = $look->activeProducts()->latest()->get();

        return view('lookbook_show', compact('look', 'products'));
    }
}