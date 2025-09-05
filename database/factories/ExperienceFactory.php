<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true); // e.g., "Wine Tours"
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'hero_image' => 'https://i.pravatar.cc/150?img=' . rand(1, 70),

            // Editor.js JSON block format
            'description' => [
                'time' => now()->timestamp,
                'blocks' => [
                    [
                        'id' => Str::random(10),
                        'type' => 'paragraph',
                        'data' => ['text' => $this->faker->paragraph()],
                    ],
                    [
                        'id' => Str::random(10),
                        'type' => 'paragraph',
                        'data' => ['text' => $this->faker->paragraph()],
                    ],
                ],
                'version' => '2.27.0'
            ],

            'rezdy_url' => 'https://rezdy.com/book/' . Str::slug($name),

            'seo_title' => $this->faker->sentence(5),
            'seo_description' => $this->faker->paragraph(2),
            'seo_image' => 'https://i.pravatar.cc/150?img=' . rand(1, 70),

            'is_active' => $this->faker->boolean(90),
        ];
    }
}
