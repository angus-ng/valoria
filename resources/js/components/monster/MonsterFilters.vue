<script setup lang="ts">
import { ref, onMounted } from 'vue';
import PlaceholderPattern from '../PlaceholderPattern.vue';

const props = defineProps<{
  filters: {
    search: string;
    habitats: number[];
    crownable: boolean;
  };
}>();

const emit = defineEmits<{
  (e: 'filter-change', filters: { search: string; habitats: number[]; crownable: boolean }): void;
}>();

const search = ref(props.filters.search);
const selectedHabitats = ref<number[]>(props.filters.habitats);
const crownable = ref(props.filters.crownable);
const habitats = ref<{ id: number; name: string }[]>([]);

const fetchHabitats = async () => {
  const res = await fetch('/api/habitats');
  habitats.value = await res.json();

  updateFilters();
};

const updateFilters = () => {
  emit('filter-change', {
    search: search.value,
    habitats: selectedHabitats.value,
    crownable: crownable.value,
  });
};

onMounted(fetchHabitats);
</script>

<template>
  <div
    class="relative overflow-hidden rounded-xl border border-[#3c3c2f]/30 dark:border-[#2c2c21]/50 
           bg-[#e2e2dc]/70 dark:bg-[#1a1a14]/60 p-6 space-y-6 h-full"
  >
    <PlaceholderPattern opacity="30%" />
    <div class="relative space-y-4">
      <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa]">Filters</h2>

      <div>
        <input
          v-model="search"
          @input="updateFilters"
          type="text"
          placeholder="Search monsters..."
          class="w-full rounded border border-[#4f4f3a]/30 bg-[#f0f0ea] dark:bg-[#1f1f18] px-3 py-2 text-sm text-[#1c1c16] dark:text-[#e8e8d0] placeholder-[#5f5f4a] dark:placeholder-[#9f9f80] focus:outline-none focus:ring-2 focus:ring-[#6a6a50]"
        />
      </div>

      <div>
        <h3 class="text-sm font-medium text-[#4f4f3a] dark:text-[#d2d2aa] mb-2">Habitats</h3>
        <div class="flex flex-col gap-1">
          <label
            v-for="habitat in habitats"
            :key="habitat.id"
            class="flex items-center gap-2 text-sm text-[#2e2e23] dark:text-[#eaeac5]"
          >
            <input
              type="checkbox"
              :value="habitat.id"
              v-model="selectedHabitats"
              @change="updateFilters"
              class="rounded border-[#4f4f3a]/40 text-[#5a5a3a] bg-[#f8f8f5] dark:bg-[#1f1f18]"
            />
            {{ habitat.name }}
          </label>
        </div>
      </div>

      <h3 class="text-sm font-medium text-[#4f4f3a] dark:text-[#d2d2aa] mb-2">Crownable</h3>
      <label class="flex items-center gap-2 text-sm text-[#2e2e23] dark:text-[#eaeac5]">
        <input
          type="checkbox"
          v-model="crownable"
          @change="updateFilters"
          class="rounded border-[#4f4f3a]/40 text-[#5a5a3a] bg-[#f8f8f5] dark:bg-[#1f1f18]"
        />
        Crownable
      </label>
    </div>
  </div>
</template>
