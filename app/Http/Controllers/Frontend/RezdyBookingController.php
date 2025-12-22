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
            'affiliates' => $town->affiliates ?? 'rezdy',
            'big_hero_image' => $town->big_hero_image
                ? '/public/storage/' . $town->big_hero_image
                : null,
            'seo_title' => $town->seo_title,
            'seo_description' => $town->seo_description,
            'seo_image' => $town->seo_image,
            'base_path' => 'venture/book',
            // Town specific text
            'summary' => $town->summary ?? null,
            'content' => $town->description ?? null,
            // Defaults for fields not present in Town
            'location' => null,
            'address' => null,
            'opening_times' => null,
            'email' => null,
            'phone_number' => null,
            'video' => null,
            'custom_fields' => [],
            'social_links' => [],
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
            'affiliates' => $experience->affiliates ?? 'rezdy',
            'big_hero_image' => $experience->big_hero_image
                ? '/public/storage/' . $experience->big_hero_image
                : null,
            'seo_title' => $experience->seo_title,
            'seo_description' => $experience->seo_description,
            'seo_image' => $experience->seo_image,
            'base_path' => 'experience/book',
            // Experience specific text
            'summary' => $experience->summary ?? null,
            'content' => $experience->description ?? null,
            // Defaults for fields not present in Experience
            'location' => null,
            'address' => null,
            'opening_times' => null,
            'email' => null,
            'phone_number' => null,
            'video' => null,
            'custom_fields' => [],
            'social_links' => [],
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
            'affiliates' => $tourTile->affiliates ?? 'rezdy',
            'big_hero_image' => $tourTile->big_hero_image
                ? '/public/storage/' . $tourTile->big_hero_image
                : null,
            'seo_title' => $tourTile->seo_title,
            'seo_description' => $tourTile->seo_description,
            'seo_image' => $tourTile->seo_image,
            'base_path' => 'tours/book',
            // Full TourTile details
            'summary' => $tourTile->summary,
            'content' => $tourTile->content,
            'location' => $tourTile->location,
            'address' => $tourTile->address,
            'opening_times' => $tourTile->opening_times,
            'email' => $tourTile->email,
            'phone_number' => $tourTile->phone_number,
            'video' => $tourTile->video,
            'custom_fields' => $tourTile->custom_fields,
            'social_links' => $tourTile->social_links,
        ]);
    }
}
