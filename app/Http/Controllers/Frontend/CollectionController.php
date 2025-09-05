<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\CollectionPageSetting;
use App\Models\Tag;
use Inertia\Inertia;
use Illuminate\Http\Request;
use DB;

class CollectionController extends Controller
{
    public function showCollection($slug)
    {
        $collection = Collection::where('slug', $slug)
            ->where('is_active', 1)
            ->with([
                'events.tags:id,name',
                'items.tags:id,name',
                'items.category:id,name,slug',
                'tours.tags:id,name',
            ])
            ->firstOrFail();

        $tags = collect()
            ->merge($collection->events->pluck('tags')->flatten())
            ->merge($collection->items->pluck('tags')->flatten())
            ->merge($collection->tours->pluck('tags')->flatten())
            ->unique('id')
            ->values()
            ->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ]);

        return Inertia::render('Frontend/Collections/Show', [
            'seo' => [
                'title' => $collection->seo_title,
                'description' => $collection->seo_description,
                'image' => '/public/storage/' . $collection->seo_image,
                'canonical' => canonical_url('/collection/' . $collection->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'collection' => [
                'id' => $collection->id,
                'name' => $collection->name,
                'slug' => $collection->slug,
                'summary' => $collection->summary,
                'hero_image' => '/public/storage/' . $collection->hero_image,
                'big_hero_image' => '/public/storage/' . $collection->big_hero_image,
                'description' => $collection->description,
            ],
            'events' => $collection->events->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->name,
                'slug' => $e->slug,
                'image' => '/public/storage/' . $e->hero_image,
                'tags' => $e->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ]),
            ]),
            'content_listings' => $collection->items->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->title,
                'slug' => $c->slug,
                'summary' => $c->summary,
                'image' => '/public/storage/' . $c->image,
                'tags' => $c->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ]),
                'category' => $c->category
                    ? [
                        'id' => $c->category->id,
                        'name' => $c->category->name,
                        'slug' => $c->category->slug,
                    ]
                    : null,
            ]),
            'tour_tiles' => $collection->tours->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->title,
                'slug' => $t->slug,
                'image' => '/public/storage/' . $t->image,
                'price' => $t->price,
                'tags' => $t->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ]),
            ]),
            'tags' => $tags,
        ]);
    }

    public function collections()
    {

        $collectionSettings = CollectionPageSetting::firstOrFail();

        $collections = Collection::with('tags')
            ->where('is_active', 1)
            ->latest()
            ->distinct()
            ->get();
        // Get all item IDs in this category
        $collectionIds = $collections->pluck('id');

        // Get tag IDs used by these listings from the taggables table
        $usedTagIds = DB::table('taggables')
            ->whereIn('taggable_id', $collectionIds)
            ->where('taggable_type', Collection::class) // Ensure this matches your actual stored class name
            ->pluck('tag_id')
            ->unique();

        // Fetch tag details
        $tags = Tag::whereIn('id', $usedTagIds)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Frontend/Collections/Index', [
            'seo' => [
                'title' => $collectionSettings->seo_title,
                'description' => $collectionSettings->seo_description,
                'image' => '/public/storage/' . $collectionSettings->seo_image,
                'canonical' => canonical_url('/collections/'),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'collection' => [
                'id' => $collectionSettings->id,
                'name' => $collectionSettings->name,
                'summary' => $collectionSettings->summary,
                'hero_image' => '/public/storage/' . $collectionSettings->hero_image,
                'big_hero_image' => '/public/storage/' . $collectionSettings->big_hero_image,
                'description' => $collectionSettings->description,
                'seo_title' => $collectionSettings->seo_title,
                'seo_description' => $collectionSettings->seo_description,
                'seo_image' => $collectionSettings->seo_image,
            ],
            'tags' => $tags,
            'initialItems' => $collections,
        ]);
    }

    public function collectionsFetch(Request $request)
    {

        $query = Collection::with('tags')
            ->where('is_active', 1)
            ->latest()
            ->distinct();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tags')) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $request->tags));
        }

        return response()->json($query->paginate(10));
    }
}
