<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Tag;
use App\Models\ThingsToDoCategory;
use App\Models\ThingsToDoItem;
use App\Models\Town;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ThingsToDoCategoryController extends Controller
{
    public function showCategory($slug)
    {
        $currentCategory = ThingsToDoCategory::where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        $thingsToDoItems = ThingsToDoItem::with(['tags', 'towns', 'events'])
            ->where('category_id', $currentCategory->id)
            ->where('is_active', 1)
            ->latest()
            ->distinct()
            ->get();

        // Get all item IDs in this category
        $listingIds = $thingsToDoItems->pluck('id');

        // Get tag IDs used by these listings from the taggables table
        $usedTagIds = DB::table('taggables')
            ->whereIn('taggable_id', $listingIds)
            ->where('taggable_type', ThingsToDoItem::class) // Ensure this matches your actual stored class name
            ->pluck('tag_id')
            ->unique();

        // Fetch tag details
        $tags = Tag::whereIn('id', $usedTagIds)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Frontend/Category/Show', [
            'seo' => [
                'title' => $currentCategory->seo_title,
                'description' => $currentCategory->seo_description,
                'image' => '/public/storage/' . $currentCategory->seo_image,
                'canonical' => canonical_url('/explore/' . $currentCategory->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'category' => [
                'id' => $currentCategory->id,
                'name' => $currentCategory->name,
                'slug' => $currentCategory->slug,
                'summary' => $currentCategory->summary,
                'hero_image' => '/public/storage/' . $currentCategory->icon,
                'big_hero_image' => '/public/storage/' . $currentCategory->big_image,
                'description' => $currentCategory->description,
                'seo_title' => $currentCategory->seo_title,
                'seo_description' => $currentCategory->seo_description,
                'seo_image' => $currentCategory->seo_image,
            ],
            'tags' => $tags,
            'towns' => Town::where('is_active', 1)->orderBy('name')->get(['id', 'name', 'slug']),
            'events' => Event::where('is_active', 1)->orderBy('name')->get(['id', 'name', 'slug']),
            'initialItems' => $thingsToDoItems,
        ]);
    }

    public function listingsFetch(Request $request, $slug)
    {
        $category = ThingsToDoCategory::where('slug', $slug)->firstOrFail();

        $query = ThingsToDoItem::with(['tags', 'category', 'towns', 'events'])
            ->where('category_id', $category->id)
            ->where('is_active', 1)
            ->latest()
            ->distinct();

        if ($request->filled('town_ids')) {
            $query->whereHas('towns', function ($q) use ($request) {
                $q->whereIn('towns.id', $request->town_ids);
            });
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('towns', function ($townQ) use ($searchTerm) {
                        $townQ->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('tags', function ($tagQ) use ($searchTerm) {
                        $tagQ->where('name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        if ($request->filled('tags')) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $request->tags));
        }

        return response()->json($query->paginate(10));
    }
}
