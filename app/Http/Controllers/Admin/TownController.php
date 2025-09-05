<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Town;
use App\Http\Requests\StoreTownRequest;
use App\Http\Requests\UpdateTownRequest;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TownController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Town::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $towns = $query->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Towns/Index', [
            'towns' => $towns,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Towns/Create', [
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTownRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('hero_image')) {
            $validated['hero_image'] = $request->file('hero_image')->store('towns/hero', 'public');
        }

        if ($request->hasFile('big_hero_image')) {
            $validated['big_hero_image'] = $request->file('big_hero_image')->store('towns/big_hero', 'public');
        }

        if ($request->hasFile('seo_image')) {
            $validated['seo_image'] = $request->file('seo_image')->store('towns/seo', 'public');
        }

        // ✅ Generate slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $i = 1;

        while (Town::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . Str::random(5);
        }

        $validated['slug'] = $slug;

        $validated['description'] = json_encode($validated['description']);

        $town = Town::create($validated);

        $town->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.towns.index')->with('success', 'Town created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Town $town)
    {
        return Inertia::render('Admin/Towns/Edit', [
            'town' => $town->load('tags'),
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTownRequest $request, Town $town)
    {
        $validated = $request->validated();

        if ($request->hasFile('hero_image')) {
            if ($town->hero_image) {
                Storage::disk('public')->delete($town->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')->store('towns/hero', 'public');
        } else {
            $validated['hero_image'] = $town->hero_image;
        }

        if ($request->hasFile('big_hero_image')) {
            if ($town->big_hero_image) {
                Storage::disk('public')->delete($town->big_hero_image);
            }
            $validated['big_hero_image'] = $request->file('big_hero_image')->store('towns/big_hero', 'public');
        } else {
            $validated['big_hero_image'] = $town->big_hero_image;
        }

        if ($request->hasFile('seo_image')) {
            if ($town->seo_image) {
                Storage::disk('public')->delete($town->seo_image);
            }
            $validated['seo_image'] = $request->file('seo_image')->store('towns/seo', 'public');
        } else {
            $validated['seo_image'] = $town->seo_image;
        }

        // Update slug if name changed
        if ($town->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // 🔒 Ensure content is stored as JSON
        $validated['description'] = json_encode($validated['description']);

        $town->update($validated);

        $town->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.towns.index')->with('success', 'Town updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Town $town)
    {
        if ($town->hero_image) {
            Storage::disk('public')->delete($town->hero_image);
        }

        if ($town->seo_image) {
            Storage::disk('public')->delete($town->seo_image);
        }

        if ($town->big_hero_image) {
            Storage::disk('public')->delete($town->big_hero_image);
        }

        $town->delete();

        return back();
    }

    public function toggleStatus(Town $town)
    {
        $town->update(['is_active' => !$town->is_active]);

        return back();
    }
}
