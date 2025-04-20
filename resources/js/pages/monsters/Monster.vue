<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { type Monster } from '@/types/monster';
import { Head } from '@inertiajs/vue3';
const props = defineProps<{
  monster: Monster;
}>();
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Monsters', href: '/monsters' },
    {
        title: `${props.monster.name}`,
        href: `/monster/${props.monster.slug}`,
    },
];
</script>
<template>
  <Head :title="monster.name" />
  <AppLayout :breadcrumbs="breadcrumbs">
  <div class="px-4 py-8 space-y-8 w-full">
    <div class="flex items-center gap-4">
      <img
        v-if="monster.icon_url"
        :src="monster.icon_url"
        alt="Monster icon"
        class="w-16 h-16 object-contain border border-neutral-300 dark:border-neutral-700 rounded"
      />
      <h1 class="text-2xl font-bold text-[#2e2e23] dark:text-[#eaeac5]">{{ monster.name }}</h1>
    </div>
    <section>
      <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa] mb-2">Habitats</h2>
      <div class="flex flex-wrap gap-4">
        <div
          v-for="habitat in monster.habitats"
          :key="habitat.id"
          class="flex items-center gap-2 px-3 py-2 rounded bg-[#f8f8f5] dark:bg-[#1f1f18] border border-[#3c3c2f]/30 dark:border-[#2c2c21]/40"
        >
          <img v-if="habitat.icon_url" :src="habitat.icon_url" class="w-5 h-5 object-contain" />
          <span class="text-sm text-[#2e2e23] dark:text-[#eaeac5]">{{ habitat.name }}</span>
        </div>
      </div>
    </section>
    <section>
      <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa] mb-2">Crowns</h2>
      <div class="flex gap-3 text-sm text-[#2e2e23] dark:text-[#eaeac5]">
        <span
          v-if="monster.crowns.find(c => c.crown_type === 'small')"
          class="px-2 py-1 rounded bg-amber-100 dark:bg-amber-800/20"
        >
          Small Crown
        </span>
        <span
          v-if="monster.crowns.find(c => c.crown_type === 'large')"
          class="px-2 py-1 rounded bg-yellow-100 dark:bg-yellow-800/20"
        >
          Large Crown
        </span>
        <span v-if="!monster.crowns.length" class="italic opacity-60">No crowns</span>
      </div>
    </section>
    <section>
      <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa] mb-2">Armor Sets</h2>
      <p class="text-sm text-neutral-500 dark:text-neutral-400 italic">Coming soon...</p>
    </section>
  </div>
  </AppLayout>
</template>
