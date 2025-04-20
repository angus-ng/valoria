<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import ManageHabitat from '@/components/habitat/ManageHabitat.vue';
import ManageMonster from '@/components/monster/ManageMonster.vue';
import { onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Admin Dashboard',
    href: '/admin/dashboard',
  },
];

const habitats = ref([]);

const fetchHabitats = async () => {
  try {
    const res = await fetch('/api/habitats');
    habitats.value = await res.json();
  } catch (error) {
    console.error('Failed to fetch habitats:', error);
  }
};

const monsters = ref([]);

const fetchMonsters = async () => {
  try {
    const res = await fetch('/api/monsters/all');
    monsters.value = await res.json();
  } catch (error) {
    console.error('Failed to fetch monsters:', error);
  }
};

onMounted(() => {
  fetchHabitats();
  fetchMonsters();
});

const refreshMonsters = () => fetchMonsters();
const refreshHabitats = () => fetchHabitats();
</script>

<template>
  <Head title="Admin Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="grid gap-4 md:grid-cols-3">
      <div class="col-span-3">
        <ManageHabitat :habitats="habitats" @updated="refreshHabitats" />
      </div>
      <div class="col-span-3">
        <ManageMonster
        :monsters="monsters"
        :habitats="habitats"
        @monsters-updated="refreshMonsters"
        />
      </div>
    </div>
  </AppLayout>
</template>
