<script setup lang="ts">
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import ConfirmationDialog from '@/components/ConfirmationDialog.vue';
import { AppPageProps } from '@/types/inertia';
import { type ArmorSlot } from './AddArmor.vue';
import { Page } from '@inertiajs/inertia';

const page = usePage() as unknown as Page<AppPageProps>;
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');


type ArmorPiece = {
  id: number;
  slot: ArmorSlot;
};

export type ArmorSet = {
  id: number;
  name: string;
  rarity: number;
  event_only: boolean;
  source_type: string | null;
  monster_id: number | null;
  monster?: { id: number; name: string; icon_url: string };
  pieces: ArmorPiece[];
};

const emit = defineEmits(['armor-updated']);
const props = defineProps<{ armorSets: ArmorSet[] }>();

const selectedSetId = ref<number | null>(null);
const showConfirm = ref(false);

const confirmDelete = () => {
  if (selectedSetId.value !== null) {
    router.delete(`/api/admin/armor-sets/${selectedSetId.value}`, {
      preserveScroll: true,
      onSuccess: () => {
        emit('armor-updated');
        showConfirm.value = false;
        selectedSetId.value = null;
      },
    });
  }
};

const cancelDelete = () => {
  selectedSetId.value = null;
  showConfirm.value = false;
};

const askDelete = (id: number) => {
  selectedSetId.value = id;
  showConfirm.value = true;
};

const eventSets = computed(() =>
  [...props.armorSets]
    .filter((set) => set.event_only)
    .sort((a, b) => a.rarity - b.rarity || a.name.localeCompare(b.name))
);

const grouped = computed(() => {
  const result: Record<string, Record<number | string, ArmorSet[]>> = {};

  for (const set of props.armorSets) {
    if (set.event_only) continue;

    const source = set.source_type || 'Unknown';
    if (!result[source]) result[source] = {};

    if (source.toLowerCase() === 'monster' && set.monster_id !== null) {
      const key = set.monster_id;
      if (!result[source][key]) result[source][key] = [];
      result[source][key].push(set);
    } else {
      if (!result[source]['general']) result[source]['general'] = [];
      result[source]['general'].push(set);
    }
  }

  for (const source in result) {
    for (const key in result[source]) {
      result[source][key].sort((a, b) => a.rarity - b.rarity || a.name.localeCompare(b.name));
    }
  }

  return result;
});
</script>


