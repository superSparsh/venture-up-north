<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThingsToDoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\StoreThingsToDoCategoryRequest;
use App\Http\Requests\UpdateThingsToDoCategoryRequest;
use App\Models\Tag;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ThingsToDoCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ThingsToDoCategory::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $categories = $query->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Categories/Create', [
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreThingsToDoCategoryRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('things_to_do_categories/hero', 'public');
        }

        if ($request->hasFile('big_image')) {
            $validated['big_image'] = $request->file('big_image')->store('things_to_do_categories/big_hero', 'public');
        }

        if ($request->hasFile('seo_image')) {
            $validated['seo_image'] = $request->file('seo_image')->store('things_to_do_categories/seo', 'public');
        }

        // ✅ Generate slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $i = 1;

        while (ThingsToDoCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . Str::random(5);
        }

        $validated['slug'] = $slug;

        $validated['description'] = json_encode($validated['description']);

        $category = ThingsToDoCategory::create($validated);

        $category->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
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
    public function edit(ThingsToDoCategory $category)
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category->load('tags'),
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThingsToDoCategoryRequest $request, ThingsToDoCategory $category)
    {
        $validated = $request->validated();

        if ($request->hasFile('icon')) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            $validated['icon'] = $request->file('icon')->store('things_to_do_categories/hero', 'public');
        } else {
            $validated['icon'] = $category->icon;
        }

        if ($request->hasFile('big_image')) {
            if ($category->big_image) {
                Storage::disk('public')->delete($category->big_image);
            }
            $validated['big_image'] = $request->file('big_image')->store('things_to_do_categories/big_hero', 'public');
        } else {
            $validated['big_image'] = $category->big_image;
        }

        if ($request->hasFile('seo_image')) {
            if ($category->seo_image) {
                Storage::disk('public')->delete($category->seo_image);
            }
            $validated['seo_image'] = $request->file('seo_image')->store('things_to_do_categories/seo', 'public');
        } else {
            $validated['seo_image'] = $category->seo_image;
        }

        // Update slug if name changed
        if ($category->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // 🔒 Ensure content is stored as JSON
        $validated['description'] = json_encode($validated['description']);

        $category->update($validated);

        $category->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThingsToDoCategory $category)
    {
        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }

        if ($category->big_image) {
            Storage::disk('public')->delete($category->big_image);
        }

        $category->delete();

        return back();
    }

    public function toggleStatus(ThingsToDoCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return back();
    }
}
