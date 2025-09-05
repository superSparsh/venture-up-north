<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Town;
use Inertia\Inertia;
use App\Models\Experience;
use App\Models\TourTile;
use Illuminate\Support\Facades\Storage;


class RezdyBookingController extends Controller
{
    public function show($slug)
    {
        // Find matching tile or experience by slug
        $town = Town::where('slug', $slug)->firstOrFail();

        // Pass rezdy_url to Vue page
        return Inertia::render('Frontend/RezdyBooking', [
            'seo' => [
                'title' => $town->seo_title,
                'description' => $town->seo_description,
                'image' => '/public/storage/' . $town->seo_image,
                'canonical' => canonical_url('/venture/book/' . $town->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'title' => $town->name,
            'slug' => $town->slug,
            'rezdy_url' => $town->rezdy_url,
            'big_hero_image' => $town->big_hero_image
                ? '/public/storage/' . $town->big_hero_image
                : null,
            'seo_title' => $town->seo_title,
            'seo_description' => $town->seo_description,
            'seo_image' => $town->seo_image,
            'base_path' => 'venture/book',
        ]);
    }

    public function experience($slug)
    {
        $experience = Experience::where('slug', $slug)->firstOrFail();

        return Inertia::render('Frontend/RezdyBooking', [
            'seo' => [
                'title' => $experience->seo_title,
                'description' => $experience->seo_description,
                'image' => '/public/storage/' . $experience->seo_image,
                'canonical' => canonical_url('/experience/book/' . $experience->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'title' => $experience->name,
            'slug' => $experience->slug,
            'rezdy_url' => $experience->rezdy_url,
            'big_hero_image' => $experience->big_hero_image
                ? '/public/storage/' . $experience->big_hero_image
                : null,
            'seo_title' => $experience->seo_title,
            'seo_description' => $experience->seo_description,
            'seo_image' => $experience->seo_image,
            'base_path' => 'experience/book',
        ]);
    }

    public function tourTiles($slug)
    {
        $tourTile = TourTile::where('slug', $slug)->firstOrFail();

        return Inertia::render('Frontend/RezdyBooking', [
            'seo' => [
                'title' => $tourTile->seo_title,
                'description' => $tourTile->seo_description,
                'image' => '/public/storage/' . $tourTile->seo_image,
                'canonical' => canonical_url('/tours/book/' . $tourTile->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'title' => $tourTile->title,
            'slug' => $tourTile->slug,
            'rezdy_url' => $tourTile->rezdy_url,
            'big_hero_image' => $tourTile->big_hero_image
                ? '/public/storage/' . $tourTile->big_hero_image
                : null,
            'seo_title' => $tourTile->seo_title,
            'seo_description' => $tourTile->seo_description,
            'seo_image' => $tourTile->seo_image,
            'base_path' => 'tours/book',
        ]);
    }
}
