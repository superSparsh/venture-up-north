<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\VentureMagazine;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreVentureMagazineRequest;
use App\Http\Requests\UpdateVentureMagazineRequest;
use App\Models\Experience;
use App\Models\Tag;
use App\Models\TermsCondition;
use App\Models\TourClick;
use App\Models\TourTile;
use App\Models\Town;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MagazineRejected;
use App\Notifications\MagazineApprovedNotification;

class VentureMagazineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VentureMagazine::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        $magazines = $query->with('contributor')
            ->whereNotNull('contributor_id')
            ->orderByDesc('published_at')
            ->paginate(10)->withQueryString();

        return Inertia::render('Admin/VentureMagazines/Index', [
            'magazines' => $magazines,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/VentureMagazines/Create', [
            'contributors' => TeamMember::select('id', 'name')->get(),
            'towns' => Town::select('id', 'name')->get(),
            'experiences' => Experience::select('id', 'name')->get(),
            'tourTiles' => TourTile::select('id', 'title')->get(),
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVentureMagazineRequest $request)
    {
        $data = $request->validated();

        // ✅ Generate slug
        $slug = Str::slug($data['title']);
        $originalSlug = $slug;
        $i = 1;

        while (VentureMagazine::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . Str::random(5);
        }

        $data['slug'] = $slug;

        $data['content'] = json_encode($data['content']);

        // Upload images
        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $request->file('hero_image')->store('magazine/hero', 'public');
        }

        if ($request->hasFile('big_hero_image')) {
            $data['big_hero_image'] = $request->file('big_hero_image')->store('magazine/big_hero', 'public');
        }

        if ($request->hasFile('seo_image')) {
            $data['seo_image'] = $request->file('seo_image')->store('magazine/seo', 'public');
        }

        $data['published_at'] = $request->boolean('is_published') ? now() : null;

        $magazine = VentureMagazine::create($data);

        $magazine->towns()->sync($request->town_ids);
        $magazine->experiences()->sync($request->experience_ids);
        $magazine->tourTiles()->sync($request->tour_tile_ids);
        $magazine->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.venture-magazines.index')->with('success', 'Magazine post created successfully!.');
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
    public function edit(VentureMagazine $ventureMagazine)
    {
        $terms = TermsCondition::for('contributor_submission');
        $enabledBoxes = collect($terms?->boxes ?? [])
            ->filter(fn($b) => ($b['enabled'] ?? false))
            ->values();

        $agreementsRaw = (array) ($ventureMagazine->agreements ?? []);
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
    
        return Inertia::render('Admin/VentureMagazines/Edit', [
            'magazine' => array_merge(
                $ventureMagazine->toArray(),
                [
                    'town_ids' => $ventureMagazine->towns()->select('id', 'name')->get(),
                    'experience_ids' => $ventureMagazine->experiences()->select('id', 'name')->get(),
                    'tour_tile_ids' => $ventureMagazine->tourTiles()->select('id', 'title')->get(),
                    'tag_ids' => $ventureMagazine->tags()->select('tags.id', 'tags.name')->get(),
                ]
            ),
            'contributors' => TeamMember::select('id', 'name')->get(),
            'towns' => Town::select('id', 'name')->get(),
            'experiences' => Experience::select('id', 'name')->get(),
            'tourTiles' => TourTile::select('id', 'title')->get(),
            'tags' => Tag::where('is_active', 1)->orderBy('name')->get(),
            'agreements' => $agreements,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVentureMagazineRequest $request, VentureMagazine $ventureMagazine)
    {
        $data = $request->validated();

        // 🔁 Update slug if title changed
        if ($data['title'] !== $ventureMagazine->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;

            while (VentureMagazine::where('slug', $slug)->where('id', '!=', $ventureMagazine->id)->exists()) {
                $slug = $originalSlug . '-' . Str::random(5);
            }

            $data['slug'] = $slug;
        }

        // 🖼️ Handle hero image upload
        if ($request->hasFile('hero_image')) {
            if ($ventureMagazine->hero_image) {
                Storage::disk('public')->delete($ventureMagazine->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('magazine/hero', 'public');
        } else {
            $data['hero_image'] = $ventureMagazine->hero_image;
        }

        // 🖼️ Handle social image upload
        if ($request->hasFile('big_hero_image')) {
            if ($ventureMagazine->big_hero_image) {
                Storage::disk('public')->delete($ventureMagazine->big_hero_image);
            }
            $data['big_hero_image'] = $request->file('big_hero_image')->store('magazine/big_hero', 'public');
        } else {
            $data['big_hero_image'] = $ventureMagazine->big_hero_image;
        }

        // 🖼️ Handle SEO image upload
        if ($request->hasFile('seo_image')) {
            if ($ventureMagazine->seo_image) {
                Storage::disk('public')->delete($ventureMagazine->seo_image);
            }
            $data['seo_image'] = $request->file('seo_image')->store('magazine/seo', 'public');
        } else {
            $data['seo_image'] = $ventureMagazine->seo_image;
        }

        // 🕐 Publish time
        $data['published_at'] = $request->boolean('is_published') ? now() : null;

        // 🔒 Ensure content is stored as JSON
        $data['content'] = json_encode($data['content']);


        $ventureMagazine->update($data);
        $ventureMagazine->towns()->sync(collect($request->town_ids)->pluck('id')->toArray());
        $ventureMagazine->experiences()->sync(collect($request->experience_ids)->pluck('id')->toArray());
        $ventureMagazine->tourTiles()->sync(collect($request->tour_tile_ids)->pluck('id')->toArray());
        $ventureMagazine->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());

        return redirect()
            ->route('admin.venture-magazines.index')
            ->with('success', 'Magazine post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VentureMagazine $ventureMagazine)
    {
        if ($ventureMagazine->hero_image) {
            Storage::disk('public')->delete($ventureMagazine->hero_image);
        }

        if ($ventureMagazine->seo_image) {
            Storage::disk('public')->delete($ventureMagazine->seo_image);
        }

        if ($ventureMagazine->big_hero_image) {
            Storage::disk('public')->delete($ventureMagazine->big_hero_image);
        }

        $ventureMagazine->delete();

        return back();
    }

    public function toggleFeatured(VentureMagazine $ventureMagazine)
    {
        if (!$ventureMagazine->is_featured) {
            VentureMagazine::where('is_featured', true)->update(['is_featured' => false]);
            $ventureMagazine->update(['is_featured' => true]);
        } else {
            $ventureMagazine->update(['is_featured' => false]);
        }

        return back();
    }


    public function showReviewMagazine($slug)
    {
        $currentMagazine = VentureMagazine::with([
            'towns.tags',
            'experiences.tags',
            'tourTiles.tags',
            'tags',
            'realContributor.author'
        ])
            ->where('slug', $slug)
            ->where('status', 'pending')
            ->firstOrFail();

        $otherMagazines = VentureMagazine::with('tags')->where('slug', '!=', $slug)
            ->where('is_published', 1)
            ->inRandomOrder()
            ->take(5)
            ->get(['id', 'title', 'slug', 'hero_image', 'seo_title', 'seo_description', 'seo_image']);

        $tourLabelsFromThisPage = collect($currentMagazine->tourTiles)
            ->pluck('rezdy_url')
            ->filter()
            ->toArray();

        $tourClicks = TourClick::whereIn('event_label', $tourLabelsFromThisPage)->get();


        return Inertia::render('Admin/VentureMagazines/Contributors/Review', [
            'magazine' => [
                'id' => $currentMagazine->id,
                'name' => $currentMagazine->title,
                'slug' => $currentMagazine->slug,
                'status' => $currentMagazine->status,
                'rezdy_url' => $currentMagazine->rezdy_url,
                'hero_image' => '/public/storage/' . $currentMagazine->hero_image,
                'big_hero_image' => '/public/storage/' . $currentMagazine->big_hero_image,
                'description' => $currentMagazine->content,
                'seo_title' => $currentMagazine->seo_title,
                'seo_description' => $currentMagazine->seo_description,
                'seo_image' => $currentMagazine->seo_image,
                'site_name' => 'Venture Magazine',

                'tags' => $currentMagazine->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ]),
                // Contributor
                'contributor' => $currentMagazine->realContributor
                    ? [
                        'id' => $currentMagazine->realContributor->id,
                        'name' => $currentMagazine->realContributor->author->display_name,
                        'photo' => $currentMagazine->realContributor->author->avatar_path
                            ? '/public/storage/' . $currentMagazine->realContributor->author->avatar_path
                            : null,
                    ]
                    : null,
            ],
            // Related: Towns
            'towns' => $currentMagazine->towns->map(function ($town) {
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

            // Related: Experiences
            'experiences' => $currentMagazine->experiences->map(function ($experience) {
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

            // Related: Tour Tiles
            'tour_tiles' => $currentMagazine->tourTiles->map(function ($tile) {
                return [
                    'id' => $tile->id,
                    'title' => $tile->title,
                    'slug' => $tile->slug,
                    'image' => '/public/storage/' . $tile->image,
                    'rezdy_url' => $tile->rezdy_url,
                    'tags' => $tile->tags->map(fn($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ]),
                ];
            }),

            // Other Magazines
            'allMagazines' => $otherMagazines->map(function ($magazine) {
                return [
                    'id' => $magazine->id,
                    'name' => $magazine->title,
                    'slug' => $magazine->slug,
                    'hero_image' => '/public/storage/' . $magazine->hero_image,
                    'tags' => $magazine->tags->map(fn($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ]),
                ];
            }),
            'tourClicks' => $tourClicks,
        ]);
    }


    public function contributors()
    {
        $magazines = VentureMagazine::with('realContributor.author')
            ->whereNotNull('real_contributor_id')
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->orderByRaw("CASE 
            WHEN status = 'pending' THEN 0 
            WHEN status = 'approved' THEN 1 
            WHEN status = 'rejected' THEN 2 
            ELSE 3 END")
            ->latest()
            ->paginate(15);
        return inertia('Admin/VentureMagazines/Contributors/Index', [
            'magazines' => $magazines,
        ]);
    }

    public function approve(Request $request,VentureMagazine $magazine)
    {
        $request->validate(['note' => 'nullable|string|max:1000']);
        // apply pending_payload if present, set status approved & published_at, notify contributor
        $magazine->approve(auth()->id()); // if you added the trait method

        if ($magazine->realContributor && $magazine->realContributor->user) {
            $magazine->realContributor->user->notify(new MagazineApprovedNotification($magazine,$request->note));
        }
        return  redirect()
            ->route('admin.venture-magazines.contributors')->with('success', 'Approved and published.');
    }

    public function reject(Request $request, VentureMagazine $magazine)
    {
        $request->validate(['reason' => 'required|string|max:1000']);
        $magazine->reject($request->reason, auth()->id()); // clears pending_payload, sets status rejected, notify
        // Send notification to contributor's user account
        if ($magazine->realContributor && $magazine->realContributor->user) {
            $magazine->realContributor->user->notify(new MagazineRejected($magazine, $request->reason));
        }

        return redirect()
            ->route('admin.venture-magazines.contributors')->with('success', 'Rejected. Contributor notified.');
    }
}
