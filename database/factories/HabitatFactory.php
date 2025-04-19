<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Habitat>
 */
class HabitatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $names = [
            'Windward Plains',
            'Scarlet Forest',
            'Oilwell Basin',
            'Iceshard Cliffs',
            'Ruins of Wyveria',
            'Wounded Hollow',
        ];

        static $index = 0;

        return [
            'name' => $names[$index++] ?? $this->faker->unique()->word(),
            'icon_url' => '/placeholder.svg',
        ];
    }
}
