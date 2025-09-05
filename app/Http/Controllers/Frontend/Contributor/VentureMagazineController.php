<?php

namespace App\Http\Controllers\Frontend\Contributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Contributors\StoreVentureMagazineRequest;
use App\Http\Requests\Contributors\UpdateVentureMagazineRequest;
use App\Models\Experience;
use App\Models\TermsCondition;
use App\Models\TourClick;
use App\Models\TourTile;
use App\Models\Town;
use App\Models\VentureMagazine;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MagazineSubmittedForAdmin;
use App\Notifications\MagazineReSubmittedForAdmin;
use App\Notifications\MagazineSubmissionReceived;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class VentureMagazineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user || !$user->contributor) {
            abort(403, 'Unauthorized');
        }

        $magazines = VentureMagazine::with('contributor')
            ->where('real_contributor_id', $user->contributor->id)
            ->orderByDesc('published_at')
            ->paginate(10);

        return Inertia::render('Frontend/Magazine/Contributor/Index', [
            'magazines' => $magazines
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $terms = TermsCondition::for('contributor_submission');

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

        return Inertia::render('Frontend/Magazine/Contributor/Create', [
            'real_contributor_id' => auth()->user()?->contributor->id ?? null,
            'towns' => Town::select('id', 'name')->get(),
            'experiences' => Experience::select('id', 'name')->get(),
            'tourTiles' => TourTile::select('id', 'title')->get(),
            'terms'        => $safeTerms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVentureMagazineRequest $request)
    {
        $manager = new ImageManager(new Driver());

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

        // Hero
        if ($request->hasFile('hero_image')) {
            $image = $manager->read($request->file('hero_image')->getPathname())
                ->cover(738, 500);

            $bytes = $image->toJpeg(85);
            $path  = 'magazine/hero/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $data['hero_image'] = $path;
        }

        // big hero 
        if ($request->hasFile('big_hero_image')) {
            $image = $manager->read($request->file('big_hero_image')->getPathname())
                ->cover(1200, 630);

            $bytes = $image->toJpeg(85);
            $path  = 'magazine/big_hero/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $data['big_hero_image'] = $path;
        }

        // Seo 
        if ($request->hasFile('seo_image')) {
            $image = $manager->read($request->file('seo_image')->getPathname())
                ->cover(1200, 630);

            $bytes = $image->toJpeg(85);
            $path  = 'magazine/seo/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $data['seo_image'] = $path;
        }

        $data['published_at'] = null;
        $data['submitted_by'] = auth()->id();
        $data['status'] = $request->status ?? 'draft';
        $data['agreements'] = $request->agreements ?? [];
        $magazine = VentureMagazine::create($data);


        $magazine->towns()->sync($request->town_ids);
        $magazine->experiences()->sync($request->experience_ids);
        $magazine->tourTiles()->sync($request->tour_tile_ids);

        $emails = config('notifications.admin_submission_recipients');

        if ($request->status == 'pending') {
            foreach ($emails as $email) {
                Notification::route('mail', $email)
                    ->notify(new MagazineSubmittedForAdmin($magazine, auth()->user()));
            }
            // notify Contributor (the logged-in user)
            auth()->user()->notify(new MagazineSubmissionReceived($magazine));
        }
        return redirect()
            ->route('contributor.magazines.index')
            ->with('success', 'Magazine post created successfully! Please wait while the admin reviews and approves it.');
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
        $userContributorId = $user?->contributor?->id;
        $isOwner = $ventureMagazine->real_contributor_id
            && $ventureMagazine->real_contributor_id == $userContributorId;

        if (! $isOwner) {
            abort(404);
        }

        if ($ventureMagazine->status === 'pending' || $ventureMagazine->status === 'approved') {
            abort(403, 'This magazine has already been submitted for review or published and cannot be edited.');
        }

        return Inertia::render('Frontend/Magazine/Contributor/Edit', [
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
            'terms' => $safeTerms
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVentureMagazineRequest $request, VentureMagazine $ventureMagazine)
    {
        $manager = new ImageManager(new Driver());

        $user = auth()->user();
        $userContributorId = $user?->contributor?->id;
        $isOwner = $ventureMagazine->real_contributor_id
            && $ventureMagazine->real_contributor_id == $userContributorId;

        if (! $isOwner) {
            abort(404);
        }

        if ($ventureMagazine->status === 'pending' || $ventureMagazine->status === 'approved') {
            abort(403, 'This magazine has already been submitted for review or published and cannot be edited.');
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


          // Hero
          if ($request->hasFile('hero_image')) {
            if ($ventureMagazine->hero_image) {
                Storage::disk('public')->delete($ventureMagazine->hero_image);
            }
            $image = $manager->read($request->file('hero_image')->getPathname())
                ->cover(738, 500);

            $bytes = $image->toJpeg(85);
            $path  = 'magazine/hero/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $data['hero_image'] = $path;
        }else {
            $data['hero_image'] = $ventureMagazine->hero_image;
        }

        // big hero 
        if ($request->hasFile('big_hero_image')) {
            if ($ventureMagazine->big_hero_image) {
                Storage::disk('public')->delete($ventureMagazine->big_hero_image);
            }
            $image = $manager->read($request->file('big_hero_image')->getPathname())
                ->cover(1200, 630);

            $bytes = $image->toJpeg(85);
            $path  = 'magazine/big_hero/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $data['big_hero_image'] = $path;
        }else {
            $data['big_hero_image'] = $ventureMagazine->big_hero_image;
        }

        // Seo 
        if ($request->hasFile('seo_image')) {
            if ($ventureMagazine->seo_image) {
                Storage::disk('public')->delete($ventureMagazine->seo_image);
            }
            $image = $manager->read($request->file('seo_image')->getPathname())
                ->cover(1200, 630);

            $bytes = $image->toJpeg(85);
            $path  = 'magazine/seo/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($path, $bytes);
            $data['seo_image'] = $path;
        }else {
            $data['seo_image'] = $ventureMagazine->seo_image;
        }

        $data['published_at'] = null;
        $data['submitted_by'] = auth()->id();
        $data['status'] = $request->status ?? 'draft'; // default to draft

        // 🔒 Ensure content is stored as JSON
        $data['content'] = json_encode($data['content']);
        $data['agreements'] = $request->agreements ?? [];


        $ventureMagazine->update($data);
        $ventureMagazine->towns()->sync(collect($request->town_ids)->pluck('id')->toArray());
        $ventureMagazine->experiences()->sync(collect($request->experience_ids)->pluck('id')->toArray());
        $ventureMagazine->tourTiles()->sync(collect($request->tour_tile_ids)->pluck('id')->toArray());

        $emails = config('notifications.admin_submission_recipients');


        if ($request->status == 'pending') {
            foreach ($emails as $email) {
                Notification::route('mail', $email)
                    ->notify(new MagazineReSubmittedForAdmin($ventureMagazine, auth()->user()));
            }
            // notify Contributor (the logged-in user)
            auth()->user()->notify(new MagazineSubmissionReceived($ventureMagazine));
        }

        return redirect()
            ->route('contributor.magazines.index')
            ->with('success', 'Magazine post updated successfully! Please wait while Admin approved this Blog Post.');
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
