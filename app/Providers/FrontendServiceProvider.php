<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Town;
use App\Models\TeamMember;
use App\Models\Experience;
use App\Models\IndulgeLink;
use App\Models\Setting;
use App\Models\VentureMagazine;

class FrontendServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Inertia::share([
            'layoutData' => function () {
                // Get featured magazine or fallback to latest published
                $featuredMagazine = VentureMagazine::where('is_published', 1)
                    ->where('is_featured', true)
                    ->select('id', 'title', 'slug', 'is_featured', 'is_published', 'hero_image', 'published_at')
                    ->first();

                if (!$featuredMagazine) {
                    $featuredMagazine = VentureMagazine::where('is_published', 1)
                        ->orderByDesc('published_at')
                        ->select('id', 'title', 'slug', 'is_featured', 'is_published', 'hero_image', 'published_at')
                        ->first();

                    if ($featuredMagazine) {
                        $featuredMagazine->is_featured = true; // in-memory update
                    }
                }
                return [
                    'members' => TeamMember::where('is_active', 1)
                        ->with('user')
                        ->get(['id', 'name', 'photo', 'designation', 'bio', 'user_id'])
                        ->map(function ($member) {
                            return [
                                'id' => $member->id,
                                'name' => $member->name,
                                'designation' => $member->designation,
                                'bio' => $member->bio,
                                'email' => $member->user->email ?? null,
                                'photo' => '/public/storage/' . $member->photo,
                            ];
                        }),

                    'towns' => Town::where('is_active', 1)
                        ->orderBy('name')
                        ->get(['id', 'name', 'slug'])
                        ->toArray(),

                    'experiences' => Experience::where('is_active', 1)
                        ->orderBy('name')
                        ->get(['id', 'name', 'slug'])
                        ->toArray(),
                    'magazine' => $featuredMagazine,
                    'indulgeLinks' => IndulgeLink::select(['id', 'title', 'url'])->where('is_active', true)->orderBy('sort_order')->get()->map(function ($tile) {
                        return [
                            'id' => $tile->id,
                            'title' => $tile->title,
                            'link' => $tile->url,
                        ];
                    }),
                    'contact' => json_decode(Setting::get('contact_info')),
                    'social' => json_decode(Setting::get('social_links')),
                ];
            }
        ]);
    }
}
