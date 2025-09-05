<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VentureMagazine;
use App\Models\TeamMember;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VentureMagazine>
 */
class VentureMagazineFactory extends Factory
{
    protected $model = VentureMagazine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $title = $this->faker->sentence(6, true);


        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'contributor_id' => TeamMember::factory(), // creates a fake team member

            'hero_image' => 'dummy_images/hero.jpg',
            'social_image' => 'dummy_images/social.jpg',

            'content' => json_encode([
                'time' => now()->timestamp,
                'blocks' => [
                    [
                        'type' => 'paragraph',
                        'data' => [
                            'text' => $this->faker->paragraph(5, true),
                        ]
                    ],
                    [
                        'type' => 'header',
                        'data' => [
                            'text' => $this->faker->sentence(),
                            'level' => 2
                        ]
                    ]
                ],
                'version' => '2.27.0'
            ]),

            'seo_title' => $this->faker->sentence(8, true),
            'seo_description' => $this->faker->paragraph(2),
            'seo_image' => 'dummy_images/seo.jpg',

            'is_published' => $this->faker->boolean(80),
            'published_at' => now()->subDays(rand(0, 30)),
        ];
    }
}
