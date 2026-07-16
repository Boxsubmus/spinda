<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, nextTick } from 'vue'
import { apiRoutes } from '../../routes'
import ActionButton from '../components/ActionButton.vue'

const props = defineProps({
  beatmapsetId: { type: [Number, String], required: true },
})

const emit = defineEmits(['posted'])

const form = useForm({
  content: '',
})

const textarea = ref(null)

function autoResize() {
  const el = textarea.value
  if (!el) return
  el.style.height = 'auto'
  el.style.height = `${el.scrollHeight}px`
}

function submit() {
  form.post(apiRoutes.commentPost(props.beatmapsetId), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('content')
      nextTick(autoResize)
      emit('posted')
    },
  })
}
</script>

<template>
  <form @submit.prevent="submit" class="flex flex-col gap-2">
    <textarea
        ref="textarea"
        v-model="form.content"
        @input="autoResize"
        rows="2"
        maxlength="1000"
        placeholder="Write a comment..."
        class="resize-none overflow-hidden bg-zinc-900 rounded-lg p-3 text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"
        style="font-family: Inter;"
    ></textarea>

    <div v-if="form.errors.content" class="text-red-400 text-sm">
      {{ form.errors.content }}
    </div>

    <div class="flex justify-between items-center">
      <span class="text-xs text-zinc-500">{{ form.content.length }}/1000</span>
      <ActionButton
        label="Post"
        type="submit"
        icon="fa-paper-plane"
        :disabled="form.processing || !form.content.trim()"
      />
    </div>
  </form>
</template>