<template>
  <ConfirmationDialog
    :show="showConfirm"
    title="Delete Armor Set"
    message="Are you sure you want to delete this armor set? This action cannot be undone."
    @confirm="confirmDelete"
    @cancel="cancelDelete"
  />
  <div class="relative overflow-hidden rounded-xl border border-[#3c3c2f]/50 dark:border-[#2c2c21] w-full h-full">
    <PlaceholderPattern opacity="30%" />
    <div class="relative w-full p-6 space-y-6">
      <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa]">Armor Sets</h2>

      <div v-if="armorSets.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic">
        No armor sets available.
      </div>

      <div v-for="(groups, source) in grouped" :key="source" class="space-y-6">
        <h3 class="text-md font-semibold text-[#4f4f3a] dark:text-[#d2d2aa] capitalize">{{ source }}</h3>

        <template v-if="source.toLowerCase() === 'monster'">
          <div
            v-for="(sets, monsterId) in groups"
            :key="monsterId"
            class="rounded-xl border border-[#b5b2a3]/50 dark:border-[#2c2c21]/50 bg-[#f0efea]/60 dark:bg-[#1a1a14]/50 p-4 space-y-2"
          >
            <div class="flex items-center gap-2 mb-2">
              <img
                v-if="sets[0].monster?.icon_url"
                :src="sets[0].monster?.icon_url"
                class="w-5 h-5 object-contain rounded border border-[#3c3c2f]/30 dark:border-[#2c2c21]/40"
              />
              <h4 class="text-sm font-medium text-[#2e2e23] dark:text-[#eaeac5]">
                {{ sets[0]?.monster?.name || 'Unknown Monster' }}
              </h4>
            </div>

            <ul class="divide-y divide-[#3c3c2f]/20 dark:divide-[#2c2c21]/30">
              <li v-for="set in sets" :key="set.id" class="py-2">
                <div class="flex items-center justify-between mb-1">
                  <span class="font-medium text-[#2e2e23] dark:text-[#eaeac5]">{{ set.name }}</span>
                  <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">★{{ set.rarity }}</span>
                    <button
                      v-if="isAdmin"
                      @click="askDelete(set.id)"
                      class="text-xs px-2 py-1 rounded bg-red-600/80 text-white hover:bg-red-700 transition"
                    >
                      Delete
                    </button>
                  </div>
                </div>
                <div class="flex flex-wrap gap-2 text-sm text-[#2e2e23] dark:text-[#eaeac5]">
                  <span
                    v-for="slot in ['Head', 'Chest', 'Arms', 'Waist', 'Legs']"
                    :key="slot"
                    :class="[
                      'text-xs px-2 py-1 rounded',
                      set.pieces.some(p => p.slot === slot)
                        ? 'bg-[#e2e2dc] dark:bg-[#2a2a1d]'
                        : 'bg-[#00000020] dark:bg-[#ffffff10] text-[#777] dark:text-[#888]'
                    ]"
                  >
                    {{ slot }}
                  </span>
                </div>
              </li>
            </ul>
          </div>
        </template>

        <template v-else>
          <div v-for="(sets, groupKey) in groups" :key="groupKey" class="space-y-4">
            <div
              v-for="set in sets"
              :key="set.id"
              class="rounded-xl border border-[#b5b2a3]/50 dark:border-[#2c2c21]/50 bg-[#f0efea]/60 dark:bg-[#1a1a14]/50 p-4 space-y-2"
            >
              <div class="flex items-center justify-between mb-1">
                <span class="font-medium text-[#2e2e23] dark:text-[#eaeac5]">{{ set.name }}</span>
                <div class="flex items-center gap-2">
                  <span class="text-sm text-gray-500 dark:text-gray-400">★{{ set.rarity }}</span>
                  <button
                    v-if="isAdmin"
                    @click="askDelete(set.id)"
                    class="text-xs px-2 py-1 rounded bg-red-600/80 text-white hover:bg-red-700 transition"
                  >
                    Delete
                  </button>
                </div>
              </div>
              <div class="flex flex-wrap gap-2 text-sm text-[#2e2e23] dark:text-[#eaeac5]">
                <span
                  v-for="slot in ['Head', 'Chest', 'Arms', 'Waist', 'Legs']"
                  :key="slot"
                  :class="[
                    'text-xs px-2 py-1 rounded',
                    set.pieces.some(p => p.slot === slot)
                      ? 'bg-[#e2e2dc] dark:bg-[#2a2a1d]'
                      : 'bg-[#00000020] dark:bg-[#ffffff10] text-[#777] dark:text-[#888]'
                  ]"
                >
                  {{ slot }}
                </span>
              </div>
            </div>
          </div>
        </template>
      </div>

      <div
        v-if="eventSets.length"
        class="space-y-4 pt-4 border-t border-[#3c3c2f]/30 dark:border-[#2c2c21]/40"
      >
        <h3 class="text-md font-semibold text-[#4f4f3a] dark:text-[#d2d2aa]">Event Armor</h3>

        <div
          v-for="set in eventSets"
          :key="set.id"
          class="rounded-xl border border-[#b5b2a3]/50 dark:border-[#2c2c21]/50 bg-[#f0efea]/60 dark:bg-[#1a1a14]/50 p-4 space-y-2"
        >
          <div class="flex items-center justify-between mb-1">
            <span class="font-medium text-[#2e2e23] dark:text-[#eaeac5]">{{ set.name }}</span>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-500 dark:text-gray-400">★{{ set.rarity }}</span>
              <button
                v-if="isAdmin"
                @click="askDelete(set.id)"
                class="text-xs px-2 py-1 rounded bg-red-600/80 text-white hover:bg-red-700 transition"
              >
                Delete
              </button>
            </div>
          </div>
          <div class="flex flex-wrap gap-2 text-sm text-[#2e2e23] dark:text-[#eaeac5]">
            <span
              v-for="slot in ['Head', 'Chest', 'Arms', 'Waist', 'Legs']"
              :key="slot"
              :class="[
                'text-xs px-2 py-1 rounded',
                set.pieces.some(p => p.slot === slot)
                  ? 'bg-[#e2e2dc] dark:bg-[#2a2a1d]'
                  : 'bg-[#00000020] dark:bg-[#ffffff10] text-[#777] dark:text-[#888]'
              ]"
            >
              {{ slot }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>