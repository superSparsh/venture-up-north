<?php

namespace Database\Factories;

use App\Models\HomeSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HomeSection>
 */
class HomeSectionFactory extends Factory
{
    protected $model = HomeSection::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(2, true),
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
            'order' => rand(1, 10),
            'is_active' => true,
        ];
    }
}
