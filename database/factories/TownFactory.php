<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Town;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Town>
 */
class TownFactory extends Factory
{
    protected $model = Town::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city,
            'slug' => $this->faker->unique()->slug,
            'summary' => $this->faker->sentence,
            'hero_image' => 'https://source.unsplash.com/600x400/?nature,town,' . rand(1, 50),
            'rezdy_url' => 'https://sobroome.rezdy.com/carouselWidget/318043?iframe=true',
            'description' => json_encode([
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
        ];
    }
}
