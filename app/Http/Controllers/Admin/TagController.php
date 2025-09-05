<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tag::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $tags = $query->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Tags/Index', [
            'tags' => $tags,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Tags/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();

        // if ($request->hasFile('hero_image')) {
        //     $validated['hero_image'] = $request->file('hero_image')->store('tags/hero', 'public');
        // }

        // if ($request->hasFile('big_hero_image')) {
        //     $validated['big_hero_image'] = $request->file('big_hero_image')->store('tags/big_hero', 'public');
        // }

        // if ($request->hasFile('seo_image')) {
        //     $validated['seo_image'] = $request->file('seo_image')->store('tags/seo', 'public');
        // }

        // // ✅ Generate slug
        // $slug = Str::slug($validated['name']);
        // $originalSlug = $slug;
        // $i = 1;

        // while (Tag::where('slug', $slug)->exists()) {
        //     $slug = $originalSlug . '-' . Str::random(5);
        // }

        // $validated['slug'] = $slug;

        // $validated['description'] = json_encode($validated['description']);

        Tag::create($validated);

        return redirect()->route('admin.tags.index')->with('success', 'Classification created successfully.');
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
    public function edit(Tag $tag)
    {
        return Inertia::render('Admin/Tags/Edit', [
            'tag' => $tag,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $validated = $request->validated();

        // if ($request->hasFile('hero_image')) {
        //     if ($tag->hero_image) {
        //         Storage::disk('public')->delete($tag->hero_image);
        //     }
        //     $validated['hero_image'] = $request->file('hero_image')->store('tags/hero', 'public');
        // } else {
        //     $validated['hero_image'] = $tag->hero_image;
        // }

        // if ($request->hasFile('big_hero_image')) {
        //     if ($tag->big_hero_image) {
        //         Storage::disk('public')->delete($tag->big_hero_image);
        //     }
        //     $validated['big_hero_image'] = $request->file('big_hero_image')->store('tags/big_hero', 'public');
        // } else {
        //     $validated['big_hero_image'] = $tag->big_hero_image;
        // }

        // if ($request->hasFile('seo_image')) {
        //     if ($tag->seo_image) {
        //         Storage::disk('public')->delete($tag->seo_image);
        //     }
        //     $validated['seo_image'] = $request->file('seo_image')->store('tags/seo', 'public');
        // } else {
        //     $validated['seo_image'] = $tag->seo_image;
        // }

        // Update slug if name changed
        // if ($tag->name !== $validated['name']) {
        //     $validated['slug'] = Str::slug($validated['name']);
        // }

        // 🔒 Ensure content is stored as JSON
        // $validated['description'] = json_encode($validated['description']);

        $tag->update($validated);

        return redirect()->route('admin.tags.index')->with('success', 'Classification updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        if ($tag->hero_image) {
            Storage::disk('public')->delete($tag->hero_image);
        }

        if ($tag->seo_image) {
            Storage::disk('public')->delete($tag->seo_image);
        }

        if ($tag->big_hero_image) {
            Storage::disk('public')->delete($tag->big_hero_image);
        }

        $tag->delete();

        return back();
    }

    public function toggleStatus(Tag $tag)
    {
        $tag->update(['is_active' => !$tag->is_active]);

        return back();
    }
}
