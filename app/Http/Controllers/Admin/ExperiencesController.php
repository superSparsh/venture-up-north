<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreExperienceRequest;
use App\Http\Requests\UpdateExperienceRequest;
use App\Models\Experience;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExperiencesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $experiences = Experience::orderBy('name')->paginate(10);

        return Inertia::render('Admin/Experiences/Index', [
            'experiences' => $experiences
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Experiences/Create',[
            'tags' => Tag::where('is_active',1)->orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExperienceRequest $request)
    {
        $data = $request->validated();

        // Upload images
        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $request->file('hero_image')->store('experience/image', 'public');
        }

        if ($request->hasFile('big_hero_image')) {
            $data['big_hero_image'] = $request->file('big_hero_image')->store('experience/big_hero', 'public');
        }

        if ($request->hasFile('seo_image')) {
            $data['seo_image'] = $request->file('seo_image')->store('experience/seo', 'public');
        }

        $data['slug'] = Str::slug($data['name']);
        $data['description'] = json_encode($data['description']);
        $data['is_active'] = $request->boolean('is_active');

        $experience = Experience::create($data);

        $experience->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.experiences.index')->with('success', 'Experience created successfully!');
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
    public function edit(Experience $experience)
    {
        return Inertia::render('Admin/Experiences/Edit', [
            'experience' => $experience->load('tags'),
            'tags' => Tag::orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExperienceRequest $request, Experience $experience)
    {
        $data = $request->validated();

        if ($request->hasFile('hero_image')) {
            if ($experience->hero_image) {
                Storage::disk('public')->delete($experience->hero_image);
            }

            $data['hero_image'] = $request->file('hero_image')->store('experience', 'public');
        } else {
            $data['hero_image'] = $experience->hero_image;
        }

        if ($request->hasFile('big_hero_image')) {
            if ($experience->big_hero_image) {
                Storage::disk('public')->delete($experience->big_hero_image);
            }

            $data['big_hero_image'] = $request->file('big_hero_image')->store('experience', 'public');
        } else {
            $data['big_hero_image'] = $experience->big_hero_image;
        }

        if ($request->hasFile('seo_image')) {
            if ($experience->seo_image) {
                Storage::disk('public')->delete($experience->seo_image);
            }

            $data['seo_image'] = $request->file('seo_image')->store('experience', 'public');
        } else {
            $data['seo_image'] = $experience->seo_image;
        }
        // Update slug if name changed
        if ($experience->name !== $data['name']) {
            $data['slug'] = Str::slug($data['name']);
        }

        // 🔒 Ensure content is stored as JSON
        $data['description'] = json_encode($data['description']);

        $experience->update($data);

        $experience->tags()->sync(collect($request->input('tag_ids', []))->pluck('id')->toArray());

        return redirect()->route('admin.experiences.index')->with('success', 'Experience updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Experience $experience)
    {
        if ($experience->hero_image) {
            Storage::disk('public')->delete($experience->hero_image);
        }

        if ($experience->seo_image) {
            Storage::disk('public')->delete($experience->seo_image);
        }
        
        if ($experience->big_hero_image) {
            Storage::disk('public')->delete($experience->big_hero_image);
        }

        $experience->delete();

        return back();
    }

    public function toggleStatus(Experience $experience)
    {
        $experience->update(['is_active' => !$experience->is_active]);

        return back();
    }
}
