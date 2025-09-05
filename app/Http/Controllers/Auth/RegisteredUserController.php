<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Contributor;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 2) Create Author (type=user) with unique slug
        $slug = $this->makeUniqueAuthorSlug($request->name);

        $author = Author::create([
            'type'         => 'user',
            'ref_id'       => $user->id,
            'display_name' => $user->name,
            'slug'         => $slug,
        ]);

        // 3) Create Contributor (active by default)
        Contributor::create([
            'user_id'   => $user->id,
            'author_id' => $author->id,
            'status'    => 'active',
        ]);

        event(new Registered($user));

        Auth::login($user);

        // return redirect(route('dashboard', absolute: false));
        return redirect()->route('contributors.dashboard', [
            'slug' => $user->contributor->author->slug
        ]);
    }

    /**
     * Make a unique slug for the author from the given name.
     */
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
