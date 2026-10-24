<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;

class ContactController extends Controller
{
    public function index()
    {
        $contact = json_decode(SiteSetting::get('contact_content', '{}'), true);

        return view('contact', compact('contact'));
    }
}