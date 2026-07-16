<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: { type: String, default: null },
  icon: { type: String, required: true },
  url: { type: String, default: null },
  type: { type: String, default: 'button' },
  disabled: { type: Boolean, default: false },
  class: { type: String, default: null }
})

const tag = computed(() => (props.url !== null ? 'a' : 'button'))

const builtinclasses = `
  flex items-center justify-between height-4 cursor-pointer
  bg-cyan-600 transition-colors hover:bg-cyan-400
  p-3 px-4 rounded shadow gap-3 h-10
  text-shadow-sm
  items-center
  border border-white/30
  disabled:opacity-50 disabled:cursor-not-allowed
`

let finalClasses = builtinclasses;
if (props.class)
{
  finalClasses += props.class;
}

</script>

<template>
  <component
    :is="tag"
    :href="url ?? undefined"
    :type="tag === 'button' ? type : undefined"
    :disabled="tag === 'button' ? disabled : undefined"
    :class="finalClasses"
  >
    <span v-if="label !== null" class="flex flex-col justify-center font-semibold">
      {{ label }}
    </span>
    <span class="text-xl justify-center flex">
      <i class="m-0 fas" :class="icon"></i>
    </span>
  </component>
</template>