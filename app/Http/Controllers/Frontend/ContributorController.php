<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContributorController extends Controller
{
    public function dashboard(string $slug)
    {
        $author = Author::query()
            ->select(['id', 'display_name', 'slug', 'avatar_path'])
            ->where('slug', $slug)
            ->whereHas('contributor', fn($q) => $q->where('user_id', auth()->id()))
            ->with(['contributor' => fn($q) => $q->select([
                'id',
                'author_id',
                'status',
                'submissions_count',
                'approved_count',
                'rejected_count',
                'last_submission_at',
            ])->where('user_id', auth()->id())])
            ->firstOrFail();

        $c = $author->contributor;

        return Inertia::render('Frontend/Contributors/Dashboard', [
            'author' => [
                'display_name' => $author->display_name,
                'slug'         => $author->slug,
                'avatar_path'  => $author->avatar_path,
            ],
            'contributor' => [
                'status'              => $c?->status ?? 'active',
                'submissions_count'   => (int) ($c?->submissions_count ?? 0),
                'approved_count'      => (int) ($c?->approved_count ?? 0),
                'rejected_count'      => (int) ($c?->rejected_count ?? 0),
                'last_submission_at'  => optional($c?->last_submission_at)?->toDateTimeString(),
                // helpful booleans for the UI
                'is_blocked'          => ($c?->status ?? 'active') === 'blocked',
            ],
        ]);
    }
}
