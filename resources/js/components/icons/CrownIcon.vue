<template>
  <svg
    fill="none"
    stroke-width="2"
    xmlns="http://www.w3.org/2000/svg"
    class="icon icon-tabler icon-tabler-crown"
    viewBox="0 0 24 24"
    stroke="currentColor"
    stroke-linecap="round"
    stroke-linejoin="round"
    :class="classes"
  >
    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
    <path d="M12 6l4 6l5 -4l-2 10h-14l-2 -10l5 4z" />
  </svg>
</template>

<script setup lang="ts">
defineProps<{ classes?: string }>()
</script>