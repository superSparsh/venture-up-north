<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ExperienceController extends Controller
{
    public function showExperience($slug)
    {
        $currentExperience = Experience::with('tags')->where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        $otherExperiences = Experience::with('tags')->where('slug', '!=', $slug)
            ->where('is_active', 1)
            ->inRandomOrder()
            ->take(5)
            ->get(['id', 'name', 'slug', 'hero_image', 'seo_title', 'seo_description', 'seo_image']);

        return Inertia::render('Frontend/Experiences/Show', [
            'seo' => [
                'title' => $currentExperience->seo_title,
                'description' => $currentExperience->seo_description,
                'image' => '/public/storage/' . $currentExperience->seo_image,
                'canonical' => canonical_url('/experience/' . $currentExperience->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'experience' => [
                'id' => $currentExperience->id,
                'name' => $currentExperience->name,
                'slug' => $currentExperience->slug,
                'rezdy_url' => $currentExperience->rezdy_url,
                'hero_image' => '/public/storage/' . $currentExperience->hero_image,
                'big_hero_image' => '/public/storage/' . $currentExperience->big_hero_image,
                'description' => $currentExperience->description,
                'seo_title' => $currentExperience->seo_title,
                'seo_description' => $currentExperience->seo_description,
                'seo_image' => '/public/storage/' . $currentExperience->seo_image,
                'tags' => $currentExperience->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ]),
            ],
            'allExperiences' => $otherExperiences->map(function ($experience) {
                return [
                    'id' => $experience->id,
                    'name' => $experience->name,
                    'slug' => $experience->slug,
                    'hero_image' => '/public/storage/' . $experience->hero_image,
                    'tags' => $experience->tags->map(fn($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ]),
                ];
            }),
        ]);
    }
}
