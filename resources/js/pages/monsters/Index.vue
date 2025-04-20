<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import MonsterFilters from '@/components/monster/MonsterFilters.vue';
import MonsterList from '@/components/monster/MonsterList.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { BreadcrumbItem } from '@/types';
import { type Monster } from '@/types/monster';

const props = defineProps<{
  monsters: Monster[];
  filters: {
    search: string;
    habitats: number[];
    crownable: boolean;
  };
}>();

const filters = ref({
  search: props.filters.search,
  habitats: props.filters.habitats,
  crownable: props.filters.crownable,
});

const applyFilters = (f: typeof filters.value) => {
  filters.value = f;

  router.get(route('monsters.index'), f, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  });
};

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Monsters',
    href: '/monsters',
  },
];
</script>

<template>
  <Head title="Monsters" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="grid md:grid-cols-4 gap-6 p-4">
      <div>
        <MonsterFilters :filters="filters" @filter-change="applyFilters" />
      </div>
      <div class="md:col-span-3">
        <MonsterList :monsters="props.monsters" />
      </div>
    </div>
  </AppLayout>
</template>
