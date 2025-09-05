<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreTeamMemberRequest;
use App\Http\Requests\UpdateTeamMemberRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class TeamMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teamMembers = TeamMember::with('user')->orderBy('order')->paginate(10);

        return Inertia::render('Admin/TeamMembers/Index', [
            'teamMembers' => $teamMembers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/TeamMembers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamMemberRequest $request)
    {
        $validated = $request->validated();

        // 1. Upload photo (if any)
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('team_photos', 'public');
        }

        $seoImagePath = null;
        if ($request->hasFile('seo_image')) {
            $seoImagePath = $request->file('seo_image')->store('seo_images', 'public');
        }

        // 2. Create contributor user

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'] ?? 'defaultPass123'),
            'role' => 'contributor',
            'bio' => $validated['bio'],
            'photo' => $photoPath
        ]);

        // 3. Create team member
        TeamMember::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'designation' => $validated['designation'],
            'bio' => $validated['bio'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
            'photo' => $photoPath,
            'seo_title' => $validated['seo_title'],
            'seo_description' => $validated['seo_description'],
            'seo_image' => $seoImagePath ?? $teamMember->seo_image ?? null,
        ]);

        return redirect()->route('admin.team-members.index')->with('success', 'Team member created successfully!');
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
    public function edit(TeamMember $teamMember)
    {
        $teamMember->load('user');

        return Inertia::render('Admin/TeamMembers/Edit', [
            'teamMember' => $teamMember,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamMemberRequest $request, TeamMember $teamMember)
    {
        $validated = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($teamMember->photo) {
                Storage::disk('public')->delete($teamMember->photo);
            }
            $validated['photo'] = $request->file('photo')->store('team_photos', 'public');
        } else {
            $validated['photo'] = $teamMember->photo;
        }

        // Handle SEO image upload
        if ($request->hasFile('seo_image')) {
            if ($teamMember->seo_image) {
                Storage::disk('public')->delete($teamMember->seo_image);
            }
            $validated['seo_image'] = $request->file('seo_image')->store('seo_images', 'public');
        } else {
            $validated['seo_image'] = $teamMember->seo_image;
        }

        $teamMember->update($validated);

        if ($teamMember->user) {
            $teamMember->user->update([
                'email' => $validated['email'] ?? $teamMember->user->email,
            ]);
        }

        return redirect()->route('admin.team-members.index')->with('success', 'Team member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeamMember $teamMember)
    {
        if ($teamMember->photo) {
            Storage::disk('public')->delete($teamMember->photo);
        }

        $teamMember->delete();

        return back();
    }

    public function toggleStatus(TeamMember $teamMember)
    {
        $teamMember->update(['is_active' => !$teamMember->is_active]);

        return back();
    }
}
