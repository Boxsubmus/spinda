<script setup>
import AppLayout from '../layouts/AppLayout.vue';
import Markdown from '../components/Markdown.vue';
import CommentVote from './CommentVote.vue';
import ActionButton from '../components/ActionButton.vue';
import CommentForm from './CommentForm.vue';
import BeatmapStatus from './BeatmapStatus.vue';

import { useTimeAgo } from '@vueuse/core';

import { Link, usePage } from '@inertiajs/vue3';
import { routes } from '../../routes.js';
import { ref, computed } from 'vue';

const page = usePage()
const auth = computed(() => page.props.auth)

defineOptions({
    layout: [AppLayout, { title: 'beatmap info' }]
})
const props = defineProps({
    beatmapset: Object,
    comments: Object
})

</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex flex-col rounded-2xl overflow-hidden shadow basic-border">

            <!-- header -->
            <div class="relative grid overflow-hidden w-full">

                <!-- banner cover -->
                <div class="absolute inset-0 bg-cover bg-center bg-[radial-gradient(circle_at_center,#1f2937,transparent_70%)]"
                    >
                </div>

                <!-- dark overlay so text stays readable -->
                <div class="absolute inset-0" style="background-color: hsla(0, 0%, 0%, 0.2);"></div>

                <div class="z-10 flex p-4 pb-0">
                    <div v-for="diff in beatmapset.difficulties" class="bg-black/40 p-3 rounded-xl">
                        <div class="flex">
                            <span class="rounded-full border-4 w-7 h-7" :style="{'border-color': '#' + diff.color}"></span>
                            <span class="pl-2">{{ diff.name }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="relative z-10 flex flex-row gap-4 p-4">
                    <img class="rounded-lg shadow w-52 h-52" :src="beatmapset.coverUrl"/>

                    <div class="grid content-between text-shadow-lg grow">
                        <!-- title and artist -->
                        <div class="flex flex-col">
                            <p class="text-5xl">{{ beatmapset.title }}</p>
                            <p class="text-3xl">{{ beatmapset.artist }}</p>
                        </div>

                        <!-- mapper -->
                        <div class="flex items-center gap-2.5">
                            <img class="rounded-lg w-16 shadow" :src="beatmapset.author.avatarURL"/>
                            <div class="flex flex-col justify-center">
                                <div>
                                    mapped by
                                    <Link :href="routes.userShow(beatmapset.author.id)" class="font-semibold hover:underline" href="">{{ beatmapset.author.username }}</Link>
                                </div>
                                <p>submitted {{ useTimeAgo(beatmapset.createdAt.date + beatmapset.createdAt.timezone) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- box thing -->
                    <div class="flex flex-col relative gap-4">

                        <!-- status -->
                        <div class="flex mx-auto mr-0">
                            <BeatmapStatus :beatmapset="beatmapset" />
                        </div>

                        <!-- box -->
                        <div class="grid grid-cols-2 gap-1">
                            
                            <span class="bg-zinc-800 px-4 p-2 rounded basic-border">
                                <i class="fas fa-download"></i>
                                {{ beatmapset.downloads }}
                            </span>

                            <span class="bg-zinc-800 px-4 p-2 rounded basic-border">
                                <i class="fas fa-heart"></i>
                                {{ beatmapset.favorites }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- buttons -->
                <div class="z-10 flex flex-row p-4 gap-2 basic-border-t" style="background-color: hsla(0, 0%, 0%, 0.3);">
                    <ActionButton
                        label="Download"
                        icon="fas fa-download"
                    />

                    <div v-if="auth.user.roles.includes('ROLE_ADMIN')">
                        <ActionButton
                            label="Award"
                            icon="fas fa-award"
                        />
                    </div>

                    <div class="grow"></div>

                    <ActionButton
                        icon="fas fa-heart"
                    />
                </div>

            </div>

            <div class="bg-zinc-800 px-6 py-4 basic-border-t">
                <span class="text-lg font-semibold">
                    Description
                </span>
                <Markdown :source="beatmapset.description" />
            </div>
        </div>

        <div class="bg-zinc-800 rounded-2xl overflow-hidden shadow py-6 pb-0 basic-border">
            <h1 class="px-6 pb-6 text-2xl">comments</h1>

            <div class="p-4">
                <CommentForm
                    :beatmapset-id="beatmapset.id"
                />
            </div>


            <div v-for="comment in comments">
                <div class="basic-border-t p-6">
                    <div class="flex gap-3">
                        <Link :href="routes.userShow(comment.author.id)" class="w-12 h-12 flex-none">
                            <span class="inline-block w-full h-0 rounded bg-contain" :style="{ 'padding-bottom': '100%', 'background-image': 'url(' + comment.author.avatarURL + ')'}"></span>
                        </Link>
                        <div class="flex flex-1 flex-col">
                            <div class="flex">
                                <Link :href="routes.userShow(comment.author.id)" class="font-semibold text-xl hover:underline block -mt-2 -mb-1">
                                    {{ comment.author.username }}
                                </Link>
                                <div class="ml-auto flex">
                                <div class="-mt-1.5 text-white/40">
                                    <span>{{ useTimeAgo(comment.createdAt.date + comment.createdAt.timezone) }}</span>
                                </div>
                                </div>

                            </div>

                            <div style="font-family: Inter;">
                                {{ comment.content }}
                            </div>

                            <div class="flex items-baseline flex-wrap gap-2">
                                <div class="font-semibold">
                                </div>
                            </div>
                            
                                <CommentVote
                                :comment-id="comment.id"
                                :initial-likes="comment.likes"
                                :initial-dislikes="comment.dislikes"
                                :initial-user-vote="comment.userVote"
                                />

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>