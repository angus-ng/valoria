<script setup lang="ts">
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import ConfirmationDialog from '@/components/ConfirmationDialog.vue';
import { type Monster } from '@/types/monster';
import { Page } from '@inertiajs/inertia';
import { AppPageProps } from '@/types/inertia';

defineProps<{
  monsters: Monster[];
}>();

const emit = defineEmits(['monsters-updated']);

const selectedMonsterId = ref<number | null>(null);
const showConfirm = ref(false);

const askDelete = (id: number) => {
  selectedMonsterId.value = id;
  showConfirm.value = true;
};

const cancelDelete = () => {
  selectedMonsterId.value = null;
  showConfirm.value = false;
};

const confirmDelete = () => {
  if (selectedMonsterId.value !== null) {
    router.delete(`/api/admin/monsters/${selectedMonsterId.value}`, {
      preserveScroll: true,
      onSuccess: () => {
        emit('monsters-updated');
        showConfirm.value = false;
        selectedMonsterId.value = null;
      },
    });
  }
};

const { props: pageProps } = usePage() as unknown as Page<AppPageProps>;
const isAdmin = computed(() => pageProps.auth?.user?.role === 'admin');
</script>

<template>
  <ConfirmationDialog
    :show="showConfirm"
    title="Delete Monster"
    message="Are you sure you want to delete this monster?"
    @confirm="confirmDelete"
    @cancel="cancelDelete"
  />

  <div class="relative overflow-hidden rounded-xl border border-[#3c3c2f]/50 dark:border-[#2c2c21] md:col-span-1 h-full">
    <PlaceholderPattern opacity="30%" />

    <div class="relative p-6 space-y-4">
      <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa]">Monsters</h2>
      <ul class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <li
          v-for="monster in monsters"
          :key="monster.id"
          class="flex items-center justify-between p-2 rounded border border-[#3c3c2f]/20 dark:border-[#2c2c21]/30 bg-white/70 dark:bg-[#1f1f18]/60"
        >
          <div class="flex items-center gap-3">
            <img
              v-if="monster.icon_url"
              :src="monster.icon_url"
              alt=""
              class="w-6 h-6 object-contain rounded border border-[#3c3c2f]/30 dark:border-[#2c2c21]/40"
            />
            <span class="text-sm text-[#2e2e23] dark:text-[#eaeac5]">{{ monster.name }}</span>
          </div>
          <div class="flex gap-2">
            <button
              class="text-xs px-2 py-1 rounded bg-[#5a5a3a] text-white hover:bg-[#6b6b48] dark:bg-[#7f7f54] dark:hover:bg-[#99996a] transition"
              @click="router.visit(`/monsters/${monster.slug}`)"
            >
              View
            </button>
            <button
              v-if="isAdmin"
              class="text-xs px-2 py-1 rounded bg-red-600/80 text-white hover:bg-red-700 transition"
              @click="askDelete(monster.id)"
            >
              Delete
            </button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

