<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SitePopup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SitePopupController extends Controller
{
    public function edit()
    {
        $popups = SitePopup::single();
        return Inertia::render('Admin/Popups/Edit', [
            'events_content'   => $popups->events_content,
            'magazine_content' => $popups->magazine_content,
        ]);
    }
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'events_content' => 'required|array',
            'events_content.blocks' => 'required_with:events_content|array|min:1',
            'magazine_content' => 'required|array',
            'magazine_content.blocks' => 'required_with:magazine_content|array|min:1',
        ]);
    
        $popups = SitePopup::single();
        $popups->update($validated);
    
        return back()->with('success', 'Popups updated successfully.');
    }
}
