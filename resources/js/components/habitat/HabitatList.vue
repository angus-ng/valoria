<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { defineEmits, defineProps, ref } from 'vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import ConfirmationDialog from '@/components/ConfirmationDialog.vue';
import { type Habitat } from '@/types/monster';

const selectedHabitatId = ref<number | null>(null);
const showConfirm = ref(false);

const askDelete = (id: number) => {
  selectedHabitatId.value = id;
  showConfirm.value = true;
};

const confirmDelete = () => {
  if (!selectedHabitatId.value) return;

  router.delete(`/api/admin/habitats/${selectedHabitatId.value}`, {
    onSuccess: () => {
      emit('updated');
      selectedHabitatId.value = null;
      showConfirm.value = false;
    },
  });
};

const cancelDelete = () => {
  selectedHabitatId.value = null;
  showConfirm.value = false;
};

defineProps<{
  habitats: Habitat[];
}>();

const emit = defineEmits(['updated']);

</script>

<template>
  <ConfirmationDialog
  :show="showConfirm"
  title="Delete Habitat"
  message="Are you sure you want to delete this habitat? This action cannot be undone."
  @confirm="confirmDelete"
  @cancel="cancelDelete"
  />
  <div class="relative overflow-hidden rounded-xl border border-[#3c3c2f]/50 dark:border-[#2c2c21] flex items-center h-full">
    <PlaceholderPattern opacity="30%"/>
    <div class="relative w-full p-6 space-y-3">
      <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa]">Existing Habitats</h2>
      <ul class="divide-y divide-[#3c3c2f]/30 dark:divide-[#2c2c21]/40">
        <li
          v-for="habitat in habitats"
          :key="habitat.id"
          class="flex items-center justify-between py-2"
        >
          <div class="flex items-center gap-3">
            <img
              v-if="habitat.icon_url"
              :src="habitat.icon_url"
              alt=""
              class="w-6 h-6 object-contain rounded border border-[#3c3c2f]/30 dark:border-[#2c2c21]/40"
            />
            <span class="text-sm text-[#2e2e23] dark:text-[#eaeac5]">{{ habitat.name }}</span>
          </div>
          <button
            @click="askDelete(habitat.id)"
            class="text-xs px-2 py-1 rounded bg-red-600/80 text-white hover:bg-red-700 transition"
          >
            Delete
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>

