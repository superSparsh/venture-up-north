<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventPageSetting;
use App\Models\SitePopup;
use App\Models\Tag;
use App\Models\Town;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DB;

class EventController extends Controller
{
    public function showEvent($slug)
    {
        // Get the listing within that category
        $currentEvent = Event::upcoming()->with(['towns', 'tags'])
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        return Inertia::render('Frontend/Event/Show', [
            'seo' => [
                'title' => $currentEvent->seo_title,
                'description' => $currentEvent->seo_description,
                'image' => '/public/storage/' . $currentEvent->seo_image,
                'canonical' => canonical_url('/event/' . $currentEvent->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'event' => [
                'id' => $currentEvent->id,
                'name' => $currentEvent->name,
                'slug' => $currentEvent->slug,
                'summary' => $currentEvent->summary,
                'hero_image' => '/public/storage/' . $currentEvent->hero_image,
                'big_hero_image' => '/public/storage/' . $currentEvent->big_hero_image,
                'description' => $currentEvent->content,
                'opening_times' => $currentEvent->opening_times,
                'address' => $currentEvent->address,
                'phone_number' => $currentEvent->phone_number,
                'email' => $currentEvent->email,
                'custom_fields' => $currentEvent->custom_fields,
                'custom_buttons' => $currentEvent->custom_buttons,
                'social_links' => $currentEvent->social_links,
                'seo_title' => $currentEvent->seo_title,
                'seo_description' => $currentEvent->seo_description,
                'seo_image' => $currentEvent->seo_image,
                'location' => $currentEvent->location,
                'video' => $currentEvent->video,
                'event_date_label' => $currentEvent->event_date_label,
                'start_date' => $currentEvent->start_date,
                'end_date' => $currentEvent->end_date,
                'towns' => $currentEvent->towns->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]),
                'tags' => $currentEvent->tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]),
            ],
        ]);
    }


    public function events()
    {
        $eventSettings = EventPageSetting::firstOrFail();
        $popup = SitePopup::single();

        $events = Event::upcoming()->with(['tags', 'towns'])
            ->where('is_active', 1)
            ->latest()
            ->distinct()
            ->get();

        // Get all item IDs in this category
        $eventIds = $events->pluck('id');

        // Get tag IDs used by these listings from the taggables table
        $usedTagIds = DB::table('taggables')
            ->whereIn('taggable_id', $eventIds)
            ->where('taggable_type', Event::class) // Ensure this matches your actual stored class name
            ->pluck('tag_id')
            ->unique();

        // Fetch tag details
        $tags = Tag::whereIn('id', $usedTagIds)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Frontend/Event/Index', [
            'seo' => [
                'title' => $eventSettings->seo_title,
                'description' => $eventSettings->seo_description,
                'image' => '/public/storage/' . $eventSettings->seo_image,
                'canonical' => canonical_url('/events/'),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'event' => [
                'id' => $eventSettings->id,
                'name' => $eventSettings->name,
                'summary' => $eventSettings->summary,
                'hero_image' => '/public/storage/' . $eventSettings->hero_image,
                'big_hero_image' => '/public/storage/' . $eventSettings->big_hero_image,
                'description' => $eventSettings->description,
                'seo_title' => $eventSettings->seo_title,
                'seo_description' => $eventSettings->seo_description,
                'seo_image' => $eventSettings->seo_image,
            ],
            'tags' => $tags,
            'towns' => Town::where('is_active', 1)->orderBy('name')->get(['id', 'name', 'slug']),
            'initialItems' => $events,
            'eventPopup' => $popup->events_content ?? '',
        ]);
    }

    public function eventsFetch(Request $request)
    {

        $query = Event::upcoming()->with(['tags',  'towns'])
            ->where('is_active', 1)
            ->latest()
            ->distinct();

        if ($request->filled('town_ids')) {
            $query->whereHas('towns', function ($q) use ($request) {
                $q->whereIn('towns.id', $request->town_ids);
            });
        }

        // if ($request->filled('month')) {
        //     $query->whereIn(DB::raw('MONTHNAME(created_at)'), $request->month);
        // }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tags')) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $request->tags));
        }

        return response()->json($query->paginate(10));
    }
}
