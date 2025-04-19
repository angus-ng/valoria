<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Monster;
use App\Models\Habitat;
use App\Models\Crown;
use App\Models\ArmorSet;
use App\Models\ArmorPiece;
use Illuminate\Support\Facades\DB;

class MonsterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Habitat::count() === 0) {
            \App\Models\Habitat::factory()->count(6)->create();
        }

        $hasCrowns = false;

        Monster::factory()
            ->count(3)
            ->create()
            ->each(function ($monster) use (&$hasCrowns) {

                $habitatIds = Habitat::inRandomOrder()
                    ->take(rand(1, 3))
                    ->pluck('id');
                $monster->habitats()->attach($habitatIds);

                $giveCrowns = rand(0, 1);

                if (!$hasCrowns) {
                    $giveCrowns = true;
                    $hasCrowns = true;
                }
            
                if ($giveCrowns) {
                    Crown::create([
                        'monster_id' => $monster->id,
                        'crown_type' => 'small',
                    ]);
            
                    Crown::create([
                        'monster_id' => $monster->id,
                        'crown_type' => 'large',
                    ]);
                }

                $eventOnly = rand(0, 1) ? 'true' : 'false';
                
                $armorSetId = DB::table('armor_sets')->insertGetId([
                    'name' => $monster->name . ' Armor',
                    'monster_id' => $monster->id,
                    'source_type' => 'Monster',
                    'rarity' => rand(1, 8),
                    'event_only' => DB::raw("'$eventOnly'"),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $armorSet = ArmorSet::find($armorSetId);
    
                $slots = collect(['Head', 'Chest', 'Arms', 'Waist', 'Legs'])->shuffle()->take(rand(1, 5));
                foreach ($slots as $slot) {
                    ArmorPiece::create([
                        'armor_set_id' => $armorSet->id,
                        'slot' => $slot,
                    ]);
                }
            });

    }
}
