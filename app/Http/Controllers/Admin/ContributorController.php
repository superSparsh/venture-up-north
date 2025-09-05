<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contributor;
use App\Models\VentureMagazine;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ContributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contributors = Contributor::with(['user', 'author'])->whereHas('user', fn($q) => $q->where('role', '<>', 'admin'))
            ->latest() // orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Contributors/Index', [
            'contributors' => $contributors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(Contributor $contributor)
    {
        $contributor->load(['user', 'author']);

        return Inertia::render('Admin/Contributors/Edit', [
            'contributor' => $contributor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contributor $contributor)
    {
        // Validate exactly what your form sends
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'display_name'  => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255'],
            'status'        => ['required', 'in:active,blocked'],
            'photo'         => ['nullable', 'image', 'max:4096'],
        ]);

        DB::transaction(function () use ($validated, $request, $contributor) {
            // 1) Update contributor (status only here)
            $contributor->update([
                'status' => $validated['status'],
            ]);

            // 2) Update related user (name/email)
            if ($contributor->user) {
                $contributor->user->update([
                    'name'  => $validated['name'],
                    'email' => $validated['email'],
                ]);
            }

            // Photo upload
            if ($request->hasFile('photo')) {
                if ($contributor->author->avatar_path) {
                    Storage::disk('public')->delete($contributor->author->avatar_path);
                }
                $contributor->author->avatar_path = $request->file('photo')->store('contributors/photos', 'public');
            }

            // Always update display_name
            $contributor->author->display_name = $validated['display_name'];
            $contributor->author->save();
        });

        return redirect()
            ->route('admin.contributors.index')
            ->with('success', 'Contributor updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contributor $contributor)
    {
        DB::transaction(function () use ($contributor) {
            // If magazines reference this contributor, decide what to do:
            // Option A: orphan them (set to null)
            VentureMagazine::where('real_contributor_id', $contributor->id)
                ->delete();

            // // 1) Delete author assets and null the FK so we can delete the author
            // if ($contributor->author) {
            //     if ($contributor->author->avatar_path) {
            //         Storage::disk('public')->delete($contributor->author->avatar_path);
            //     }

            //     $contributor->author->delete();
            // }


            // 3) Delete linked user (if you truly want to remove the account)
            if ($contributor->user) {
                // If you store an avatar on user, delete it here as well
                $contributor->user->delete();
            }

            // 4) Finally delete the contributor
            $contributor->delete();
        });

        return back()->with('success', 'Contributor deleted successfully.');
    }

    public function toggleStatus(Contributor $contributor)
    {
        $newStatus = $contributor->status === 'active' ? 'blocked' : 'active';

        $contributor->update([
            'status' => $newStatus,
        ]);

        return back()->with('success', "Contributor status updated to {$newStatus}.");
    }
}
