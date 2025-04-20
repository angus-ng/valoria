<script setup lang="ts">
import { type Habitat } from '@/types/monster';
import AddHabitat from '../../components/habitat/AddHabitat.vue';
import HabitatList from '../../components/habitat/HabitatList.vue';

defineProps<{
  habitats: Habitat[];
}>();

const emit = defineEmits(['updated']);

const handleUpdated = () => {
  emit('updated');
};
</script>

<template>
  <div class="grid gap-4 md:grid-cols-2 px-4 pt-4">
    <div class="grid-cols-3 md:grid-cols-1">
      <HabitatList :habitats="habitats" @updated="handleUpdated" />
    </div>
    <div class="grid-cols-3 md:grid-cols-1">
      <AddHabitat @updated="handleUpdated" />
    </div>
  </div>
</template>