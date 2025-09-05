<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVentureMagazineRequest;
use App\Http\Requests\UpdateVentureMagazineRequest;
use App\Models\Experience;
use App\Models\SitePopup;
use App\Models\TourClick;
use App\Models\TourTile;
use App\Models\Town;
use App\Models\VentureMagazine;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class VentureMagazineController extends Controller
{

    public function ours(Request $req)
    {
        $popup = SitePopup::single();
        $q = VentureMagazine::query()
            ->whereNull('real_contributor_id')
            ->whereIn('status', ['approved', 'published'])
            ->orderByDesc('published_at');

        // optional search & filters
        if ($s = trim($req->get('q', ''))) {
            $q->where(function ($w) use ($s) {
                $w->where('title', 'like', "%$s%");
            });
        }

        $magazines = $q->paginate(12)->withQueryString();


        return Inertia::render('Frontend/Magazine/Ours', [
            'magazines' => $magazines,
            'filters'  => [
                'q' => $s ?? '',
            ],
            'magazinePopup' => $popup->magazine_content ?? '',
        ]);
    }

    public function community(Request $req)
    {
        $popup = SitePopup::single();
        $q = VentureMagazine::query()
            ->whereNotNull('real_contributor_id')
            ->whereIn('status', ['approved', 'published'])
            ->orderByDesc('published_at');

        // optional search & filters
        if ($s = trim($req->get('q', ''))) {
            $q->where(function ($w) use ($s) {
                $w->where('title', 'like', "%$s%");
            });
        }

        $magazines = $q->paginate(12)->withQueryString();

        return Inertia::render('Frontend/Magazine/Community', [
            'magazines' => $magazines,
            'filters'  => [
                'q' => $s ?? '',
            ],
            'magazinePopup' => $popup->magazine_content ?? '',
        ]);
    }

    public function showMagazine($slug)
    {
        $currentMagazine = VentureMagazine::with([
            'towns.tags',
            'experiences.tags',
            'tourTiles.tags',
            'tags',
        ])
            ->where('slug', $slug)
            ->where('is_published', 1)
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


        return Inertia::render('Frontend/Magazine/Show', [
            'seo' => [
                'title' => $currentMagazine->seo_title,
                'description' => $currentMagazine->seo_description,
                'image' => '/public/storage/' . $currentMagazine->seo_image,
                'canonical' => canonical_url('/magazine/' . $currentMagazine->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'magazine' => [
                'id' => $currentMagazine->id,
                'name' => $currentMagazine->title,
                'slug' => $currentMagazine->slug,
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
                'contributor' => $currentMagazine->contributor
                    ? [
                        'id' => $currentMagazine->contributor->id,
                        'name' => $currentMagazine->contributor->name,
                        'photo' => $currentMagazine->contributor->photo
                            ? '/public/storage/' . $currentMagazine->contributor->photo
                            : null,
                    ]
                    : null,
                'real_contributor' => $currentMagazine->realContributor
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user || !$user->teamMember) {
            abort(403, 'Unauthorized');
        }

        $magazines = VentureMagazine::with('contributor')
            ->where('contributor_id', $user->teamMember->id)
            ->orderByDesc('published_at')
            ->paginate(10);

        return Inertia::render('Frontend/Magazine/User/Index', [
            'magazines' => $magazines
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Frontend/Magazine/User/Create', [
            'contributor_id' => auth()->user()?->teamMember->id ?? null,
            'towns' => Town::select('id', 'name')->get(),
            'experiences' => Experience::select('id', 'name')->get(),
            'tourTiles' => TourTile::select('id', 'title')->get(),
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

        return redirect()->route('user.magazines.index')->with('success', 'Magazine post created successfully!.');
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
        $userTeamMemberId = auth()->user()?->teamMember->id;
        // ⛔ Abort with 404 if this magazine doesn't belong to the logged-in contributor
        if ($ventureMagazine->contributor_id !== $userTeamMemberId) {
            abort(404);
        }

        return Inertia::render('Frontend/Magazine/User/Edit', [
            'magazine' => array_merge(
                $ventureMagazine->toArray(),
                [
                    'town_ids' => $ventureMagazine->towns()->select('id', 'name')->get(),
                    'experience_ids' => $ventureMagazine->experiences()->select('id', 'name')->get(),
                    'tour_tile_ids' => $ventureMagazine->tourTiles()->select('id', 'title')->get(),
                ]
            ),
            'towns' => Town::select('id', 'name')->get(),
            'experiences' => Experience::select('id', 'name')->get(),
            'tourTiles' => TourTile::select('id', 'title')->get(),
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVentureMagazineRequest $request, VentureMagazine $ventureMagazine)
    {
        $userTeamMemberId = auth()->user()?->teamMember->id;
        // ⛔ Abort with 404 if this magazine doesn't belong to the logged-in contributor
        if ($ventureMagazine->contributor_id !== $userTeamMemberId) {
            abort(404);
        }

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

        return redirect()
            ->route('user.magazines.index')
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

        if ($ventureMagazine->social_image) {
            Storage::disk('public')->delete($ventureMagazine->social_image);
        }

        $ventureMagazine->delete();

        return back();
    }
}
