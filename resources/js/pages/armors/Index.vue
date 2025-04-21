<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { computed } from 'vue';
import type { Monster } from '@/types/monster';
import { BreadcrumbItem } from '@/types';

type ArmorPiece = {
  id: number;
  slot: 'Head' | 'Chest' | 'Arms' | 'Waist' | 'Legs';
  users: {
    pivot: {
      obtained: boolean;
      obtained_at?: string;
    };
  }[];
};

type ArmorSet = {
  id: number;
  name: string;
  rarity: number;
  event_only: boolean;
  source_type: string | null;
  monster_id: number | null;
  monster?: Monster;
  pieces: ArmorPiece[];
};

const props = defineProps<{ armorSets: ArmorSet[] }>();

const togglePiece = (pieceId: number) => {
  router.post('/armor-sets/toggle', { armor_piece_id: pieceId }, {
    preserveScroll: true,
    onSuccess: () => {
      router.visit('/armor-sets', { preserveScroll: true });
    }
  });
};

const totalPieces = computed(() =>
  props.armorSets.reduce((acc, set) => acc + set.pieces.length, 0)
);

const obtainedPieces = computed(() =>
  props.armorSets.reduce(
    (acc, set) => acc + set.pieces.filter(p => p.users?.[0]?.pivot?.obtained).length,
    0
  )
);

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Armors', href: '/armor-sets' },
];

const grouped = computed(() => {
  const result: Record<string, Record<string | number, ArmorSet[]>> = {};

  for (const set of props.armorSets) {
    if (set.event_only) continue;

    const source = set.source_type?.toLowerCase() || 'unknown';
    const sourceKey = source.charAt(0).toUpperCase() + source.slice(1);

    if (!result[sourceKey]) result[sourceKey] = {};

    if (source === 'monster' && set.monster_id !== null) {
      if (!result[sourceKey][set.monster_id]) result[sourceKey][set.monster_id] = [];
      result[sourceKey][set.monster_id].push(set);
    } else {
      if (!result[sourceKey]['general']) result[sourceKey]['general'] = [];
      result[sourceKey]['general'].push(set);
    }
  }

  for (const source in result) {
    for (const key in result[source]) {
      result[source][key].sort((a, b) => a.rarity - b.rarity || a.name.localeCompare(b.name));
    }
  }

  return result;
});

const eventSets = computed(() =>
  props.armorSets.filter(s => s.event_only).sort((a, b) => a.rarity - b.rarity || a.name.localeCompare(b.name))
);

const countGroupPieces = (sets: ArmorSet[]) => {
  let total = 0;
  let obtained = 0;
  for (const set of sets) {
    total += set.pieces.length;
    obtained += set.pieces.filter(p => p.users?.[0]?.pivot?.obtained).length;
  }
  return { total, obtained, remaining: total - obtained };
};

const monsterCategoryCount = computed(() => {
  const monsterGroup = grouped.value['Monster'];
  const allMonsterSets = Object.values(monsterGroup || {}).flat();
  return countGroupPieces(allMonsterSets);
});
</script>

