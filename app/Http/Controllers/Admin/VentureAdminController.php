<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Models\Venture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class VentureAdminController extends Controller
{
    public function index(Request $req)
    {
        $q = Venture::query()
            ->select(['id', 'slug', 'title', 'owner_user_id', 'owner_guest_name', 'status', 'visibility', 'created_by_admin_id', 'created_at', 'published_at'])
            ->with(['days' => fn($qq) => $qq->select('id', 'venture_id')])
            ->whereNull('created_by_admin_id')
            ->withCount('items');

        if ($s = trim($req->get('q', ''))) {
            $q->where(function ($w) use ($s) {
                $w->where('title', 'like', "%$s%")
                    ->orWhere('owner_guest_name', 'like', "%$s%");
            });
        }
        if ($st = $req->get('status'))     $q->where('status', $st);
        if ($vis = $req->get('visibility')) $q->where('visibility', $vis);

        $ventures = $q->orderByDesc('created_at')->paginate(20)->withQueryString();

        return Inertia::render('Admin/Ventures/Index', [
            'ventures' => $ventures,
            'filters'  => [
                'q' => $s,
                'status' => $req->get('status'),
                'visibility' => $req->get('visibility'),
            ],
        ]);
    }

    public function ourIndex(Request $request)
    {
        $query = Collection::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $collections = $query->where('type', 'venture')->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Ventures/AdminVentures', [
            'collections' => $collections,
            'filters' => $request->only('search'),
        ]);
    }

    public function edit(Venture $venture)
    {
        return Inertia::render('Admin/Ventures/Edit', [
            'venture' => $venture->only([
                'id',
                'slug',
                'title',
                'owner_user_id',
                'owner_guest_name',
                'seo_title',
                'seo_description',
                'og_image_url',
                'cover_image_url',
                'canonical_url',
                'visibility',
                'status'
            ]),
        ]);
    }

    public function update(Request $request, Venture $venture)
    {
        // ✅ Validate only allowed fields
        $data = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'owner_user_id'     => ['nullable', 'integer', 'exists:users,id'],
            'owner_guest_name'  => ['nullable', 'string', 'max:255'],
            'seo_title'         => ['nullable', 'string', 'max:255'],
            'seo_description'   => ['nullable', 'string', 'max:5000'],
            'og_image_url'      => ['nullable', 'image', 'max:2048', 'dimensions:min_width=1200,min_height=630'],
            'cover_image_url'   => ['nullable', 'image', 'max:2048', 'dimensions:min_width=738,min_height=500'],
            'visibility'        => ['nullable', Rule::in(['public', 'unlisted', 'private'])],
            'status'            => ['nullable', Rule::in(['draft', 'submitted', 'approved', 'published', 'archived'])],
        ]);

        // ✅ Fill basic attributes
        $venture->fill([
            'title'            => $data['title'],
            'owner_user_id'    => $data['owner_user_id'] ?? null,
            'owner_guest_name' => $data['owner_guest_name'] ?? null,
            'seo_title'        => $data['seo_title'] ?? null,
            'seo_description'  => $data['seo_description'] ?? null,
        ]);

        // ✅ Handle OG image upload
        if ($request->hasFile('og_image_url')) {
            if ($venture->og_image_url) {
                Storage::disk('public')->delete($venture->og_image_url);
            }
            $venture->og_image_url = $request->file('og_image_url')->store('venture/seo', 'public');
        }

        // ✅ Handle Hero image upload
        if ($request->hasFile('cover_image_url')) {
            if ($venture->cover_image_url) {
                Storage::disk('public')->delete($venture->cover_image_url);
            }
            $venture->cover_image_url = $request->file('cover_image_url')->store('venture/hero', 'public');
        }

        // ✅ Visibility / Status
        if (isset($data['visibility'])) {
            $venture->visibility = strtolower($data['visibility']);
        }
        if (isset($data['status'])) {
            $venture->status = strtolower($data['status']);
        }

        $venture->save();

        return redirect()
            ->route('admin.ventures.index')
            ->with('success', 'Venture updated');
    }

    public function destroy(Venture $venture)
    {
        DB::transaction(function () use ($venture) {
            // (optional) delete stored files on the venture
            if ($venture->cover_image_url) {
                Storage::disk('public')->delete($venture->cover_image_url);
            }
            if ($venture->og_image_url) {
                Storage::disk('public')->delete($venture->og_image_url);
            }

            // If you have a tags pivot, detach it. Safe to call even if relation doesn't exist.
            if (method_exists($venture, 'tags')) {
                $venture->tags()->detach();
            }

            // Delete children first
            $venture->items()->delete();
            $venture->days()->delete();

            $venture->delete();
        });

        return redirect()
            ->route('admin.ventures.index')
            ->with('success', 'Venture and related records deleted.');
    }
}
