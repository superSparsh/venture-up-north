<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Author;
use App\Models\Contributor;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EnsureContributor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not authenticated, let the 'auth' middleware handle it
        if (! $user) {
            return $next($request);
        }

        // Ensure Author for this user
        $author = Author::firstOrCreate(
            ['type' => 'user', 'ref_id' => $user->id],
            [
                'display_name' => $user->name,
                'slug'         => $this->makeUniqueAuthorSlug($user->name),
                'avatar_path'  => null,
            ]
        );

        // Ensure Contributor record
        $contributor = Contributor::firstOrCreate(
            ['user_id' => $user->id],
            ['author_id' => $author->id, 'status' => 'active']
        );

        // If user was created as contributor without author linkage (edge case), fix it
        if (! $contributor->author_id) {
            $contributor->author_id = $author->id;
            $contributor->save();
        }

        // Blocked contributors cannot access these routes
        if ($contributor->status === 'blocked') {
            return redirect()->route('login')
                ->with('error', 'Your contributor account is blocked. Please contact admin.');
        }

        // Make available to controllers if needed
        $request->attributes->set('contributor', $contributor);
        $request->attributes->set('contributor_slug', $author->slug);

        // Share slug to all Inertia responses (handy for Ziggy links)
        Inertia::share([
            'auth' => [
                'user' => array_merge(
                    $request->user()->only('id', 'name', 'email'),
                    ['contributor_slug' => $author->slug]
                ),
            ],
        ]);

        return $next($request);
    }

    private function makeUniqueAuthorSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'author';
        $slug = $base;
        $i = 1;

        while (Author::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }
}
