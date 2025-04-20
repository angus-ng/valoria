<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { AppPageProps } from '@/types/inertia';
import { Page } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';

const page = usePage() as unknown as Page<AppPageProps>;

const showSuccess = ref(!!page.props.flash?.success);
const showError = ref(!!page.props.flash?.error);
const showInfo = ref(!!page.props.flash?.info);

onMounted(() => {
  if (showSuccess.value) setTimeout(() => (showSuccess.value = false), 4000);
  if (showError.value) setTimeout(() => (showError.value = false), 4000);
  if (showInfo.value) setTimeout(() => (showInfo.value = false), 4000);
});
</script>

<template>
  <div class="space-y-2">
    <div
      v-if="page.props.flash?.success"
      class="rounded bg-green-100 px-4 py-2 text-sm text-green-800 dark:bg-green-900/40 dark:text-green-200"
    >
      {{ page.props.flash.success }}
    </div>
    <div
      v-if="page.props.flash?.error"
      class="rounded bg-red-100 px-4 py-2 text-sm text-red-800 dark:bg-red-900/40 dark:text-red-200"
    >
      {{ page.props.flash.error }}
    </div>
    <div
      v-if="page.props.flash?.info"
      class="rounded bg-blue-100 px-4 py-2 text-sm text-blue-800 dark:bg-blue-900/40 dark:text-blue-200"
    >
      {{ page.props.flash.info }}
    </div>
  </div>
</template>