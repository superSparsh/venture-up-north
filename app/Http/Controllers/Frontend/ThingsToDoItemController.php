<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ThingsToDoCategory;
use App\Models\ThingsToDoItem;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ThingsToDoItemController extends Controller
{
    public function showListing($category_slug, $slug)
    {
        // Get the category by slug
        $category = ThingsToDoCategory::where('slug', $category_slug)->firstOrFail();

        // Get the listing within that category
        $currentCategoryListing = ThingsToDoItem::with(['towns', 'tags', 'events'])
            ->where('slug', $slug)
            ->where('category_id', $category->id)
            ->where('is_active', 1)
            ->firstOrFail();

        return Inertia::render('Frontend/CategoryListing/Show', [
            'seo' => [
                'title' => $currentCategoryListing->seo_title,
                'description' => $currentCategoryListing->seo_description,
                'image' => '/public/storage/' . $currentCategoryListing->seo_image,
                'canonical' => canonical_url('/explore/' . $category->slug . '/' . $currentCategoryListing->slug),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            'categoryListing' => [
                'id' => $currentCategoryListing->id,
                'name' => $currentCategoryListing->title,
                'slug' => $currentCategoryListing->slug,
                'summary' => $currentCategoryListing->summary,
                'hero_image' => '/public/storage/' . $currentCategoryListing->image,
                'big_hero_image' => '/public/storage/' . $currentCategoryListing->big_image,
                'description' => $currentCategoryListing->content,
                'opening_times' => $currentCategoryListing->opening_times,
                'address' => $currentCategoryListing->address,
                'phone_number' => $currentCategoryListing->phone_number,
                'email' => $currentCategoryListing->email,
                'custom_fields' => $currentCategoryListing->custom_fields,
                'custom_buttons' => $currentCategoryListing->custom_buttons,
                'social_links' => $currentCategoryListing->social_links,
                'seo_title' => $currentCategoryListing->seo_title,
                'seo_description' => $currentCategoryListing->seo_description,
                'seo_image' => $currentCategoryListing->seo_image,
                'location' => $currentCategoryListing->location,
                'video' => $currentCategoryListing->video,
                'towns' => $currentCategoryListing->towns->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]),
                'tags' => $currentCategoryListing->tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name]),
                'events' => $currentCategoryListing->events->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]),
            ],
            'category' => $category
        ]);
    }
}
