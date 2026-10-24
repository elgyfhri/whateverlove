<?php

namespace App\Http\Controllers;

use App\Http\Controllers\InformationController;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AdminWebAppearanceController extends Controller
{
    public function contact()
    {
        return view('admin.web.contact', [
            'contact' => json_decode(SiteSetting::get('contact_content', json_encode([
                'email' => 'hello@whateverlove.id',
                'instagram' => 'https://instagram.com/whateverlove',
                'shopee' => 'https://shopee.co.id/whateverlove',
                'tokopedia' => 'https://www.tokopedia.com/whateverlove_shop',
                'bukalapak' => 'https://www.bukalapak.com/u/whateverlove',
            ])), true),
        ]);
    }

    public function updateContact(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'instagram' => ['required', 'url', 'max:500'],
            'shopee' => ['required', 'url', 'max:500'],
            'tokopedia' => ['required', 'url', 'max:500'],
            'bukalapak' => ['required', 'url', 'max:500'],
        ]);
        SiteSetting::put('contact_content', json_encode($data));
        return back()->with('success', 'Contact Us berhasil diupdate.');
    }

    public function information()
    {
        $saved = json_decode(SiteSetting::get('information_topics', '{}'), true);
        $defaults = InformationController::topics();
        $topics = [
            'how-to-order' => $saved['how-to-order'] ?? $defaults['how-to-order'],
            'faq' => $saved['faq'] ?? $defaults['faq'],
            'refund-return' => $saved['refund-return'] ?? $defaults['refund-return'],
        ];

        return view('admin.web.information', compact('topics'));
    }

    public function updateInformation(Request $request)
    {
        $data = $request->validate([
            'topics' => ['required', 'array'],
            'topics.*.description' => ['required', 'string', 'max:2000'],
            'topics.*.sections' => ['required', 'array'],
            'topics.*.sections.*.heading' => ['required', 'string', 'max:255'],
            'topics.*.sections.*.body' => ['required', 'string', 'max:5000'],
        ]);
        SiteSetting::put('information_topics', json_encode($data['topics']));
        return back()->with('success', 'Information berhasil diupdate.');
    }
}
