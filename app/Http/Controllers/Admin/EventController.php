<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Requests\UpdateTownRequest;
use App\Models\Event;
use App\Models\EventPageSetting;
use App\Models\Tag;
use App\Models\TermsConditionEvents;
use App\Models\Town;
use App\Notifications\EventApprovedNotification;
use App\Notifications\EventRejected;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::query();

        // 🔎 search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $query->where(function ($q) {
            $q->whereNull('user_id')
                ->orWhereDoesntHave('user.contributor');
        });

        $events = $query
            ->with('towns')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
            'filters' => $request->only('search'),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Events/Create', [
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'towns' => Town::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('hero_image')) {
            $validated['hero_image'] = $request->file('hero_image')->store('events/hero', 'public');
        }

        if ($request->hasFile('big_hero_image')) {
            $validated['big_hero_image'] = $request->file('big_hero_image')->store('events/big_hero', 'public');
        }

        if ($request->hasFile('seo_image')) {
            $validated['seo_image'] = $request->file('seo_image')->store('events/seo', 'public');
        }

        // ✅ Generate slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $i = 1;

        while (Event::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . Str::random(5);
        }

        $validated['slug'] = $slug;

        $validated['content'] = json_encode($validated['content']);
        $validated['opening_times'] = json_encode($validated['opening_times']);

        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();

        $validated['start_date'] = $startDate;
        $validated['end_date'] = $endDate;

        $event = Event::create($validated);

        $event->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());
        $event->towns()->sync(collect($request->input('town_ids', []))->pluck('id'));

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $event->load('tags', 'towns');

        $terms = TermsConditionEvents::for('contributor_submission');
        $enabledBoxes = collect($terms?->boxes ?? [])
            ->filter(fn($b) => ($b['enabled'] ?? false))
            ->values();

        $agreementsRaw = (array) ($event->agreements ?? []);
        $agreements = [];

        foreach ($enabledBoxes as $i => $box) {
            if (!array_key_exists($i, $agreementsRaw)) {
                continue;
            }

            $agreements[] = [
                'index'    => $i,
                'label'    => $box['label'] ?? "Agreement #{$i}",
                'required' => (bool) ($box['required'] ?? false),
                'accepted' => (bool) $agreementsRaw[$i],
            ];
        }

        return Inertia::render('Admin/Events/Edit', [
            'event' => $event,
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'towns' => Town::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
            'agreements' => $agreements,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $validated = $request->validated();

        if ($request->hasFile('hero_image')) {
            if ($event->hero_image) {
                Storage::disk('public')->delete($event->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')->store('events/hero', 'public');
        } else {
            $validated['hero_image'] = $event->hero_image;
        }

        if ($request->hasFile('big_hero_image')) {
            if ($event->big_hero_image) {
                Storage::disk('public')->delete($event->big_hero_image);
            }
            $validated['big_hero_image'] = $request->file('big_hero_image')->store('events/big_hero', 'public');
        } else {
            $validated['big_hero_image'] = $event->big_hero_image;
        }

        if ($request->hasFile('seo_image')) {
            if ($event->seo_image) {
                Storage::disk('public')->delete($event->seo_image);
            }
            $validated['seo_image'] = $request->file('seo_image')->store('events/seo', 'public');
        } else {
            $validated['seo_image'] = $event->seo_image;
        }

        // Update slug if name changed
        if ($event->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // 🔒 Ensure content is stored as JSON
        $validated['content'] = json_encode($validated['content']);
        $validated['opening_times'] = json_encode($validated['opening_times']);

        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();

        $validated['start_date'] = $startDate;
        $validated['end_date'] = $endDate;


        $event->update($validated);

        $event->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());
        $event->towns()->sync(collect($request->input('town_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        if ($event->hero_image) {
            Storage::disk('public')->delete($event->hero_image);
        }

        if ($event->seo_image) {
            Storage::disk('public')->delete($event->seo_image);
        }

        if ($event->big_hero_image) {
            Storage::disk('public')->delete($event->big_hero_image);
        }

        $event->delete();

        return back();
    }

    public function toggleStatus(Event $event)
    {
        $event->update(['is_active' => !$event->is_active]);

        return back();
    }

    public function editSetting()
    {
        $setting = EventPageSetting::firstOrCreate([]);
        return Inertia::render('Admin/Events/PageSettings', [
            'setting' => $setting,
        ]);
    }

    public function updateSetting(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'summary' => 'required|string|max:500',
            'description' => 'required|array',
            'description.blocks' => 'required|array|min:1',
            'hero_image' => 'required|image|max:2048|dimensions:min_width=738,min_height=500',
            'big_hero_image' => 'required|image|max:5120|dimensions:min_width=1200,min_height=600',
            'seo_image' => 'nullable|image|max:200|dimensions:min_width=1200,min_height=630',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
        ]);

        $setting = EventPageSetting::first();

        if ($request->hasFile('hero_image')) {
            if ($setting->hero_image) {
                Storage::disk('public')->delete($setting->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('events_setting/hero', 'public');
        } else {
            $data['hero_image'] = $setting->hero_image;
        }

        if ($request->hasFile('big_hero_image')) {
            if ($setting->big_hero_image) {
                Storage::disk('public')->delete($setting->big_hero_image);
            }
            $data['big_hero_image'] = $request->file('big_hero_image')->store('events_setting/big_hero', 'public');
        } else {
            $data['big_hero_image'] = $setting->big_hero_image;
        }

        if ($request->hasFile('seo_image')) {
            if ($setting->seo_image) {
                Storage::disk('public')->delete($setting->seo_image);
            }
            $data['seo_image'] = $request->file('seo_image')->store('events_setting/seo_image', 'public');
        } else {
            $data['seo_image'] = $setting->seo_image;
        }

        $setting->update($data);

        return redirect()->back()->with('success', 'Event page settings updated.');
    }

    public function showReviewEvent($slug)
    {
        // Get the listing within that category
        $currentEvent = Event::with(['towns', 'tags'])
            ->where('slug', $slug)
            ->where('status', 'pending')
            ->firstOrFail();

        return Inertia::render('Admin/Events/Contributors/Review', [
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
                'status' => $currentEvent->status,
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

    public function contributors(Request $request)
    {
        $query = Event::query()
            ->with(['user.contributor', 'towns'])
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->whereHas('user.contributor');

        // optional search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // same status ordering as magazines, then newest
        $query->orderByRaw("
        CASE
            WHEN status = 'pending'  THEN 0
            WHEN status = 'approved' THEN 1
            WHEN status = 'rejected' THEN 2
            ELSE 3
        END
    ")->orderByDesc('created_at');

        $events = $query->paginate(15)->withQueryString();

        return inertia('Admin/Events/Contributors/Index', [
            'events'  => $events,
            'filters' => $request->only('search'),
        ]);
    }

    public function approve(Request $request, Event $event)
    {
        $request->validate(['note' => 'nullable|string|max:1000']);
        // apply pending_payload if present, set status approved & published_at, notify contributor
        $event->approve($request->note, auth()->id()); // if you added the trait method

        if ($event->user && $event->user->contributor) {
            $event->user->notify(new EventApprovedNotification($event,$request->note));
        }
        return  redirect()
            ->route('admin.events.contributors')->with('success', 'Approved and published.');
    }

    public function reject(Request $request, Event $event)
    {
        $request->validate(['reason' => 'required|string|max:1000']);
        $event->reject($request->reason, auth()->id()); // clears pending_payload, sets status rejected, notify
        // Send notification to contributor's user account
        if ($event->user && $event->user->contributor) {
            $event->user->notify(new EventRejected($event, $request->reason));
        }

        return redirect()
            ->route('admin.events.contributors')->with('success', 'Rejected. Contributor notified.');
    }
}
