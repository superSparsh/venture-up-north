<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourTile>
 */
class TourTileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        return [
            'title' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'rezdy_url' => $this->faker->url,
            'image' => 'tour_tiles/' . Str::uuid() . '.jpg',
            'seo_title' => $this->faker->sentence(6),
            'seo_description' => $this->faker->paragraph(2),
            'seo_image' => 'seo_images/' . Str::uuid() . '.jpg',
        ];
    }
}
