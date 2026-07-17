<script setup>
import AppLayout from '../layouts/AppLayout.vue';
import Markdown from '../components/Markdown.vue';
import BeatmapsetCard from '../components/BeatmapsetCard.vue';
import CountryFlag from '../components/CountryFlag.vue';

import { useTimeAgo } from '@vueuse/core';

defineOptions({
    layout: [AppLayout, { title: 'user profile' }]
})

defineProps({
    user: Object,
    beatmaps: Object
})

</script>

<template>

<div class="flex flex-col gap-4">
<div class="bg-zinc-800 rounded-2xl overflow-hidden shadow basic-border">
    <div
        class="h-64 bg-center bg-cover bg-[radial-gradient(circle_at_center,#1f2937,transparent_70%)]"
        style="background-image: url('https://pbs.twimg.com/profile_banners/383832535/1488939982/1500x500');">
    </div>

    <div class="flex flex-row gap-6 h-28 basic-border-t">

        <img class="rounded-2xl w-40 h-40 shadow-lg self-end mb-4 ml-4" :src="user.avatarUrl"/>

        <div class="my-auto text-shadow-xs flex flex-col gap-1">
            <span class="text-3xl">{{ user.username }}</span>
            <div class="flex flex-row gap-2">
                <img class="w-7" :src="user.countryFlagUrl"/>
                <span class="text-lg">{{ user.countryName }}</span>
            </div>
        </div>

    </div>
</div>

<div class="flex flex-row gap-4">

    <div class="flex flex-col">
        <div class="bg-zinc-800 rounded-2xl overflow-hidden shadow p-6 basic-border">
            <h1 class="text-2xl">stats</h1>
            <div class="p-2">
                <div>
                    <i class="fas fa-calendar"></i>
                    Joined {{ useTimeAgo(user.createdAt.date + user.createdAt.timezone) }}
                </div>
                <div>
                    <i class="fas fa-hammer"></i>
                    {{ user.mappingPoints }} mapping points
                </div>
            </div>
        </div>
        <div class="flex grow"></div>
    </div>


    <div class="flex flex-col grow gap-4">
        <div class="bg-zinc-800 rounded-2xl overflow-hidden shadow p-6 basic-border">
            <h1 class="text-2xl">about me!</h1>
            <div v-if="user.aboutMe">
                <Markdown :source="user.aboutMe" />
            </div>
            <div v-else>
                nothing here... :<
            </div>


        </div>

        <div class="bg-zinc-800 rounded-2xl overflow-hidden shadow p-6 basic-border">
            <h1 class="text-2xl">maps</h1>

            <div class="p-0 rounded-2xl mt-6">
                <div class="grid grid-cols-1 gap-3">
                    <div v-for="beatmap in beatmaps">
                        <BeatmapsetCard
                            :beatmapset="beatmap"
                            />
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</div>

</template>