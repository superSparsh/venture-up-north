<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ThingsToDoCategory;
use App\Models\TourTile;
use App\Models\TourTilePageSetting;
use App\Models\Town;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class TownController extends Controller
{
    public function showTown($slug)
    {
        $currentTown = Town::with('tags')->where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        $otherTowns = Town::with('tags')->where('slug', '!=', $slug)
            ->where('is_active', 1)
            ->inRandomOrder()
            ->take(5)
            ->get(['id', 'name', 'slug', 'hero_image']);

        $tourImage = TourTilePageSetting::first();
        $accommodtaionImage = ThingsToDoCategory::where('slug', 'accommodation-up-north')->first();

        return Inertia::render('Frontend/Towns/Show', [
            'seo' => [
                'title' => $currentTown->seo_title,
                'description' => $currentTown->seo_description,
                'image' => '/public/storage/' . $currentTown->seo_image,
                'canonical' => canonical_url('/town/' . $currentTown->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'town' => [
                'id' => $currentTown->id,
                'name' => $currentTown->name,
                'slug' => $currentTown->slug,
                'summary' => $currentTown->summary,
                'rezdy_url' => $currentTown->rezdy_url,
                'hero_image' => '/public/storage/' . $currentTown->hero_image,
                'big_hero_image' => '/public/storage/' . $currentTown->big_hero_image,
                'description' => $currentTown->description,
                'seo_title' => $currentTown->seo_title,
                'seo_description' => $currentTown->seo_description,
                'seo_image' => $currentTown->seo_image,
                'tags' => $currentTown->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ]),
            ],
            'allTowns' => $otherTowns->map(function ($town) {
                return [
                    'id' => $town->id,
                    'name' => $town->name,
                    'slug' => $town->slug,
                    'hero_image' => '/public/storage/' . $town->hero_image,
                    'tags' => $town->tags->map(fn($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ]),
                ];
            }),
            'tourImage' => $tourImage?->hero_image
                ? '/public/storage/' . $tourImage->hero_image
                : null,

            'accommodationImage' => $accommodtaionImage?->icon
                ? '/public/storage/' . $accommodtaionImage->icon
                : null,
        ]);
    }
}
