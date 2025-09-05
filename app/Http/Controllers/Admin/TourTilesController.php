<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourTileRequest;
use App\Http\Requests\UpdateTourTileRequest;
use App\Models\Tag;
use App\Models\TourTile;
use App\Models\TourTilePageSetting;
use App\Models\Town;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourTilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TourTile::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }
        $tourTiles = $query->orderBy('title')->paginate(10)->withQueryString();

        return Inertia::render('Admin/TourTiles/Index', [
            'tourTiles' => $tourTiles,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/TourTiles/Create', [
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'towns' => Town::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTourTileRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('tour_tiles/hero', 'public');
        }

        if ($request->hasFile('big_hero_image')) {
            $validated['big_hero_image'] = $request->file('big_hero_image')->store('tour_tiles/big_hero_image', 'public');
        }

        if ($request->hasFile('seo_image')) {
            $validated['seo_image'] = $request->file('seo_image')->store('tour_tiles/seo', 'public');
        }

        // ✅ Generate slug
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $i = 1;

        while (TourTile::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . Str::random(5);
        }

        $validated['slug'] = $slug;

        if (isset($validated['content'])) {
            $validated['content'] = json_encode($validated['content']);
        }
        if (isset($validated['opening_times'])) {
            $validated['opening_times'] = json_encode($validated['opening_times']);
        }

        $tourTile = TourTile::create($validated);

        $tourTile->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());
        $tourTile->towns()->sync(collect($request->input('town_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.tour-tiles.index')->with('success', 'Tour Tile created successfully.');
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
    public function edit(TourTile $tourTile)
    {

        return Inertia::render('Admin/TourTiles/Edit', [
            'tourTile' => $tourTile->load(['tags', 'towns']),
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'towns' => Town::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTourTileRequest $request, TourTile $tour_tile)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($tour_tile->image) {
                Storage::disk('public')->delete($tour_tile->image);
            }
            $validated['image'] = $request->file('image')->store('tour_tiles/hero', 'public');
        } else {
            $validated['image'] = $tour_tile->image;
        }

        if ($request->hasFile('big_hero_image')) {
            if ($tour_tile->big_hero_image) {
                Storage::disk('public')->delete($tour_tile->big_hero_image);
            }
            $validated['big_hero_image'] = $request->file('big_hero_image')->store('tour_tiles/big_hero_image', 'public');
        } else {
            $validated['big_hero_image'] = $tour_tile->big_hero_image;
        }

        if ($request->hasFile('seo_image')) {
            if ($tour_tile->seo_image) {
                Storage::disk('public')->delete($tour_tile->seo_image);
            }
            $validated['seo_image'] = $request->file('seo_image')->store('tour_tiles/seo', 'public');
        } else {
            $validated['seo_image'] = $tour_tile->seo_image;
        }

        // Update slug if name changed
        if ($tour_tile->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // 🔒 Ensure content is stored as JSON
        if (isset($validated['content'])) {
            $validated['content'] = json_encode($validated['content']);
        }

        if (isset($validated['opening_times'])) {
            $validated['opening_times'] = json_encode($validated['opening_times']);
        }


        $tour_tile->update($validated);

        $tour_tile->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());
        $tour_tile->towns()->sync(collect($request->input('town_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.tour-tiles.index')->with('success', 'Tour Tile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TourTile $tourTile)
    {
        if ($tourTile->image) {
            Storage::disk('public')->delete($tourTile->image);
        }

        if ($tourTile->seo_image) {
            Storage::disk('public')->delete($tourTile->seo_image);
        }

        if ($tourTile->big_hero_image) {
            Storage::disk('public')->delete($tourTile->big_hero_image);
        }

        $tourTile->delete();

        return back();
    }

    public function toggleStatus(TourTile $tourTile)
    {
        $tourTile->update(['is_active' => !$tourTile->is_active]);

        return back();
    }

    public function editSetting()
    {
        $setting = TourTilePageSetting::firstOrCreate([]);
        return Inertia::render('Admin/TourTiles/PageSettings', [
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

        $setting = TourTilePageSetting::first();

        if ($request->hasFile('hero_image')) {
            if ($setting->hero_image) {
                Storage::disk('public')->delete($setting->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('tour_tiles_setting/hero', 'public');
        } else {
            $data['hero_image'] = $setting->hero_image;
        }

        if ($request->hasFile('big_hero_image')) {
            if ($setting->big_hero_image) {
                Storage::disk('public')->delete($setting->big_hero_image);
            }
            $data['big_hero_image'] = $request->file('big_hero_image')->store('tour_tiles_setting/big_hero', 'public');
        } else {
            $data['big_hero_image'] = $setting->big_hero_image;
        }

        if ($request->hasFile('seo_image')) {
            if ($setting->seo_image) {
                Storage::disk('public')->delete($setting->seo_image);
            }
            $data['seo_image'] = $request->file('seo_image')->store('tour_tiles_setting/seo_image', 'public');
        } else {
            $data['seo_image'] = $setting->seo_image;
        }

        $setting->update($data);

        return redirect()->back()->with('success', 'Tour page settings updated.');
    }
}
