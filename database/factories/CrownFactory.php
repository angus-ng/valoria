<?php

namespace Database\Factories;

use App\Models\Crown;
use App\Models\Monster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Crown>
 */
class CrownFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        throw new \Exception("Use this factory via a loop or seeder to assign crowns in pairs.");
    }

    /**
     * Static method to generate both crowns for eligible monsters
     */
    public static function createForEligibleMonsters(int $count = 3): void
    {
        $monsters = Monster::inRandomOrder()->take($count)->get();

        foreach ($monsters as $monster) {
            Crown::create([
                'monster_id' => $monster->id,
                'crown_type' => 'small',
            ]);

            Crown::create([
                'monster_id' => $monster->id,
                'crown_type' => 'large',
            ]);
        }
    }
}
