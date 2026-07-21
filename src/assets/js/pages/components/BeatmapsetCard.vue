<script setup>
import { Link } from '@inertiajs/vue3'
import { routes } from '../../routes';
import BeatmapStatus from '../beatmapsets/BeatmapStatus.vue';

defineProps({
  beatmapset: { type: Object, required: true },
})
</script>

<template>
  <div class="group relative block overflow-hidden rounded-2xl basic-outline">
    <!-- Blurred background cover -->
    <div
      class="absolute inset-0 bg-cover bg-center blur-sm scale-110"
      :style="{ backgroundImage: `url('${beatmapset.images.list}')` }"
    ></div>

    <!-- Overlay for readability -->
    <div class="absolute inset-0 bg-zinc-900/80"></div>

    <!-- Hover dimming overlay -->
    <div class="absolute inset-0 bg-black opacity-0 transition-opacity duration-1 group-hover:opacity-40"></div>

    <!-- link -->
    <Link :href="routes.beatmapsetShow(beatmapset.id)" class="absolute inset-0 z-10"></Link>

    <!-- Content -->
    <div class="relative flex flex-row gap-4 p-2">
      <img class="w-32 rounded-xl" :src="beatmapset.images.list" />
      <div class="flex flex-col text-shadow-sm">
        <span class="text-2xl font-semibold">{{ beatmapset.title }}</span>
        <span class="text-xl">{{ beatmapset.artist }}</span>
        <div class="text-sm">
          mapped by
          <Link
            :href="`/users/${beatmapset.author.id}`"
            class="font-semibold pointer-events-auto relative z-30 hover:underline"
          >
            {{ beatmapset.author.username }}
          </Link>
        </div>
        <div class="grow"></div>
        <div class="flex">
          <span class="bg-gray-500 py-0 px-3 text-shadow-none rounded-3xl text-center text-zinc-800 font-bold inline-flex items-center justify-center">
            <span class="pb-0.75"> UNRANKED </span>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>