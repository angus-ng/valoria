<script setup lang="ts">
defineProps<{
  show: boolean
  title?: string
  message?: string
}>()

const emit = defineEmits<{
  (e: 'cancel'): void
  (e: 'confirm'): void
}>()

const cancel = () => emit('cancel')
const confirm = () => emit('confirm')
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-[#1f1f18] rounded-lg p-6 w-full max-w-sm shadow-xl">
      <h3 class="text-lg font-semibold text-[#2e2e23] dark:text-[#eaeac5] mb-4">{{ title }}</h3>
      <p class="text-sm text-[#444] dark:text-[#aaa] mb-6">{{ message }}</p>
      <div class="flex justify-end gap-2">
        <button @click="cancel" class="px-4 py-2 text-sm text-white hover:underline">Cancel</button>
        <button @click="confirm" class="px-4 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700">Confirm</button>
      </div>
    </div>
  </div>
</template>