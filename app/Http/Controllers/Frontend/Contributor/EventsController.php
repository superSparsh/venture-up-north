<?php

namespace App\Http\Controllers\Frontend\Contributor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contributors\StoreEventRequest;
use App\Http\Requests\Contributors\UpdateEventRequest;
use App\Models\Event;
use App\Models\Tag;
use App\Models\TermsCondition;
use App\Models\TermsConditionEvents;
use App\Models\Town;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EventSubmittedForAdmin;
use App\Notifications\EventReSubmittedForAdmin;
use App\Notifications\EventSubmissionReceived;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->contributor) {
            abort(403, 'Unauthorized');
        }

        $query = Event::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $events = $query->with(['towns'])->where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Frontend/Event/Contributor/Index', [
            'events' => $events,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        $terms = TermsConditionEvents::for('contributor_submission');

        $safeTerms = $terms ? [
            'body'      => (string) $terms->body,
            'is_active' => (bool) $terms->is_active,
            'boxes'     => collect($terms->boxes ?? [])
                ->filter(fn($b) => is_array($b))
                ->map(function ($b) {
                    return [
                        'label'    => isset($b['label']) ? (string) $b['label'] : '',
                        'enabled'  => (bool) ($b['enabled'] ?? false),
                        'required' => (bool) ($b['required'] ?? false),
                    ];
                })
                ->values()
                ->all(),
        ] : null;

        return Inertia::render('Frontend/Event/Contributor/Create', [
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'towns' => Town::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
            'terms'        => $safeTerms,
        ]);
    }

    public function store(StoreEventRequest $request)
    {
        $manager = new ImageManager(new Driver());

        $validated = $request->validated();

        // Hero
        if ($request->hasFile('hero_image')) {
            $image = $manager->read($request->file('hero_image')->getPathname())
                ->cover(738, 500);

            $bytes = $image->toJpeg(85);
            $path  = 'events/hero/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $validated['hero_image'] = $path;
        }

        // big hero 
        if ($request->hasFile('big_hero_image')) {
            $image = $manager->read($request->file('big_hero_image')->getPathname())
                ->cover(1200, 630);

            $bytes = $image->toJpeg(85);
            $path  = 'events/big_hero/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $validated['big_hero_image'] = $path;
        }

        // Seo 
        if ($request->hasFile('seo_image')) {
            $image = $manager->read($request->file('seo_image')->getPathname())
                ->cover(1200, 630);

            $bytes = $image->toJpeg(85);
            $path  = 'events/seo/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $validated['seo_image'] = $path;
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
        $validated['opening_times'] = json_encode($validated['opening_times'] ?? []);

        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();

        $validated['start_date'] = $startDate;
        $validated['end_date'] = $endDate;
        $validated['published_at'] = null;
        $validated['submitted_by'] = auth()->id();
        $validated['user_id'] = auth()->id();
        $validated['status'] = $request->status ?? 'draft';
        $validated['agreements'] = $request->agreements ?? [];
        $event = Event::create($validated);

        $event->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());
        $event->towns()->sync(collect($request->input('town_ids', []))->pluck('id'));

        $emails = config('notifications.admin_submission_recipients');

        if ($request->status == 'pending') {
            foreach ($emails as $email) {
                Notification::route('mail', $email)
                    ->notify(new EventSubmittedForAdmin($event, auth()->user()));
            }
            // notify Contributor (the logged-in user)
            auth()->user()->notify(new EventSubmissionReceived($event));
        }

        return redirect()->route('contributor.events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        $terms = TermsConditionEvents::for('contributor_submission');

        $safeTerms = $terms ? [
            'body'      => (string) $terms->body,
            'is_active' => (bool) $terms->is_active,
            'boxes'     => collect($terms->boxes ?? [])
                ->filter(fn($b) => is_array($b))
                ->map(function ($b) {
                    return [
                        'label'    => isset($b['label']) ? (string) $b['label'] : '',
                        'enabled'  => (bool) ($b['enabled'] ?? false),
                        'required' => (bool) ($b['required'] ?? false),
                    ];
                })
                ->values()
                ->all(),
        ] : null;

        $user = auth()->user();
        $userId = $user?->id;
        $isOwner = $event->user_id
            && $event->user_id == $userId;

        if (! $isOwner) {
            abort(404);
        }

        if ($event->status == 'pending' || $event->status == 'approved') {
            abort(403, 'This event has already been submitted for review or published and cannot be edited.');
        }

        $event->load('tags', 'towns');

        return Inertia::render('Frontend/Event/Contributor/Edit', [
            'event' => $event,
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'towns' => Town::where('is_active', 1)->select('id', 'name')->orderBy('name')->get(),
            'terms' => $safeTerms
        ]);
    }


    public function update(UpdateEventRequest $request, Event $event)
    {
        $manager = new ImageManager(new Driver());

        $user = auth()->user();
        $userId = $user?->id;
        $isOwner = $event->user_id
            && $event->user_id == $userId;

        if (! $isOwner) {
            abort(404);
        }

        if ($event->status == 'pending' || $event->status == 'approved') {
            abort(403, 'This event has already been submitted for review or published and cannot be edited.');
        }

        $validated = $request->validated();


        // Hero
        if ($request->hasFile('hero_image')) {
            if ($event->hero_image) {
                Storage::disk('public')->delete($event->hero_image);
            }
            $image = $manager->read($request->file('hero_image')->getPathname())
                ->cover(738, 500);

            $bytes = $image->toJpeg(85);
            $path  = 'events/hero/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $validated['hero_image'] = $path;
        } else {
            $validated['hero_image'] = $event->hero_image;
        }

        // big hero 
        if ($request->hasFile('big_hero_image')) {
            if ($event->big_hero_image) {
                Storage::disk('public')->delete($event->big_hero_image);
            }
            $image = $manager->read($request->file('big_hero_image')->getPathname())
                ->cover(1200, 630);

            $bytes = $image->toJpeg(85);
            $path  = 'events/big_hero/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $validated['big_hero_image'] = $path;
        } else {
            $validated['big_hero_image'] = $event->big_hero_image;
        }

        // Seo 
        if ($request->hasFile('seo_image')) {
            if ($event->seo_image) {
                Storage::disk('public')->delete($event->seo_image);
            }
            $image = $manager->read($request->file('seo_image')->getPathname())
                ->cover(1200, 630);

            $bytes = $image->toJpeg(85);
            $path  = 'events/seo/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $validated['seo_image'] = $path;
        } else {
            $validated['seo_image'] = $event->seo_image;
        }

        // Update slug if name changed
        if ($event->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // 🔒 Ensure content is stored as JSON
        $validated['content'] = json_encode($validated['content']);
        $validated['opening_times'] = json_encode($validated['opening_times'] ?? []);

        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();

        $validated['start_date'] = $startDate;
        $validated['end_date'] = $endDate;

        $validated['published_at'] = null;
        $validated['submitted_by'] = auth()->id();
        $validated['status'] = $request->status ?? 'draft';
        $validated['user_id'] = auth()->id();
        $validated['agreements'] = $request->agreements ?? [];
        $event->update($validated);

        $event->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());
        $event->towns()->sync(collect($request->input('town_ids', []))->pluck('id')->toArray());

        $emails = config('notifications.admin_submission_recipients');


        if ($request->status == 'pending') {
            foreach ($emails as $email) {
                Notification::route('mail', $email)
                    ->notify(new EventReSubmittedForAdmin($event, auth()->user()));
            }
            // notify Contributor (the logged-in user)
            auth()->user()->notify(new EventSubmissionReceived($event));
        }

        return redirect()->route('contributor.events.index')->with('success', 'Event updated successfully.');
    }

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
}
