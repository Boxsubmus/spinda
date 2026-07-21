<script setup>
import AppLayout from '../layouts/AppLayout.vue';
defineOptions({
    layout: [AppLayout, { title: 'beatmap listing' }]
})

import BeatmapsetCard from '../components/BeatmapsetCard.vue';
import Paginate from '../components/Paginate.vue';

const props = defineProps({
    beatmapsets: Object,
    pagination: { type: Object, required: true },
})

</script>

<template>

            <Paginate
            :pagination="pagination"
            base-url="/maps"    
        />

    <div class="grid grid-cols-2 gap-4">
    <div v-for="beatmapset in beatmapsets">
        <BeatmapsetCard
        :beatmapset="beatmapset"/>
    </div>
    </div>

            <Paginate
            :pagination="pagination"
            base-url="/maps"    
        />

</template>