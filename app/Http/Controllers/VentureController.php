<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentureRequest;
use App\Models\Collection;
use App\Models\CollectionPageSetting;
use App\Models\Tag;
use App\Models\Venture;
use App\Models\VentureDay;
use App\Models\VentureItem;
use App\Notifications\VentureSubmittedForAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;


class VentureController extends Controller
{
    public function store(StoreVentureRequest $request)
    {
        $manager = new ImageManager(new Driver());

        $data = $request->validated();
        $user = auth()->user();
        $isAdmin = $user && $user->role == 'admin';
        // slug
        $base = Str::slug($data['title']);
        $slug = $base;
        $n = 2;
        while (Venture::where('slug', $slug)->exists()) $slug = $base . '-' . $n++;

        // mark approved immediately (per your instruction)

        $cookieName = 'venture_owner_token';

        if ($isAdmin) {
            // Admin-created
            $createdByAdminId = $user->id;
            $ownerUserId      = null;
            $ownerToken       = null;
            $guestName        = data_get($data, 'owner_guest.name');
            $guestEmail       =  data_get($data, 'owner_guest.email');
            $visibility = 'public';
            $status = 'approved';
        } elseif ($user) {
            // Logged-in non-admin user
            $createdByAdminId = null;
            $ownerUserId      = $user->id;
            $ownerToken       = null;
            $guestName        = data_get($data, 'owner_guest.name');
            $guestEmail       =  data_get($data, 'owner_guest.email');
            $visibility = 'private';
            $status = 'submitted';
        } else {
            // Guest
            // Guest
            $createdByAdminId = null;
            $ownerUserId      = null;

            // Check for existing cookie, otherwise create one
            $ownerToken = request()->cookie($cookieName);

            if (! $ownerToken) {
                $ownerToken = Str::random(40);
                Cookie::queue(cookie()->forever(
                    $cookieName,
                    $ownerToken,
                    path: '/',
                    domain: null,
                    secure: app()->environment('production'),
                    httpOnly: true,
                    sameSite: 'Lax'
                ));
            }
            $guestName        = data_get($data, 'owner_guest.name');
            $guestEmail       = data_get($data, 'owner_guest.email');
            $visibility = 'private';
            $status = 'submitted';
        }

        // Hero
        if ($request->hasFile('heroImage')) {
            $image = $manager->read($request->file('heroImage')->getPathname())
                ->cover(738, 500);

            $bytes = $image->toJpeg(85);
            $path  = 'ventures/hero/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $data['cover_image_url'] = $path;
        }

        // Social
        if ($request->hasFile('socialImage')) {
            $image = $manager->read($request->file('socialImage')->getPathname())
                ->cover(1200, 630);

            $bytes = $image->toJpeg(85);
            $path  = 'ventures/social/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $data['og_image_url'] = $path;
        }

        $venture = DB::transaction(function () use ($data, $slug, $visibility, $status, $ownerUserId, $guestName, $guestEmail, $ownerToken, $createdByAdminId) {
            $venture = Venture::create([
                'slug' => $slug,
                'title' => $data['title'],
                'cover_image_url' => $data['cover_image_url'] ?? null,
                'og_image_url' => $data['og_image_url'] ?? null,
                'summary' => $data['summary'] ?? null,
                'visibility' => $visibility,
                'status' => $status,
                'owner_user_id' => $ownerUserId,
                'owner_guest_name' => $guestName,
                'owner_guest_email' => $guestEmail,
                'created_by_admin_id' => $createdByAdminId,
                'owner_token' => $ownerToken,
                'published_at' => now(),
            ]);

            // Days
            $dayMap = [];
            $days = collect($data['days'] ?? [['title' => 'Day 1', 'index' => 1]])
                ->sortBy('index')
                ->values();

            foreach ($days as $d) {
                $day = VentureDay::create([
                    'venture_id' => $venture->id,
                    'title' => $d['title'],
                    'day_index' => (int)$d['index'],
                ]);
                $dayMap[$d['index']] = $day->id;
            }

            // Items
            $items = collect($data['items'] ?? [])->sortBy([['day_index', 'asc'], ['position', 'asc']])->values();
            foreach ($items as $it) {
                VentureItem::create([
                    'venture_id' => $venture->id,
                    'venture_day_id' => $dayMap[$it['day_index']] ?? null,
                    'position' => (int)$it['position'],
                    'item_type' => $it['item_type'],
                    'item_id' => (int)$it['item_id'],
                    'source_url' => $it['source_url'],
                    'cat_source_url' => $it['cat_source_url'],
                    'title' => $it['title'],
                    'image_url' => $it['image_url'] ?? null,
                    'tags' => $it['tags'] ?? null,
                    'lat' => $it['lat'] ?? null,
                    'lng' => $it['lng'] ?? null,
                    'payload' => $it['payload'] ?? null,
                ]);
            }

            // counts & snapshot
            $venture->items_count = $items->count();
            $venture->days_count = $days->count();
            $venture->data_snapshot = [
                'days' => $days,
                'items' => $items,
            ];
            $venture->save();

            if ($venture->status == 'submitted' && $venture->visibility == 'private') {
                $emails = config('notifications.admin_submission_recipients');

                foreach ($emails as $email) {
                    Notification::route('mail', $email)
                        ->notify(new VentureSubmittedForAdmin($venture));
                }
            }

            return $venture;
        });




        // Return show page
        if ($isAdmin) {
            // redirect to admin ventures index or admin show page
            return redirect()
                ->route('admin.ventures.ours.index')
                ->with('success', 'Venture created successfully by admin');
        } else {
            // guest or normal user flow
            $response = redirect()
                ->route('ventures.your', $venture->slug)
                ->with([
                    'success' => 'Venture saved successfully',
                    'clear_local' => true, 
                ]);

            if ($ownerToken) {
                $response->withCookie(cookie()->forever($cookieName, $ownerToken));
            }

            return $response;
        }
    }

