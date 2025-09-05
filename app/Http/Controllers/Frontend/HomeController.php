<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Event;
use App\Models\Experience;
use App\Models\ExploreTile;
use App\Models\IndulgeLink;
use App\Models\Setting;
use App\Models\SitePopup;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TeamMember;
use App\Models\ThingsToDoCategory;
use App\Models\ThingsToDoItem;
use App\Models\TourTile;
use App\Models\Town;
use App\Models\VentureMagazine;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


class HomeController extends Controller
{
    public function layoutData()
    {
        $popup = SitePopup::single();
        return Inertia::render('layouts/FrontendLayout', [
            'members' => TeamMember::all(['id', 'name', 'photo', 'bio', 'is_active'])->where('is_active', 1)->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                ];
            }),


            'towns' => Town::all(['id', 'name', 'hero_image', 'slug', 'summary', 'rezdy_url', 'is_active'])->where('is_active', 1)->orderBy('name')->map(function ($town) {
                return [
                    'id' => $town->id,
                    'name' => $town->name,
                    'slug' => $town->slug,
                ];
            }),

            'experiences' => Experience::all(['id', 'name', 'hero_image', 'slug', 'rezdy_url', 'is_active'])->where('is_active', 1)->orderBy('name')->map(function ($experience) {
                return [
                    'id' => $experience->id,
                    'name' => $experience->name,
                    'slug' => $experience->slug,
                ];
            }),
            'magazines' => VentureMagazine::all(['id', 'title', 'hero_image', 'slug', 'is_published'])->where('is_published', 1)->map(function ($magazine) {
                return [
                    'id' => $magazine->id,
                    'title' => $magazine->title,
                    'slug' => $magazine->slug,
                ];
            }),
            'indulgeLinks' => IndulgeLink::select(['id', 'title', 'url'])->where('is_active', true)->orderBy('sort_order')->get()->map(function ($tile) {
                return [
                    'id' => $tile->id,
                    'title' => $tile->title,
                    'link' => $tile->url,
                ];
            }),
            'contact' => json_decode(Setting::get('contact_info')),
            'social' => json_decode(Setting::get('social_links')),
            'magazinePopup' => $popup->magazine_content ?? '',
        ]);
    }
    public function index()
    {
        $seo = json_decode(\App\Models\Setting::get('site_seo'), true);
        $popup = SitePopup::single();

        return Inertia::render('Frontend/Home/Index', [
            // 'seo' => [
            //     'title' => $seo['title'] ?? 'Welcome - Venture Up North',
            //     'description' => $seo['description'] ?? 'Venture Up North - Explore, Indulge, Breathe',
            //     'image' => '/public' . $seo['image'] ?? asset('/public/images/Venture-Up-North.png'),
            //     'canonical' => canonical_url(),
            //     'robots' => 'index, follow',
            //     'type' => 'website',
            // ],
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


            'towns' => Town::with('tags')->select(['id', 'name', 'hero_image', 'slug', 'summary', 'rezdy_url', 'is_active'])->where('is_active', 1)->orderBy('name')->get()->map(function ($town) {
                return [
                    'id' => $town->id,
                    'name' => $town->name,
                    'slug' => $town->slug,
                    'summary' => $town->summary,
                    'rezdy_url' => $town->rezdy_url,
                    'hero_image' => '/public/storage/' . $town->hero_image,
                    'tags' => $town->tags->map(fn($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ]),
                ];
            }),

            'experiences' => Experience::with('tags')->select(['id', 'name', 'hero_image', 'slug', 'rezdy_url', 'is_active'])->where('is_active', 1)->orderBy('name')->get()->map(function ($experience) {
                return [
                    'id' => $experience->id,
                    'name' => $experience->name,
                    'slug' => $experience->slug,
                    'rezdy_url' => $experience->rezdy_url,
                    'hero_image' => '/public/storage/' . $experience->hero_image,
                    'tags' => $experience->tags->map(fn($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ]),
                ];
            }),
            'magazines' => VentureMagazine::with('tags')->select(['id', 'title', 'hero_image', 'slug', 'is_published'])->where('is_published', 1)->get()->map(function ($magazine) {
                return [
                    'id' => $magazine->id,
                    'title' => $magazine->title,
                    'slug' => $magazine->slug,
                    'hero_image' => '/public/storage/' . $magazine->hero_image,
                    'tags' => $magazine->tags->map(fn($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ]),
                ];
            }),
            'tiles' => ExploreTile::select(['id', 'title', 'subtitle', 'image', 'link'])->where('is_active', true)->orderBy('sort_order')->get()->map(function ($tile) {
                return [
                    'id' => $tile->id,
                    'title' => $tile->title,
                    'subtitle' => $tile->subtitle,
                    'link' => $tile->link,
                    'image' => '/public/storage/' . $tile->image,
                ];
            }),
            'canLogin' => Route::has('login'),
            'magazinePopup' => $popup->magazine_content ?? '',
            // 'canRegister' => Route::has('register'),
        ]);
    }

    public function sitemap()
    {
        return Inertia::render('Frontend/SitemapPage', [
            'seo' => [
                'title' => 'Sitemap - Venture Up North',
                'description' => 'Explore the full structure of Venture Up North — from towns and experiences to blog posts and tours.',
                'image' => asset('/public/images/Venture-Up-North.png'),
                'canonical' => canonical_url(),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'towns' => Town::select('id', 'slug', 'name')->get(),
            'experiences' => Experience::select('id', 'slug', 'name')->get(),
            'posts' => VentureMagazine::select('id', 'slug', 'title')->get(),
            'tours' => TourTile::select('id', 'slug', 'title')->get(),
            'events' => Event::upcoming()->select('id', 'slug', 'name')->get(),
            'contents' => ThingsToDoCategory::select('id', 'slug', 'name')->get(),
            'content_listings' => ThingsToDoItem::with(['category:id,slug'])->select('id', 'slug', 'title','category_id')->get(),
            'collections' => Collection::select('id', 'slug', 'name')->get(),
        ]);
    }

    public function upcomingFeatures()
    {
        return Inertia::render('Frontend/Features', [
            'seo' => [
                'title' => 'Upcoming Features - Venture Up North',
                'description' => 'Preview what’s coming next on Venture Up North: trip planner, local events, social features, and more.',
                'image' => asset('/public/images/Venture-Up-North.png'),
                'canonical' => canonical_url(),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
        ]);
    }
}