<template>
  <Head title="Armors" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <h1 class="text-2xl font-bold text-[#4f4f3a] dark:text-[#d2d2aa]">Armor Progress</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">
        Total: {{ obtainedPieces }} / {{ totalPieces }} pieces
        ({{ totalPieces - obtainedPieces }} remaining)
      </p>

      <div v-for="(groups, source) in grouped" :key="source" class="space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa]">{{ source }}</h2>
          <span class="text-sm text-gray-500 dark:text-gray-400">
            <template v-if="source === 'Monster'">
              {{ monsterCategoryCount.obtained }} / {{ monsterCategoryCount.total }} pieces
              ({{ monsterCategoryCount.remaining }} remaining)
            </template>
            <template v-else>
              {{ countGroupPieces(groups.general).obtained }} / {{ countGroupPieces(groups.general).total }} pieces
              ({{ countGroupPieces(groups.general).remaining }} remaining)
            </template>
          </span>
        </div>

        <template v-if="source === 'Monster'">
          <div v-for="(sets, monsterId) in groups" :key="monsterId" class="space-y-4">
            <div class="flex justify-between items-center mb-1">
              <div class="flex items-center gap-2">
                <img
                  v-if="sets[0].monster?.icon_url"
                  :src="sets[0].monster.icon_url"
                  class="w-6 h-6 object-contain rounded border border-[#3c3c2f]/30 dark:border-[#2c2c21]/40"
                />
                <h3 class="text-md font-medium text-[#2e2e23] dark:text-[#eaeac5]">
                  {{ sets[0].monster?.name || 'Unknown Monster' }}
                </h3>
              </div>
              <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ countGroupPieces(sets).obtained }} / {{ countGroupPieces(sets).total }} pieces
                ({{ countGroupPieces(sets).remaining }} remaining)
              </span>
            </div>

            <div
              v-for="set in sets"
              :key="set.id"
              class="relative rounded-xl border border-[#3c3c2f]/30 dark:border-[#2c2c21]/40 bg-[#e2e2dc]/70 dark:bg-[#1a1a14]/60 p-4 space-y-3"
            >
              <PlaceholderPattern opacity="10%" />
              <div class="flex justify-between items-center">
                <h4 class="font-semibold text-[#2e2e23] dark:text-[#eaeac5]">{{ set.name }}</h4>
                <span class="text-xs text-[#5f5f4a] dark:text-[#a5a580]">★{{ set.rarity }}</span>
              </div>

              <div class="flex flex-wrap gap-2">
                <button
                  v-for="piece in set.pieces"
                  :key="piece.id"
                  @click="togglePiece(piece.id)"
                  class="relative text-xs px-2 py-1 rounded cursor-pointer transition"
                  :class="piece.users?.[0]?.pivot?.obtained
                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200'
                    : 'bg-[#d6d6c9] text-[#444] dark:bg-[#ffffff10] dark:text-[#999]'"
                >
                  {{ piece.slot }}
                  <div
                    v-if="!piece.users?.[0]?.pivot?.obtained"
                    class="absolute inset-0 bg-black/10 dark:bg-black/60 rounded pointer-events-none"
                  ></div>
                </button>
              </div>
            </div>
          </div>
        </template>

        <template v-else>
          <div
            v-for="set in groups.general"
            :key="set.id"
            class="relative rounded-xl border border-[#3c3c2f]/30 dark:border-[#2c2c21]/40 bg-[#e2e2dc]/70 dark:bg-[#1a1a14]/60 p-4 space-y-3"
          >
            <PlaceholderPattern opacity="10%" />
            <div class="flex justify-between items-center">
              <h4 class="font-semibold text-[#2e2e23] dark:text-[#eaeac5]">{{ set.name }}</h4>
              <span class="text-xs text-[#5f5f4a] dark:text-[#a5a580]">★{{ set.rarity }}</span>
            </div>

            <div class="flex flex-wrap gap-2">
              <button
                v-for="piece in set.pieces"
                :key="piece.id"
                @click="togglePiece(piece.id)"
                class="relative text-xs px-2 py-1 rounded cursor-pointer transition"
                :class="piece.users?.[0]?.pivot?.obtained
                  ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200'
                  : 'bg-[#d6d6c9] text-[#444] dark:bg-[#ffffff10] dark:text-[#999]'"
              >
                {{ piece.slot }}
                <div
                  v-if="!piece.users?.[0]?.pivot?.obtained"
                  class="absolute inset-0 bg-black/10 dark:bg-black/60 rounded pointer-events-none"
                ></div>
              </button>
            </div>
          </div>
        </template>
      </div>

      <div v-if="eventSets.length" class="space-y-6 border-t border-[#3c3c2f]/30 dark:border-[#2c2c21]/40 pt-6">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa]">Event Armor</h2>
          <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ countGroupPieces(eventSets).obtained }} / {{ countGroupPieces(eventSets).total }} pieces
            ({{ countGroupPieces(eventSets).remaining }} remaining)
          </span>
        </div>

        <div
          v-for="set in eventSets"
          :key="set.id"
          class="relative rounded-xl border border-[#3c3c2f]/30 dark:border-[#2c2c21]/40 bg-[#e2e2dc]/70 dark:bg-[#1a1a14]/60 p-4 space-y-3"
        >
          <PlaceholderPattern opacity="10%" />
          <div class="flex justify-between items-center">
            <h4 class="font-semibold text-[#2e2e23] dark:text-[#eaeac5]">{{ set.name }}</h4>
            <span class="text-xs text-[#5f5f4a] dark:text-[#a5a580]">★{{ set.rarity }}</span>
          </div>

          <div class="flex flex-wrap gap-2">
            <button
              v-for="piece in set.pieces"
              :key="piece.id"
              @click="togglePiece(piece.id)"
              class="relative text-xs px-2 py-1 rounded cursor-pointer transition"
              :class="piece.users?.[0]?.pivot?.obtained
                ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200'
                : 'bg-[#d6d6c9] text-[#444] dark:bg-[#ffffff10] dark:text-[#999]'"
            >
              {{ piece.slot }}
              <div
                v-if="!piece.users?.[0]?.pivot?.obtained"
                class="absolute inset-0 bg-black/10 dark:bg-black/60 rounded pointer-events-none"
              ></div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
