<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\TourTile;
use App\Models\TourTilePageSetting;
use App\Models\Town;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TourTileController extends Controller
{
    public function showTour($slug)
    {
        // Get the listing within that category
        $currentTour = TourTile::with(['towns', 'tags'])
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        return Inertia::render('Frontend/TourTile/Show', [
            'seo' => [
                'title' => $currentTour->seo_title,
                'description' => $currentTour->seo_description,
                'image' => '/public/storage/' . $currentTour->seo_image,
                'canonical' => canonical_url('/tour/' . $currentTour->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'tour' => [
                'id' => $currentTour->id,
                'name' => $currentTour->title,
                'slug' => $currentTour->slug,
                'summary' => $currentTour->summary,
                'hero_image' => '/public/storage/' . $currentTour->image,
                'big_hero_image' => '/public/storage/' . $currentTour->big_hero_image,
                'description' => $currentTour->content,
                'opening_times' => $currentTour->opening_times,
                'address' => $currentTour->address,
                'phone_number' => $currentTour->phone_number,
                'email' => $currentTour->email,
                'custom_fields' => $currentTour->custom_fields,
                'custom_buttons' => $currentTour->custom_buttons,
                'social_links' => $currentTour->social_links,
                'seo_title' => $currentTour->seo_title,
                'seo_description' => $currentTour->seo_description,
                'seo_image' => $currentTour->seo_image,
                'location' => $currentTour->location,
                'video' => $currentTour->video,
                'towns' => $currentTour->towns->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]),
                'tags' => $currentTour->tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]),
            ],
        ]);
    }

    public function tours()
    {
        $tourSettings = TourTilePageSetting::firstOrFail();

        $tours = TourTile::with(['tags', 'towns'])
            ->where('is_active', 1)
            ->latest()
            ->distinct()
            ->get();

        // Get all item IDs in this category
        $tourIds = $tours->pluck('id');

        // Get tag IDs used by these listings from the taggables table
        $usedTagIds = DB::table('taggables')
            ->whereIn('taggable_id', $tourIds)
            ->where('taggable_type', TourTile::class) // Ensure this matches your actual stored class name
            ->pluck('tag_id')
            ->unique();

        // Fetch tag details
        $tags = Tag::whereIn('id', $usedTagIds)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Frontend/TourTile/Index', [
            'seo' => [
                'title' => $tourSettings->seo_title,
                'description' => $tourSettings->seo_description,
                'image' => '/public/storage/' . $tourSettings->seo_image,
                'canonical' => canonical_url('/tours/'),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'tour' => [
                'id' => $tourSettings->id,
                'name' => $tourSettings->name,
                'summary' => $tourSettings->summary,
                'hero_image' => '/public/storage/' . $tourSettings->image,
                'big_hero_image' => '/public/storage/' . $tourSettings->big_hero_image,
                'description' => $tourSettings->description,
                'seo_title' => $tourSettings->seo_title,
                'seo_description' => $tourSettings->seo_description,
                'seo_image' => $tourSettings->seo_image,
            ],
            'tags' => $tags,
            'towns' => Town::where('is_active', 1)->orderBy('name')->get(['id', 'name', 'slug']),
            'initialItems' => $tours,
        ]);
    }

    public function toursFetch(Request $request)
    {

        $query = TourTile::with(['tags',  'towns'])
            ->where('is_active', 1)
            ->latest()
            ->distinct();

        if ($request->filled('town_ids')) {
            $query->whereHas('towns', function ($q) use ($request) {
                $q->whereIn('towns.id', $request->town_ids);
            });
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tags')) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $request->tags));
        }

        return response()->json($query->paginate(10));
    }
}
