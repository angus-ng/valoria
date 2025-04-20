<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Habitat } from '@/types/monster';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';

const props = defineProps<{
  crowns: {
    crown_type: 'small' | 'large';
    user_obtained: boolean;
    monster: {
      id: number;
      name: string;
      icon_url: string;
      habitats: Habitat[];
    };
  }[];
}>();

const groupedMonsters = computed(() => {
  const map = new Map<number, {
    id: number;
    name: string;
    icon_url: string;
    habitats: Habitat[];
    smallObtained: boolean;
    largeObtained: boolean;
  }>();

  props.crowns.forEach(crown => {
    const { monster, crown_type, user_obtained } = crown;

    if (!map.has(monster.id)) {
      map.set(monster.id, {
        id: monster.id,
        name: monster.name,
        icon_url: monster.icon_url,
        habitats: monster.habitats,
        smallObtained: false,
        largeObtained: false,
      });
    }

    const entry = map.get(monster.id)!;
    if (crown_type === 'small') entry.smallObtained = user_obtained;
    if (crown_type === 'large') entry.largeObtained = user_obtained;
  });

  return Array.from(map.values());
});

const collectedCount = computed(() =>
  groupedMonsters.value.filter(m => m.smallObtained && m.largeObtained).length
);
const smallCount = computed(() =>
  groupedMonsters.value.filter(m => m.smallObtained).length
);
const largeCount = computed(() =>
  groupedMonsters.value.filter(m => m.largeObtained).length
);

const toggleCrown = (monsterId: number, crownType: 'small' | 'large') => {
  router.post('/crowns/toggle', { monster_id: monsterId, crown_type: crownType }, {
    preserveScroll: true,
    onSuccess: () => {
      router.visit('/crowns', { preserveScroll: true });
    }
  });
};

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Crowns', href: '/crowns' },
];
</script>



<template>
  <Head title="Crowns" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-4 p-6">
      <h1 class="text-2xl font-bold text-[#4f4f3a] dark:text-[#d2d2aa]">Crown Progress</h1>
      <div class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
        <p>
          Fully Crowned: {{ collectedCount }} / {{ groupedMonsters.length }} - ({{ groupedMonsters.length - collectedCount }} remaining)
        </p>
        <p>
          Small Crowns: {{ smallCount }} / {{ groupedMonsters.length }} - ({{ groupedMonsters.length - smallCount }} remaining)
        </p>
        <p>
          Large Crowns: {{ largeCount }} / {{ groupedMonsters.length }} - ({{ groupedMonsters.length - largeCount }} remaining)
        </p>
      </div>

      <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <li
          v-for="monster in groupedMonsters"
          :key="monster.id"
          class="relative rounded border border-[#3c3c2f]/30 dark:border-[#2c2c21]/40 bg-[#f8f8f5] dark:bg-[#1f1f18] p-4 space-y-3 overflow-hidden"
        >
          <PlaceholderPattern class="absolute inset-0 pointer-events-none" opacity="30%"/>
          
          <div class="relative z-10 space-y-3">
            <div class="flex items-center gap-3">
              <img :src="monster.icon_url" class="w-8 h-8 object-contain" />
              <span class="text-[#2e2e23] dark:text-[#eaeac5] font-medium">{{ monster.name }}</span>
            </div>

            <div v-if="monster.habitats?.length" class="flex flex-wrap gap-2 text-xs">
              <div
                v-for="habitat in monster.habitats"
                :key="habitat.id"
                class="flex items-center gap-2 px-2 py-1 rounded bg-[#f8f8f5] dark:bg-[#1f1f18] border border-[#3c3c2f]/30 dark:border-[#2c2c21]/40"
              >
                <img
                  v-if="habitat.icon_url"
                  :src="habitat.icon_url"
                  class="w-4 h-4 object-contain"
                />
                <span class="text-[#2e2e23] dark:text-[#eaeac5]">{{ habitat.name }}</span>
              </div>
            </div>

            <div class="flex gap-2">
              <button
                @click="toggleCrown(monster.id, 'small')"
                class="relative px-2 py-1 rounded text-xs cursor-pointer"
                :class="monster.smallObtained
                  ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200'
                  : 'bg-[#00000010] text-[#777] dark:bg-[#ffffff10] dark:text-[#999]'"
              >
                👑 Small
                <div
                  v-if="!monster.smallObtained"
                  class="absolute inset-0 bg-black/40 dark:bg-black/60 rounded pointer-events-none"
                ></div>
              </button>

              <button
                @click="toggleCrown(monster.id, 'large')"
                class="relative px-2 py-1 rounded text-xs cursor-pointer"
                :class="monster.largeObtained
                  ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200'
                  : 'bg-[#00000010] text-[#777] dark:bg-[#ffffff10] dark:text-[#999]'"
              >
                👑 Large
                <div
                  v-if="!monster.largeObtained"
                  class="absolute inset-0 bg-black/40 dark:bg-black/60 rounded pointer-events-none"
                ></div>
              </button>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </AppLayout>
</template>

