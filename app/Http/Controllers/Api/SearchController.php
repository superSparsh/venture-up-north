<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Town;
use App\Models\Experience;
use App\Models\ThingsToDoCategory;
use App\Models\ThingsToDoItem;
use App\Models\TourTile;
use App\Models\VentureMagazine;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $towns = Town::where('name', 'LIKE', "%$q%")->limit(5)->get(['id', 'name', 'slug', 'hero_image']);
        $experiences = Experience::where('name', 'LIKE', "%$q%")->limit(5)->get(['id', 'name', 'slug', 'hero_image']);
        $tours = TourTile::where('title', 'LIKE', "%$q%")->limit(5)->get(['id', 'title as name', 'slug', 'image as hero_image']);
        $posts = VentureMagazine::where('title', 'LIKE', "%$q%")->limit(5)->get(['id', 'title as name', 'slug', 'hero_image']);
        $events = Event::upcoming()->where('name', 'LIKE', "%$q%")->limit(5)->get(['id', 'name', 'slug', 'hero_image']);
        $contents = ThingsToDoCategory::where('name', 'LIKE', "%$q%")->limit(5)->get(['id', 'name', 'slug', 'icon as hero_image']);
        $content_listings = ThingsToDoItem::with(['category:id,slug'])->where('title', 'LIKE', "%$q%")->limit(5)->get(['id', 'title as name', 'slug', 'image as hero_image', 'category_id']);
        $collections = Collection::where('name', 'LIKE', "%$q%")->limit(5)->get(['id', 'name', 'slug', 'hero_image']);

        // Sort fuzzy-matched results by similarity
        $sortBySimilarity = fn($a, $b) => similar_text($q, $b->name) <=> similar_text($q, $a->name);

        $towns = $towns->sort($sortBySimilarity)->values();
        $experiences = $experiences->sort($sortBySimilarity)->values();
        $tours = $tours->sort($sortBySimilarity)->values();
        $posts = $posts->sort($sortBySimilarity)->values();
        $events = $events->sort($sortBySimilarity)->values();
        $contents = $contents->sort($sortBySimilarity)->values();
        $content_listings = $content_listings->sort($sortBySimilarity)->values();
        $collections = $collections->sort($sortBySimilarity)->values();

        return response()->json([
            'towns' => $towns,
            'experiences' => $experiences,
            'tours' => $tours,
            'posts' => $posts,
            'events' => $events,
            'contents' => $contents,
            'content_listings' => $content_listings,
            'collections' => $collections
        ]);
    }
}
