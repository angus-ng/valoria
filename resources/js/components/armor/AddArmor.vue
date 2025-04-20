<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { computed, watch, ref } from 'vue';

defineProps<{
  monsters: { id: number; name: string }[];
}>();

const emit = defineEmits(['armor-updated']);

export type ArmorSlot = 'Head' | 'Chest' | 'Arms' | 'Waist' | 'Legs';
const slotKeys: ArmorSlot[] = ['Head', 'Chest', 'Arms', 'Waist', 'Legs'];

const form = useForm({
  name: '',
  monster_id: null,
  source_type: '',
  rarity: null,
  event_only: false,
  pieces: {
    Head: false,
    Chest: false,
    Arms: false,
    Waist: false,
    Legs: false,
  } as Record<ArmorSlot, boolean>,
});

const piecesError = ref<string | null>(null);

const submit = () => {
  const selectedSlots = Object.entries(form.pieces)
    .filter(([, value]) => value)
    .map(([slot]) => slot);

  if (selectedSlots.length === 0) {
    piecesError.value = 'Please select at least one armor piece.';
    return;
  }

  piecesError.value = null;

  form.post('/api/admin/armor-sets', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      piecesError.value = null;
      emit('armor-updated');
    },
  });
};

const isMonsterSource = computed(() => form.source_type.trim().toLowerCase() === 'monster');
watch(() => form.source_type, (val) => {
  if (val.trim().toLowerCase() !== 'monster') {
    form.monster_id = null;
  }
});
</script>

<template>
  <div class="relative overflow-hidden rounded-xl border border-[#b5b2a3] dark:border-[#2c2c21]/50 bg-[#e8e6dd] dark:bg-[#1a1a14] h-full w-full">
    <PlaceholderPattern opacity="30%" />
    <div class="relative flex flex-col justify-between p-6 h-full min-h-[320px]">
      <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa] mb-4">Add Armor Set</h2>

      <form @submit.prevent="submit" class="flex flex-col justify-between flex-1 space-y-4">
        <div class="flex-1 space-y-4">
          <div>
            <input
              v-model="form.name"
              type="text"
              placeholder="Armor set name"
              class="w-full rounded border border-[#4f4f3a]/40 bg-[#f0efea] dark:bg-[#1f1f18] px-3 py-2 text-sm text-[#1c1c16] dark:text-[#e8e8d0] placeholder-[#73735f] dark:placeholder-[#9f9f80] focus:outline-none focus:ring-2 focus:ring-[#8c886d] dark:focus:ring-[#8c886d]"
            />
            <p v-if="form.errors.name" class="text-sm text-red-500 mt-1">{{ form.errors.name }}</p>
          </div>

          <div class="grid grid-cols-4 gap-4">
            <input
              v-model="form.source_type"
              type="text"
              placeholder="Source type (e.g. Monster, Ore)"
              class="col-span-3 rounded border border-[#4f4f3a]/40 bg-[#f0efea] dark:bg-[#1f1f18] px-3 py-2 text-sm text-[#1c1c16] dark:text-[#e8e8d0] placeholder-[#73735f] dark:placeholder-[#9f9f80] focus:outline-none focus:ring-2 focus:ring-[#8c886d] dark:focus:ring-[#8c886d]"
            />
            <input
            v-model.number="form.rarity"
            type="number"
            min="1"
            max="8"
            placeholder="Rarity"
            class="col-span-1 rounded border border-[#4f4f3a]/40 bg-[#f0efea] dark:bg-[#1f1f18] px-3 py-2 text-sm text-[#1c1c16] dark:text-[#e8e8d0] placeholder-[#73735f] dark:placeholder-[#9f9f80] focus:outline-none focus:ring-2 focus:ring-[#8c886d] dark:focus:ring-[#8c886d]"
            />
          </div>
          <p v-if="form.errors.source_type" class="text-sm text-red-500 mt-1">{{ form.errors.source_type }}</p>
          <p v-if="form.errors.rarity" class="text-sm text-red-500 mt-1">{{ form.errors.rarity }}</p>

          <div>
            <select
              v-model="form.monster_id"
              :disabled="!isMonsterSource"
              class="w-full rounded border border-[#4f4f3a]/40 bg-[#f0efea] dark:bg-[#1f1f18] px-3 py-2 text-sm text-[#1c1c16] dark:text-[#e8e8d0] focus:outline-none focus:ring-2 focus:ring-[#8c886d] dark:focus:ring-[#8c886d] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <option :value="null">Select Monster</option>
              <option v-for="monster in monsters" :key="monster.id" :value="monster.id">
                {{ monster.name }}
              </option>
            </select>
            <p v-if="form.errors.monster_id" class="text-sm text-red-500 mt-1">{{ form.errors.monster_id }}</p>
          </div>


          <label class="flex items-center gap-2 text-sm text-[#2e2e23] dark:text-[#eaeac5]">
            <input
              type="checkbox"
              v-model="form.event_only"
              class="rounded border-[#4f4f3a]/40 text-[#5a5a3a] bg-[#f0efea] dark:bg-[#1f1f18]"
            />
            Event Only
          </label>

          <div>
            <h3 class="text-sm font-medium text-[#4f4f3a] dark:text-[#d2d2aa] mb-2">Armor Pieces</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
              <label v-for="slot in slotKeys" :key="slot" class="flex items-center gap-2 text-sm text-[#2e2e23] dark:text-[#eaeac5]">
                <input
                  type="checkbox"
                  v-model="form.pieces[slot]"
                  class="rounded border-[#4f4f3a]/40 text-[#5a5a3a] bg-[#f0efea] dark:bg-[#1f1f18]"
                />
                {{ slot }}
              </label>
            </div>
            <p v-if="piecesError" class="text-sm text-red-500 mt-1">{{ piecesError }}</p>
          </div>
          <button
            type="submit"
            :disabled="form.processing"
            class="self-start inline-flex items-center px-4 py-2 rounded-md bg-[#5a5a3a] text-white dark:bg-[#7f7f54] hover:bg-[#6b6b48] dark:hover:bg-[#99996a] focus:outline-none text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="form.processing">Saving...</span>
            <span v-else>Save</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>