<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { type Habitat } from '@/types/monster';

defineProps<{
  habitats: Habitat[];
}>();

const emit = defineEmits(['monsters-updated']);

const form = useForm({
  name: '',
  icon_url: '',
  crownable: false,
  habitats: [] as number[],
});

const toggleHabitat = (id: number) => {
  if (form.habitats.includes(id)) {
    form.habitats = form.habitats.filter(h => h !== id);
  } else {
    form.habitats.push(id);
  }
};

const submit = () => {
  form.post('/api/admin/monsters', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      emit('monsters-updated');
    },
  });
};
</script>

<template>
  <div class="relative overflow-hidden rounded-xl border border-[#b5b2a3] dark:border-[#2c2c21]/50 bg-[#e8e6dd] dark:bg-[#1a1a14] h-full">
    <PlaceholderPattern opacity="30%" />
    <div class="relative flex flex-col justify-between p-6 h-full min-h-[320px]">
      <h2 class="text-lg font-semibold text-[#4f4f3a] dark:text-[#d2d2aa] mb-4">Add Monster</h2>

      <form @submit.prevent="submit" class="flex flex-col justify-start flex-1 space-y-4">
        <div class="flex-1 space-y-4">
          <div>
            <input
              v-model="form.name"
              type="text"
              placeholder="Monster name"
              class="w-full rounded border border-[#4f4f3a]/40 bg-[#f0efea] dark:bg-[#1f1f18] px-3 py-2 text-sm text-[#1c1c16] dark:text-[#e8e8d0] placeholder-[#73735f] dark:placeholder-[#9f9f80] focus:outline-none focus:ring-2 focus:ring-[#8c886d] dark:focus:ring-[#8c886d]"
            />
            <p v-if="form.errors.name" class="text-sm text-red-500 mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <input
              v-model="form.icon_url"
              type="text"
              placeholder="Icon URL"
              class="w-full rounded border border-[#4f4f3a]/40 bg-[#f0efea] dark:bg-[#1f1f18] px-3 py-2 text-sm text-[#1c1c16] dark:text-[#e8e8d0] placeholder-[#73735f] dark:placeholder-[#9f9f80] focus:outline-none focus:ring-2 focus:ring-[#8c886d] dark:focus:ring-[#8c886d]"
            />
            <p v-if="form.errors.icon_url" class="text-sm text-red-500 mt-1">{{ form.errors.icon_url }}</p>
          </div>

          <label class="flex items-center gap-3 text-sm text-[#2e2e23] dark:text-[#eaeac5]">
            <input
              type="checkbox"
              v-model="form.crownable"
              id="crownable"
              class="rounded border-[#4f4f3a]/40 text-[#5a5a3a] bg-[#f0efea] dark:bg-[#1f1f18]"
            />
            Crownable
          </label>

          <div>
            <label class="block mb-2 text-sm font-medium text-[#4f4f3a] dark:text-[#d2d2aa]">Habitats</label>
            <div class="grid grid-cols-1 min-[1250px]:grid-cols-2 gap-2">
              <label
                v-for="habitat in habitats"
                :key="habitat.id"
                class="flex items-center gap-2 text-sm text-[#2e2e23] dark:text-[#eaeac5]"
              >
                <input
                  type="checkbox"
                  :value="habitat.id"
                  :checked="form.habitats.includes(habitat.id)"
                  @change="toggleHabitat(habitat.id)"
                  class="rounded border-[#4f4f3a]/40 text-[#5a5a3a] bg-[#f0efea] dark:bg-[#1f1f18]"
                />
                {{ habitat.name }}
              </label>
            </div>
            <p v-if="form.errors.habitats" class="text-sm text-red-500 mt-1">{{ form.errors.habitats }}</p>
          </div>
          <button
            type="submit"
            :disabled="form.processing"
            class="self-start inline-flex items-center px-4 py-2 rounded-md bg-[#5a5a3a] text-white dark:bg-[#7f7f54] hover:bg-[#6b6b48] dark:hover:bg-[#99996a] focus:outline-none text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="form.processing">Saving...</span>
            <span v-else>Save</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>
