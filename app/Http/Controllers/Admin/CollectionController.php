<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use App\Models\CollectionPageSetting;
use App\Models\Event;
use App\Models\Tag;
use App\Models\ThingsToDoCategory;
use App\Models\ThingsToDoItem;
use App\Models\TourTile;
use App\Models\Town;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Collection::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $collections = $query->where('type', 'collection')->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Collections/Index', [
            'collections' => $collections,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Collections/Create', [
            'contents' => ThingsToDoItem::where('is_active', 1)->orderBy('title')->get(),
            'tours' => TourTile::where('is_active', 1)->orderBy('title')->get(),
            'events' => Event::where('is_active', 1)->orderBy('name')->get(),
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'towns' => Town::where('is_active', 1)->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCollectionRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('hero_image')) {
            $validated['hero_image'] = $request->file('hero_image')->store('collections/hero', 'public');
        }

        if ($request->hasFile('big_hero_image')) {
            $validated['big_hero_image'] = $request->file('big_hero_image')->store('collections/big_hero', 'public');
        }

        if ($request->hasFile('seo_image')) {
            $validated['seo_image'] = $request->file('seo_image')->store('collections/seo', 'public');
        }

        // ✅ Generate slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $i = 1;

        while (Collection::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . Str::random(5);
        }

        $validated['slug'] = $slug;

        $validated['description'] = json_encode($validated['description']);

        $collection = Collection::create($validated);

        $collection->items()->sync(collect($request->input('content_ids', []))->pluck('id')->toArray());
        $collection->tours()->sync(collect($request->input('tour_ids', []))->pluck('id')->toArray());
        $collection->events()->sync(collect($request->input('event_ids', []))->pluck('id')->toArray());
        $collection->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());
        $collection->towns()->sync(collect($request->input('town_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.collections.index')->with('success', 'Collection created successfully.');
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
    public function edit(Collection $collection)
    {
        return Inertia::render('Admin/Collections/Edit', [
            'collection' => $collection->load(['items', 'tours', 'events', 'tags', 'towns']),
            'contents' => ThingsToDoItem::where('is_active', 1)->orderBy('title')->get(),
            'tours' => TourTile::where('is_active', 1)->orderBy('title')->get(),
            'events' => Event::where('is_active', 1)->orderBy('name')->get(),
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'towns' => Town::where('is_active', 1)->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCollectionRequest $request, Collection $collection)
    {
        $validated = $request->validated();

        if ($request->hasFile('hero_image')) {
            if ($collection->hero_image) {
                Storage::disk('public')->delete($collection->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')->store('collections/hero', 'public');
        } else {
            $validated['hero_image'] = $collection->hero_image;
        }

        if ($request->hasFile('big_hero_image')) {
            if ($collection->big_hero_image) {
                Storage::disk('public')->delete($collection->big_hero_image);
            }
            $validated['big_hero_image'] = $request->file('big_hero_image')->store('collections/big_hero', 'public');
        } else {
            $validated['big_hero_image'] = $collection->big_hero_image;
        }

        if ($request->hasFile('seo_image')) {
            if ($collection->seo_image) {
                Storage::disk('public')->delete($collection->seo_image);
            }
            $validated['seo_image'] = $request->file('seo_image')->store('collection/seo', 'public');
        } else {
            $validated['seo_image'] = $collection->seo_image;
        }

        // Update slug if name changed
        if ($collection->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // 🔒 Ensure content is stored as JSON
        $validated['description'] = json_encode($validated['description']);

        $collection->update($validated);

        $collection->items()->sync(collect($request->input('content_ids', []))->pluck('id')->toArray());
        $collection->tours()->sync(collect($request->input('tour_ids', []))->pluck('id')->toArray());
        $collection->events()->sync(collect($request->input('event_ids', []))->pluck('id')->toArray());
        $collection->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());
        $collection->towns()->sync(collect($request->input('town_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.collections.index')->with('success', 'Collection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        if ($collection->hero_image) {
            Storage::disk('public')->delete($collection->hero_image);
        }

        if ($collection->seo_image) {
            Storage::disk('public')->delete($collection->seo_image);
        }

        if ($collection->big_hero_image) {
            Storage::disk('public')->delete($collection->big_hero_image);
        }

        $collection->delete();

        return back();
    }

    public function toggleStatus(Collection $collection)
    {
        $collection->update(['is_active' => !$collection->is_active]);

        return back();
    }

    public function editSetting()
    {
        $setting = CollectionPageSetting::firstOrCreate([]);
        return Inertia::render('Admin/Collections/PageSettings', [
            'setting' => $setting,
        ]);
    }

    public function updateSetting(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'summary' => 'required|string|max:500',
            'description' => 'required|array',
            'description.blocks' => 'required|array|min:1',
            'hero_image' => 'required|image|max:2048|dimensions:min_width=738,min_height=500',
            'big_hero_image' => 'required|image|max:5120|dimensions:min_width=1200,min_height=600',
            'seo_image' => 'nullable|image|max:200|dimensions:min_width=1200,min_height=630',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
        ]);

        $setting = CollectionPageSetting::first();

        if ($request->hasFile('hero_image')) {
            if ($setting->hero_image) {
                Storage::disk('public')->delete($setting->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('collections_setting/hero', 'public');
        } else {
            $data['hero_image'] = $setting->hero_image;
        }

        if ($request->hasFile('big_hero_image')) {
            if ($setting->big_hero_image) {
                Storage::disk('public')->delete($setting->big_hero_image);
            }
            $data['big_hero_image'] = $request->file('big_hero_image')->store('collections_setting/big_hero', 'public');
        } else {
            $data['big_hero_image'] = $setting->big_hero_image;
        }

        if ($request->hasFile('seo_image')) {
            if ($setting->seo_image) {
                Storage::disk('public')->delete($setting->seo_image);
            }
            $data['seo_image'] = $request->file('seo_image')->store('collections_setting/seo_image', 'public');
        } else {
            $data['seo_image'] = $setting->seo_image;
        }

        $setting->update($data);

        return redirect()->back()->with('success', 'Collection page settings updated.');
    }
}
