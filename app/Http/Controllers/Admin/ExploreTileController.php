<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExploreTileRequest;
use App\Http\Requests\UpdateExploreTileRequest;
use Illuminate\Http\Request;
use App\Models\ExploreTile;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ExploreTileController extends Controller
{
    public function index()
    {
        $tiles = ExploreTile::orderBy('sort_order')->paginate(10);

        return Inertia::render('Admin/ExploreTiles/Index', [
            'tiles' => $tiles
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ExploreTiles/Create');
    }

    public function store(StoreExploreTileRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('imageTile/hero', 'public');
        }
        ExploreTile::create($validated);

        return redirect()->route('admin.explore-tiles.index')->with('success', 'Image Tile created successfully');
    }

    public function edit(ExploreTile $explore_tile)
    {
        return Inertia::render('Admin/ExploreTiles/Edit', [
            'tile' => $explore_tile
        ]);
    }

    public function update(UpdateExploreTileRequest $request, ExploreTile $explore_tile)
    {
        $validated = $request->validated();


        if ($request->hasFile('image')) {
            if ($explore_tile->image) {
                Storage::disk('public')->delete($explore_tile->image);
            }
            $validated['image'] = $request->file('image')->store('imageTile/hero', 'public');
        } else {
            $validated['image'] = $explore_tile->image;
        }

        $explore_tile->update($validated);

        return redirect()->route('admin.explore-tiles.index')->with('success', 'Tile updated successfully');
    }

    public function destroy(ExploreTile $explore_tile)
    {
        if ($explore_tile->image) {
            Storage::disk('public')->delete($explore_tile->image);
        }

        $explore_tile->delete();

        return back()->with('success', 'Tile deleted');
    }

    public function toggleStatus(ExploreTile $tile)
    {
        $tile->update(['is_active' => !$tile->is_active]);

        return back();
    }
}