    public function show($slug)
    {
        $venture = Venture::with(['days', 'items'])->where('slug', $slug)->firstOrFail();
        return Inertia::render('Ventures/Show', [
            'seo' => [
                'title' => $venture->seo_title,
                'description' => $venture->seo_description,
                'image' => '/public/storage/' . $venture->og_image_url,
                'canonical' => canonical_url('/ventures/' . $venture->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'venture' => $venture,
        ]);
    }


    public function editVenture(Request $request, $slug)
    {
        $venture = Venture::with(['days' => function ($q) {
            $q->orderBy('day_index');
        }, 'items' => function ($q) {
            $q->orderBy('position');
        }])->where('slug', $slug)->firstOrFail();

        // 🔐 owner check
        $cookieName  = 'venture_owner_token';
        $cookieToken = $request->cookie($cookieName);
        $user        = $request->user();

        $isOwner = false;
        if ($user && $venture->owner_user_id && $user->id === (int) $venture->owner_user_id) {
            $isOwner = true;
        } elseif ($cookieToken && $cookieToken === $venture->owner_token) {
            $isOwner = true;
        }

        if (! $isOwner) {
            return redirect()->route('ventures.show', $venture->slug)
                ->with('error', 'You are not allowed to edit this venture.');
        }

        // SEO image helper
        $seoImg = $venture->og_image_url
            ? (preg_match('~^https?://~i', $venture->og_image_url)
                ? $venture->og_image_url
                : '/public/storage/' . ltrim($venture->og_image_url, '/'))
            : null;

        return Inertia::render('Frontend/Venture/Edit', [
            'seo' => [
                'title'       => $venture->seo_title ?: 'Edit: ' . $venture->title,
                'description' => $venture->seo_description ?: str($venture->summary)->limit(160),
                'image'       => $seoImg,
                'canonical'   => canonical_url('/ventures/' . $venture->slug . '/edit'),
                'robots'      => 'noindex, nofollow',
                'type'        => 'website',
            ],
            'venture' => [
                'id'              => $venture->id,
                'slug'            => $venture->slug,
                'title'           => $venture->title,
                'summary'         => $venture->summary,
                'cover_image_url' => $venture->cover_image_url,
                'og_image_url'    => $venture->og_image_url,
                'visibility'      => $venture->visibility,
                'status'          => $venture->status,
                'items_count'     => $venture->items_count,
                'days_count'      => $venture->days_count,
                'owner_user_id'   => $venture->owner_user_id,
                'owner_guest_name' => $venture->owner_guest_name,
                'owner_token'     => $venture->owner_token,
                'created_by_admin_id' => $venture->created_by_admin_id,
                'days'  => $venture->days->map(fn($d) => [
                    'id'    => $d->id,
                    'title' => $d->title,
                    'index' => $d->day_index,
                ])->values(),
                'items' => $venture->items->map(fn($it) => [
                    'id'            => $it->id,
                    'item_type'     => $it->item_type,
                    'item_id'       => $it->item_id,
                    'title'         => $it->title,
                    'position'      => $it->position,
                    'venture_day_id' => $it->venture_day_id,
                    'source_url'    => $it->source_url,
                    'cat_source_url' => $it->cat_source_url,
                    'image_url'     => $it->image_url,
                    'tags'          => $it->tags,
                    'lat'           => $it->lat,
                    'lng'           => $it->lng,
                    'payload'       => $it->payload,
                    'day_index'     => optional($venture->days->firstWhere('id', $it->venture_day_id))->day_index,
                ])->values(),
            ],
        ]);
    }

    public function update(Request $request, $slug)
    {
        $venture = Venture::with(['days', 'items'])->where('slug', $slug)->firstOrFail();

        // 🔐 Owner check (logged-in user OR guest via cookie token)
        $cookieToken = $request->cookie('venture_owner_token');
        $user        = $request->user();

        $isOwner = false;
        if ($user && $venture->owner_user_id && $user->id === (int) $venture->owner_user_id) {
            $isOwner = true;
        } elseif ($cookieToken && $cookieToken === $venture->owner_token) {
            $isOwner = true;
        }
        if (! $isOwner) {
            return redirect()->route('ventures.show', $venture->slug)
                ->with('error', 'You are not allowed to edit this venture.');
        }

        // --- Decode JSON arrays coming from FormData ---
        $daysInput  = $request->input('days', '[]');
        $itemsInput = $request->input('items', '[]');

        $daysRaw  = is_string($daysInput)  ? json_decode($daysInput, true)  : $daysInput;
        $itemsRaw = is_string($itemsInput) ? json_decode($itemsInput, true) : $itemsInput;

        $daysRaw  = is_array($daysRaw)  ? $daysRaw  : [];
        $itemsRaw = is_array($itemsRaw) ? $itemsRaw : [];

        // --- Normalize days: force sequential index 1..N to avoid unique key collisions ---
        $normalizedDays = collect($daysRaw)
            ->filter(fn($d) => is_array($d) && !empty($d['title']))
            ->values()
            ->map(fn($d, $i) => [
                'title' => (string) $d['title'],
                'index' => $i + 1, // <— force 1..N
            ]);

        // Build map (newIndex => will be used) for items normalization
        $validDayIndexes = $normalizedDays->pluck('index')->all(); // [1..N]
        $maxDayIndex     = count($validDayIndexes);

        // --- Normalize items ---
        // 1) Clamp invalid/missing day_index into [1..N] (use last day if none)
        // 2) Reposition items per-day (positions 1..M for each day)
        $grouped = collect($itemsRaw)
            ->filter(fn($it) => is_array($it) && !empty($it['item_type']) && !empty($it['item_id']))
            ->map(function ($it) use ($maxDayIndex) {
                $di = (int) ($it['day_index'] ?? $maxDayIndex);
                $di = max(1, min($maxDayIndex ?: 1, $di)); // ensure within 1..N (or 1 if no days)
                return [
                    'item_type'      => (string) $it['item_type'],
                    'item_id'        => (int) ($it['item_id']),
                    'title'          => $it['title'] ?? null,
                    'day_index'      => $di,
                    'position'       => (int) ($it['position'] ?? 9999),
                    'source_url'     => $it['source_url']     ?? null,
                    'cat_source_url' => $it['cat_source_url'] ?? null,
                    'image_url'      => $it['image_url']      ?? null,
                    'tags'           => $it['tags']           ?? null,
                    'lat'            => $it['lat']            ?? null,
                    'lng'            => $it['lng']            ?? null,
                    'payload'        => $it['payload']        ?? null,
                ];
            })
            ->groupBy('day_index')
            ->map(function ($itemsForDay) {
                // sort by incoming position, then reassign 1..M
                $sorted = $itemsForDay->sortBy('position')->values();
                return $sorted->map(function ($it, $i) {
                    $it['position'] = $i + 1;
                    return $it;
                });
            });

        $normalizedItems = $grouped->flatten(1)
            ->sortBy([['day_index', 'asc'], ['position', 'asc']])
            ->values();

        // --- Validation ---
        // Scalar fields + files (file rules must use $request)
        // Make guest name conditional: if the venture already has owner_user_id, guest name is not required.
        $rules = [
            'title'             => ['required', 'string', 'max:160'],
            'owner_guest.email' => ['nullable', 'email'],
            'owner_user_id'     => ['nullable', 'integer', 'exists:users,id'],
            'visibility'        => ['nullable', 'in:public,unlisted,private'],
            'summary'           => ['nullable', 'string', 'max:500'],
            'heroImage'         => ['nullable', 'image', 'max:5120'],
            'socialImage'       => ['nullable', 'image', 'max:5120'],
        ];
        if ($venture->owner_user_id) {
            $rules['owner_guest.name'] = ['nullable', 'string', 'max:120'];
        } else {
            $rules['owner_guest.name'] = ['required', 'string', 'max:120'];
        }
        $validated = $request->validate($rules);

        // Arrays validation (on normalized data)
        Validator::make(
            [
                'days'  => $normalizedDays->all(),
                'items' => $normalizedItems->all(),
            ],
            [
                'days'                 => ['required', 'array', 'min:1'],
                'days.*.title'         => ['required', 'string', 'max:80'],
                'days.*.index'         => ['required', 'integer', 'min:1'],

                'items'                => ['array'],
                'items.*.day_index'    => ['required', 'integer', 'min:1'],
                'items.*.position'     => ['required', 'integer', 'min:1'],
                'items.*.item_type'    => ['required', 'in:event,experience,tour,listing,town'],
                'items.*.item_id'      => ['required', 'integer'],
                'items.*.source_url'   => ['nullable', 'string', 'max:2048'],
                'items.*.cat_source_url' => ['nullable', 'string', 'max:2048'],
                'items.*.title'        => ['nullable', 'string', 'max:200'],
                'items.*.image_url'    => ['nullable', 'string', 'max:2048'],
                'items.*.tags'         => ['nullable', 'array'],
                'items.*.lat'          => ['nullable', 'numeric'],
                'items.*.lng'          => ['nullable', 'numeric'],
                'items.*.payload'      => ['nullable', 'array'],
            ]
        )->validate();

        // --- Optional image processing ---
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        if ($request->hasFile('heroImage')) {
            $img   = $manager->read($request->file('heroImage')->getPathname())->cover(738, 500);
            $bytes = $img->toJpeg(85);
            $path  = 'ventures/hero/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $validated['cover_image_url'] = $path;
        }
        if ($request->hasFile('socialImage')) {
            $img   = $manager->read($request->file('socialImage')->getPathname())->cover(1200, 630);
            $bytes = $img->toJpeg(85);
            $path  = 'ventures/social/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $validated['og_image_url'] = $path;
        }
        // --- Persist atomically ---
        DB::transaction(function () use ($venture, $validated, $normalizedDays, $normalizedItems) {
            \App\Models\VentureDay::where('venture_id', $venture->id)->withTrashed()->forceDelete();
            \App\Models\VentureItem::where('venture_id', $venture->id)->withTrashed()->forceDelete();
            // Basic
            $venture->title   = $validated['title'];
            $venture->owner_guest_name  = $validated['owner_guest']['name'] ?? null;
            $venture->summary = $validated['summary'] ?? $venture->summary;
            if ($venture->visibility == 'public' && $venture->status == 'approved') {
                $venture->status =  'submitted';
                $venture->visibility =  'private';
            }
            if (isset($validated['visibility']))      $venture->visibility      = $validated['visibility'];
            if (isset($validated['cover_image_url'])) $venture->cover_image_url = $validated['cover_image_url'];
            if (isset($validated['og_image_url']))    $venture->og_image_url    = $validated['og_image_url'];

            // Rebuild days with forced 1..N index to satisfy unique (venture_id, day_index)
            $dayMap = [];
            foreach ($normalizedDays as $d) {
                $day = VentureDay::create([
                    'venture_id' => $venture->id,
                    'title'      => $d['title'],
                    'day_index'  => (int) $d['index'],
                ]);
                $dayMap[$d['index']] = $day->id;
            }

            // Rebuild items; positions already normalized per-day
            foreach ($normalizedItems as $it) {
                VentureItem::create([
                    'venture_id'      => $venture->id,
                    'venture_day_id'  => $dayMap[$it['day_index']] ?? null,
                    'position'        => (int) $it['position'],
                    'item_type'       => $it['item_type'],
                    'item_id'         => (int) $it['item_id'],
                    'source_url'      => $it['source_url']     ?? null,
                    'cat_source_url'  => $it['cat_source_url'] ?? null,
                    'title'           => $it['title']          ?? null,
                    'image_url'       => $it['image_url']      ?? null,
                    'tags'            => $it['tags']           ?? null,
                    'lat'             => $it['lat']            ?? null,
                    'lng'             => $it['lng']            ?? null,
                    'payload'         => $it['payload']        ?? null,
                ]);
            }

            $venture->items_count   = count($normalizedItems);
            $venture->days_count    = count($normalizedDays);
            $venture->data_snapshot = [
                'days'  => array_values($normalizedDays->all()),
                'items' => array_values($normalizedItems->all()),
            ];
            $venture->save();

            $emails = config('notifications.admin_submission_recipients');

            foreach ($emails as $email) {
                Notification::route('mail', $email)
                    ->notify(new VentureSubmittedForAdmin($venture));
            }
        });

        return redirect()
            ->route('ventures.your', $venture->slug)
            ->with('success', 'Venture updated successfully.');
    }



    public function your(Request $req)
    {


        $q = Venture::query()
            ->withCount(['items as items_count'])
            ->select(['id', 'slug', 'title', 'cover_image_url', 'og_image_url', 'summary', 'status', 'visibility', 'owner_user_id', 'owner_guest_name', 'owner_token', 'created_at', 'published_at', 'created_by_admin_id']);

        if ($req->user()) {
            // logged-in: show owned + also those saved from this browser token
            $token = $req->cookie('venture_owner_token');
            $q->where(function ($w) use ($req, $token) {
                $w->where('owner_user_id', $req->user()->id);
                if ($token) $w->orWhere('owner_token', $token);
            });
        } else {
            // guest: show by cookie token only
            $token = $req->cookie('venture_owner_token');
            $q->where('owner_token', $token ?: '__none__');
        }

        // optional search & filters
        if ($s = trim($req->get('q', ''))) {
            $q->where(function ($w) use ($s) {
                $w->where('title', 'like', "%$s%")
                    ->orWhere('summary', 'like', "%$s%");
            });
        }
        if ($min = (int)$req->get('min_days')) $q->where('days_count', '>=', $min);
        if ($max = (int)$req->get('max_days')) $q->where('days_count', '<=', $max);

        // simple sort options
        $sort = $req->get('sort', 'recent');
        if ($sort === 'popular') $q->orderByDesc('items_count');
        else $q->orderByDesc('created_at');

        $ventures = $q->orderByDesc('created_at')->paginate(12)->withQueryString();

        return Inertia::render('Ventures/Your', [
            'ventures' => $ventures,
            'filters'  => [
                'q' => $s ?? '',
                'min_days' => $req->get('min_days'),
                'max_days' => $req->get('max_days'),
                'sort' => $sort,
            ],
        ]);
    }

    public function ours(Request $req)
    {
        // $q = Venture::query()
        //     ->whereNotNull('created_by_admin_id')
        //     ->whereIn('status', ['approved', 'published'])
        //     ->orderByDesc('published_at')
        //     ->withCount('items')
        //     ->select([
        //         'id',
        //         'slug',
        //         'title',
        //         'summary',
        //         'cover_image_url',
        //         'og_image_url',
        //         'status',
        //         'items_count',
        //         'owner_user_id',
        //         'owner_guest_name',
        //         'created_at',
        //         'days_count'
        //     ]);

        // // optional search & filters
        // if ($s = trim($req->get('q', ''))) {
        //     $q->where(function ($w) use ($s) {
        //         $w->where('title', 'like', "%$s%")
        //             ->orWhere('summary', 'like', "%$s%");
        //     });
        // }
        // if ($min = (int)$req->get('min_days')) $q->where('days_count', '>=', $min);
        // if ($max = (int)$req->get('max_days')) $q->where('days_count', '<=', $max);

        // // simple sort options
        // $sort = $req->get('sort', 'recent');
        // if ($sort === 'popular') $q->orderByDesc('items_count');
        // else $q->orderByDesc('created_at');

        // $ventures = $q->paginate(12)->withQueryString();


        // return Inertia::render('Ventures/Ours', [
        //     'ventures' => $ventures,
        //     'filters'  => [
        //         'q' => $s ?? '',
        //         'min_days' => $req->get('min_days'),
        //         'max_days' => $req->get('max_days'),
        //         'sort' => $sort,
        //     ],
        // ]);



        $collectionSettings = CollectionPageSetting::firstOrFail();

        // Base query
        $q = Collection::query()
            ->where('is_active', 1)
            ->where('type', 'venture')
            ->with(['tags:id,name'])
            ->select([
                'id',
                'slug',
                'name',
                'summary',
                'hero_image',
                'big_hero_image',
                'type',
                'created_at',
            ]);

        // Search
        if ($s = trim($req->get('q', ''))) {
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                    ->orWhere('summary', 'like', "%{$s}%");
            });
        }

        // Optional tag filters (?tag_ids[]=1&tag_ids[]=2)
        $tagIds = collect($req->get('tag_ids', []))->filter()->values()->all();
        if (!empty($tagIds)) {
            $q->whereHas('tags', fn($w) => $w->whereIn('tags.id', $tagIds));
        }

        // Sort
        $sort = $req->get('sort', 'recent'); // recent|popular
        if ($sort === 'popular') {
            // Only if you have an items() relation:
            $q->withCount('items')->orderByDesc('items_count');
        } else {
            $q->orderByDesc('created_at');
        }

        // Paginate & keep query string
        $collections = $q->paginate(12)->withQueryString();

        // Tags used by these paginated collections
        $collectionIds = $collections->getCollection()->pluck('id');
        $usedTagIds = DB::table('taggables')
            ->whereIn('taggable_id', $collectionIds)
            ->where('taggable_type', Collection::class)
            ->pluck('tag_id')
            ->unique();

        $tags = Tag::whereIn('id', $usedTagIds)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Transform each item for the frontend while preserving pagination
        $items = $collections->through(function (Collection $c) {
            return [
                'id' => $c->id,
                'title' => $c->name,
                'summary' => $c->summary,
                'slug' => $c->slug,
                'cover_image_url' => $c->hero_image ? '/public/storage/' . $c->hero_image : null,
                'big_hero_image'  => $c->big_hero_image ? '/public/storage/' . $c->big_hero_image : null,
                'type' => $c->type,
                'tags' => $c->tags?->map(fn($t) => ['id' => $t->id, 'name' => $t->name])->all() ?? [],
                'created_at' => $c->created_at,
            ];
        });

        return Inertia::render('Ventures/Ours', [
            'seo' => [
                'title' => $collectionSettings->seo_title,
                'description' => $collectionSettings->seo_description,
                'image' => '/public/storage/' . $collectionSettings->seo_image,
                'canonical' => canonical_url('/collections/'),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'collection' => [
                'id' => $collectionSettings->id,
                'name' => $collectionSettings->name,
                'summary' => $collectionSettings->summary,
                'hero_image' => '/public/storage/' . $collectionSettings->hero_image,
                'big_hero_image' => '/public/storage/' . $collectionSettings->big_hero_image,
                'description' => $collectionSettings->description,
                'seo_title' => $collectionSettings->seo_title,
                'seo_description' => $collectionSettings->seo_description,
                'seo_image' => $collectionSettings->seo_image,
            ],
            'tags' => $tags,
            'ventures' => $items, // paginated, transformed
            'filters' => [
                'q' => $s ?? '',
                'tag_ids' => $tagIds,
                'sort' => $sort,
            ],
        ]);
    }

    public function community(Request $req)
    {
        $q = Venture::query()
            ->whereNull('created_by_admin_id')
            ->whereIn('status', ['approved', 'published'])
            ->where('visibility', 'public')
            ->withCount('items')
            ->select([
                'id',
                'slug',
                'title',
                'summary',
                'cover_image_url',
                'og_image_url',
                'status',
                'items_count',
                'owner_user_id',
                'owner_guest_name',
                'created_at',
                'days_count'
            ]);

        // optional search & filters
        if ($s = trim($req->get('q', ''))) {
            $q->where(function ($w) use ($s) {
                $w->where('title', 'like', "%$s%")
                    ->orWhere('summary', 'like', "%$s%");
            });
        }
        if ($min = (int)$req->get('min_days')) $q->where('days_count', '>=', $min);
        if ($max = (int)$req->get('max_days')) $q->where('days_count', '<=', $max);

        // simple sort options
        $sort = $req->get('sort', 'recent');
        if ($sort === 'popular') $q->orderByDesc('items_count');
        else $q->orderByDesc('created_at');

        $ventures = $q->paginate(12)->withQueryString();

        return Inertia::render('Ventures/Community', [
            'ventures' => $ventures,
            'filters'  => [
                'q' => $s ?? '',
                'min_days' => $req->get('min_days'),
                'max_days' => $req->get('max_days'),
                'sort' => $sort,
            ],
        ]);
    }
}
