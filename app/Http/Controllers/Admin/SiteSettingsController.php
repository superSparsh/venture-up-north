<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiteSettingsController extends Controller
{
    /**
     * Display the main settings panel (with tabs like footer, general, etc.)
     */
    public function index()
    {
        $contact = json_decode(Setting::get('contact_info'));
        $social = json_decode(Setting::get('social_links'));
        $seo = json_decode(Setting::get('site_seo'));

        return Inertia::render('Admin/SiteSettings/AdminSettings', [
            'contact' => $contact,
            'social' => $social,
            'seo' => $seo,
        ]);
    }

    /**
     * Update Footer settings only
     */
    public function updateFooter(Request $request)
    {
        $validated = $request->validate([
            'contact.email' => 'nullable|email',
            'contact.phone' => 'nullable|string|max:50',
            'contact.address' => 'nullable|string|max:255',
            'social' => 'nullable|array',
            'social.*.platform' => 'required|string',
            'social.*.url' => 'required|url',
            'social.*.icon' => 'nullable|string',
        ]);

        Setting::set('contact_info', $validated['contact']);
        Setting::set('social_links', $validated['social']);

        return redirect()->back()->with('success', 'Footer settings updated');
    }

    public function updateSeo(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'image' => 'nullable|image|max:200|dimensions:min_width=1200,min_height=630',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('seo', 'public');
            $validated['image'] = '/storage/' . $path;
        } else {
            $validated['image'] = Setting::get('site_seo')->image ?? null;
        }

        Setting::set('site_seo', $validated);

        return redirect()->back()->with('success', 'SEO settings updated');
    }
}
