<script setup>
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({
  commentId: { type: [Number, String], required: true },
  initialLikes: { type: Number, required: true },
  initialDislikes: { type: Number, required: true },
  initialUserVote: { type: Number, default: null }, // 1, -1, or null
})

const likes = ref(props.initialLikes)
const dislikes = ref(props.initialDislikes)
const userVote = ref(props.initialUserVote)
const isVoting = ref(false)

async function vote(type) {
  if (isVoting.value)
    return;

  isVoting.value = true;

  const newValue = type === 'like' ? 1 : -1;
  const previousVote = userVote.value;

  // optimistic update
  if (previousVote === newValue) {
    // toggling off
    userVote.value = null;
    if (type === 'like')
        likes.value--;
    else dislikes.value--;
  } else {
    userVote.value = newValue
    if (type === 'like') {
      likes.value++;
      if (previousVote === -1)
        dislikes.value--;
    } else {
      dislikes.value++;
      if (previousVote === 1)
        likes.value--;
    }
  }

  try {
    const response = await axios.post(`/api/maps/comments/${props.commentId}/vote/${type}`);
    // trust the server's counts as source of truth
    likes.value = response.data.likes;
    dislikes.value = response.data.dislikes;
    userVote.value = response.data.state;
  } catch (e) {
    // revert on failure
    userVote.value = previousVote
    likes.value = props.initialLikes
    dislikes.value = props.initialDislikes
    console.error('Vote failed', e)
  } finally {
    isVoting.value = false
  }
}
</script>

<template>
  <div class="flex gap-2 text-zinc-300">
    <button
      @click="vote('like')"
      :disabled="isVoting"
      class="cursor-pointer flex items-center gap-1"
      :class="{ 'text-green-300': userVote === 1 }"
    >
      <i class="fas fa-thumbs-up"></i>
      <span>{{ likes }}</span>
    </button>
    <button
      @click="vote('dislike')"
      :disabled="isVoting"
      class="cursor-pointer flex items-center gap-1"
      :class="{ 'text-red-400': userVote === -1 }"
    >
      <i class="fas fa-thumbs-down"></i>
      <span>{{ dislikes }}</span>
    </button>
  </div>
</template>