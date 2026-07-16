<script setup>
import AppLayout from '../layouts/AppLayout.vue';
import Markdown from '../components/Markdown.vue';
import CommentVote from './CommentVote.vue';
import ActionButton from '../components/ActionButton.vue';
import CommentForm from './CommentForm.vue';

import { useTimeAgo } from '@vueuse/core';

import { Link } from '@inertiajs/vue3';
import { routes } from '../../routes.js';
import { ref } from 'vue';

defineOptions({
    layout: [AppLayout, { title: 'beatmap info' }]
})
const props = defineProps({
    beatmapset: Object
})

const totalVotes = props.beatmapset.likes + props.beatmapset.dislikes;
const likePercent = totalVotes > 0 ? (props.beatmapset.likes / totalVotes * 100) : 50;
const dislikePercent = 100 - likePercent;

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

                <!-- Content -->
                <div class="relative z-10 flex flex-row gap-4 p-4">
                    <img class="rounded-lg shadow w-52 h-52" :src="beatmapset.coverURL"/>

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
                            <span class="bg-gray-500 py-2 px-10 text-shadow-sm rounded-3xl font-bold basic-border
                                inline-flex items-center justify-center">
                                    ARCHIVE
                            </span>
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

                            <div class="col-span-2 bg-zinc-800 p-4 w-80 rounded basic-border">

                                <div class="w-full max-w-sm mx-auto">
                                    <div class="flex items-center justify-between mb-2">
                                        <button class="flex items-center gap-2 text-green-300">
                                        <i class="fas fa-thumbs-up text-lg"></i>
                                        <span class="text-sm">{{ beatmapset.likes }}</span>
                                        </button>

                                        <button class="flex items-center gap-2 text-red-400">
                                        <span class="text-sm">{{ beatmapset.dislikes }}</span>
                                        <i class="fas fa-thumbs-down text-lg -scale-x-100"></i>
                                        </button>
                                    </div>

                                    <div class="w-full h-2 bg-transparent rounded-full overflow-hidden flex gap-0.5">
                                            <div class="bg-green-300 h-full transition-all rounded" :style="{ 'width': likePercent + '%' }"></div>
                                            <div class="bg-red-400 h-full transition-all rounded" :style="{ 'width': dislikePercent + '%' }"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- buttons -->
                <div class="z-10 flex flex-row p-4 gap-2 basic-border-t" style="background-color: hsla(0, 0%, 0%, 0.3);">
                    <ActionButton
                        label="Download"
                        icon="fa-download"
                    />

                    <ActionButton
                        label="Award"
                        icon="fa-award"
                    />

                    <div class="grow"></div>

                    <ActionButton
                        icon="fa-heart"
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


            <div v-for="comment in beatmapset.comments">
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