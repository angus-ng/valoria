<script setup lang="ts">
import AddMonster from '@/components/monster/AddMonster.vue';
import MonsterList from '@/components/monster/MonsterList.vue';
import AddArmor from '@/components/armor/AddArmor.vue';
import { type Habitat, type Monster } from '@/types/monster';
import ArmorSetList from '../armor/ArmorSetList.vue';
import { type ArmorSet } from '@/components/armor/ArmorSetList.vue'; 
import { onMounted, ref } from 'vue';

defineProps<{
  habitats: Habitat[];
  monsters: Monster[];
}>();

const emit = defineEmits(['monsters-updated', 'armor-sets-updated']);

const handleMonstersUpdated = () => {
  emit('monsters-updated');
};

const handleArmorsUpdated = () => {
  fetchArmorSets();
};
const armorSets = ref<ArmorSet[]>([]);

const fetchArmorSets = async () => {
  try {
    const res = await fetch('/api/armor-sets');
    armorSets.value = await res.json();
  } catch (error) {
    console.error('Failed to fetch armor sets:', error);
  }
};

onMounted(() => {
  fetchArmorSets();
});
</script>

<template>
  <div
    class="grid gap-4 px-4 pt-0 pb-4"
    :class="{
      'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4': true,
    }"
  >
    <div class="col-span-3 [@media(min-width:1280px)]:col-span-1">
      <AddMonster :habitats="habitats" @monsters-updated="handleMonstersUpdated" />
    </div>
    <div class="col-span-3 lg:col-span-3 pr-0">
      <MonsterList :monsters="monsters" @monsters-updated="handleMonstersUpdated" />
    </div>
    <div class="col-span-3 lg:col-span-3">
      <ArmorSetList :armor-sets="armorSets" @armor-updated="handleArmorsUpdated" />
    </div>
    <div class="col-span-3 [@media(min-width:1280px)]:col-span-1">
      <AddArmor :monsters="monsters" @armor-updated="handleArmorsUpdated" />
    </div>
  </div>
</template>

