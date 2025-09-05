<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ProfileContributorUpdateRequest;
use App\Models\Author;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        // Reset email verification if email is updated
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle user profile photo
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->photo = $path;
        } else {
            $user->photo = $user->photo;
        }

        $user->save();

        // ✅ Update associated Team Member if exists
        if ($user->teamMember) {
            $teamMember = $user->teamMember;

            // Update name and bio
            $teamMember->name = $request->input('name');
            $teamMember->bio = $request->input('bio');

            // Handle team member profile photo
            if ($request->hasFile('photo')) {
                $teamPath = $request->file('photo')->store('team-member-photos', 'public');
                $teamMember->photo = $teamPath;
            } else {
                $teamMember->photo = $teamMember->photo;
            }

            $teamMember->save();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the user's profile form.
     */
    public function editContributor(Request $request): Response
    {
        $user = $request->user(); // ✅ you were missing this

        return Inertia::render('Profile/Contributor/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            // You can override/augment the usual shared auth.user here:
            'auth' => [
                'user' => [
                    'id'           => $user->id,
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'photo'        =>  optional($user->contributor?->author)->avatar_path,
                    'display_name' => optional($user->contributor?->author)->display_name,
                    'contributor_slug'  => optional($user->contributor?->author)->slug, // ✅ added
                ],
            ],
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateContributor(ProfileContributorUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $contributor = $user->contributor; // assumes relation exists: User->hasOne(Contributor)

        DB::transaction(function () use ($request, $user, $contributor) {
            // Update user core fields
            $user->name  = $request->input('name');
            $user->email = $request->input('email');

            // Reset email verification if email changed
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            // Handle user profile photo
            $uploadedPath = null;

            $user->save();

            // Link/update Author via Contributor
            if ($contributor) {
                // Ensure an author exists and is linked
                $author = $contributor->author;
                // Update author fields
                $author->display_name = $request->input('display_name');

                // If you want the same uploaded photo also used for author:
                if ($uploadedPath) {
                    // delete previous author photo if different
                    if ($author->avatar_path && $author->avatar_path !== $uploadedPath) {
                        Storage::disk('public')->delete($author->avatar_path);
                    }
                    $author->avatar_path = $uploadedPath;
                }

                $author->save();
            }
        });

        return Redirect::route('contributor.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroyContributor(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
