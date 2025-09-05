<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeSectionController extends Controller
{
    public function index()
    {
        $sections = HomeSection::orderBy('order')->paginate(10);
        return Inertia::render('Admin/HomeSections/Index', [
            'sections' => $sections
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/HomeSections/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
            'content' => ['required'],
        ]);

        HomeSection::create([
            'title' => $validated['title'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? false,
            'content' => $validated['content'] ?? [],
        ]);

        return redirect()->route('admin.home-sections.index')
            ->with('success', 'Home Section created successfully.');
    }

    public function edit(HomeSection $homeSection)
    {
        return Inertia::render('Admin/HomeSections/Edit', [
            'section' => $homeSection->only('id', 'title', 'order', 'is_active', 'content'),
        ]);
    }

    public function update(Request $request, HomeSection $homeSection)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $homeSection->update($validated);

        return redirect()->route('admin.home-sections.index')->with('success', 'Home Section updated successfully.');
    }

    public function destroy(HomeSection $homeSection)
    {
        $homeSection->delete();

        return redirect()->route('admin.home-sections.index')->with('success', 'Section deleted.');
    }

    public function toggleStatus(HomeSection $homeSection)
    {
        $homeSection->update([
            'is_active' => !$homeSection->is_active
        ]);

        return back();
    }
}
