<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreThingsToDoItemRequest;
use App\Http\Requests\UpdateThingsToDoItemRequest;
use App\Models\Event;
use App\Models\Tag;
use App\Models\ThingsToDoCategory;
use App\Models\ThingsToDoItem;
use App\Models\Town;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ThingsToDoItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = ThingsToDoItem::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        $listings =  $query->with('category')->orderBy('title')->paginate(10);

        return Inertia::render('Admin/CategoryListings/Index', [
            'listings' => $listings,
            'filters' => $request->only('search'),
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/CategoryListings/Create', [
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'categories' => ThingsToDoCategory::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
            'towns' => Town::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
            'events' => Event::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreThingsToDoItemRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('things_to_do_items/hero', 'public');
        }

        if ($request->hasFile('big_image')) {
            $validated['big_image'] = $request->file('big_image')->store('things_to_do_items/big_hero', 'public');
        }

        if ($request->hasFile('seo_image')) {
            $validated['seo_image'] = $request->file('seo_image')->store('things_to_do_items/seo', 'public');
        }

        // ✅ Generate slug
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $i = 1;

        while (ThingsToDoItem::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . Str::random(5);
        }

        $validated['slug'] = $slug;

        $validated['content'] = json_encode($validated['content']);
        if (isset($validated['opening_times'])) {
            $validated['opening_times'] = json_encode($validated['opening_times']);
        }

        // Extract the category ID whether it's raw or object
        $categoryInput = $request->input('category_id');
        $categoryId = is_array($categoryInput) ? ($categoryInput['id'] ?? null) : $categoryInput;

        // Inject it directly into validated input
        $validated['category_id'] = $categoryId;

        $listing = ThingsToDoItem::create($validated);

        $listing->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());
        $listing->towns()->sync(collect($request->input('town_ids', []))->pluck('id'));
        $listing->events()->sync(collect($request->input('event_ids', []))->pluck('id'));

        return redirect()->route('admin.category-listings.index')->with('success', 'Category Listing created successfully.');
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
    public function edit(ThingsToDoItem $category_listing)
    {
        $category_listing->load('tags', 'category', 'towns', 'events');

        return Inertia::render('Admin/CategoryListings/Edit', [
            'listing' => $category_listing,
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'categories' => ThingsToDoCategory::where('is_active', 1)->orderBy('name')->get(),
            'towns' => Town::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
            'events' => Event::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThingsToDoItemRequest $request, ThingsToDoItem $category_listing)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($category_listing->image) {
                Storage::disk('public')->delete($category_listing->image);
            }
            $validated['image'] = $request->file('image')->store('things_to_do_items/hero', 'public');
        } else {
            $validated['image'] = $category_listing->image;
        }

        if ($request->hasFile('big_image')) {
            if ($category_listing->big_image) {
                Storage::disk('public')->delete($category_listing->big_image);
            }
            $validated['big_image'] = $request->file('big_image')->store('things_to_do_items/big_hero', 'public');
        } else {
            $validated['big_image'] = $category_listing->big_image;
        }

        if ($request->hasFile('seo_image')) {
            if ($category_listing->seo_image) {
                Storage::disk('public')->delete($category_listing->seo_image);
            }
            $validated['seo_image'] = $request->file('seo_image')->store('things_to_do_items/seo', 'public');
        } else {
            $validated['seo_image'] = $category_listing->seo_image;
        }

        // Update slug if name changed
        if ($category_listing->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // 🔒 Ensure content is stored as JSON
        $validated['content'] = json_encode($validated['content']);

        if (isset($validated['opening_times'])) {
            $validated['opening_times'] = json_encode($validated['opening_times']);
        }


        // Parse category
        $categoryInput = $request->input('category_id');
        $validated['category_id'] = is_array($categoryInput) ? ($categoryInput['id'] ?? null) : $categoryInput;

        $category_listing->update($validated);

        $category_listing->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());
        $category_listing->towns()->sync(collect($request->input('town_ids', []))->pluck('id')->toArray());
        $category_listing->events()->sync(collect($request->input('event_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.category-listings.index')->with('success', 'Category Listing updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThingsToDoItem $category_listings)
    {
        if ($category_listings->image) {
            Storage::disk('public')->delete($category_listings->image);
        }

        if ($category_listings->seo_image) {
            Storage::disk('public')->delete($category_listings->seo_image);
        }

        if ($category_listings->big_image) {
            Storage::disk('public')->delete($category_listings->big_image);
        }

        $category_listings->delete();

        return back();
    }

    public function toggleStatus(ThingsToDoItem $listing)
    {
        $listing->update(['is_active' => !$listing->is_active]);

        return back();
    }
}
