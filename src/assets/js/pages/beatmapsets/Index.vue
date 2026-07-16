<script setup>
import AppLayout from '../layouts/AppLayout.vue';
import Markdown from '../components/Markdown.vue';

defineOptions({
    layout: [AppLayout, { title: 'beatmap info' }]
})
const props = defineProps({
    beatmapset: Object
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
                                    <a class="font-semibold hover:underline" href="">{{ beatmapset.author.username }}</a>
                                </div>
                                <p>submitted {{ beatmapset.createdAt.date }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- box thing -->
                    <div class="flex flex-col relative gap-4">

                        <!-- status -->
                        <div class="flex mx-auto mr-0">
                            <span class="bg-orange-500 py-2 px-10 text-shadow-sm rounded-3xl font-bold basic-border
                                inline-flex items-center justify-center">
                                    QUALITY
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
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-zinc-800 px-6 py-4 basic-border-t">
                <span class="text-lg font-semibold">
                    Description
                </span>
                <Markdown :source="beatmapset.description" />
            </div>
        </div>

        <div class="bg-zinc-800 rounded-2xl overflow-hidden shadow py-6 basic-border">
            <h1 class="px-6 pb-6 text-2xl">comments</h1>

            <div v-for="comment in beatmapset.comments">
                <div class="basic-border-t basic-border-b p-6">
            <div class="flex gap-3">
                <a class="w-12 h-12 flex-none">
                    <span class="inline-block w-full h-0 rounded bg-contain" :style="{ 'padding-bottom': '100%', 'background-image': 'url(' + comment.author.avatarURL + ')'}"></span>
                </a>
                <div class="flex flex-1 flex-col">
                    <div class="flex">
                        <a class="font-semibold text-xl hover:underline block -mt-2 -mb-1">
                            {{ comment.author.username }}
                        </a>
                        <div class="ml-auto -mt-1.5 text-white/40">
                            <span>{{ comment.createdAt.date }}</span>
                        </div>
                    </div>

                    <div style="font-family: Inter;">
                        {{ comment.content }}
                    </div>

                    <div class="flex items-baseline flex-wrap gap-2">
                        <div class="font-semibold">
                        </div>
                    </div>

<div class="flex gap-2" data-controller="vote" data-vote-comment-id-value="{{ comment.id }}">
    <button data-vote-target="likeBtn" data-action="click->vote#vote" data-vote-type="like" class="cursor-pointer flex items-center gap-1 }}">
        <i class="fas fa-thumbs-up"></i>
        <span data-vote-target="likeCount">{{ comment.likes }}</span>
    </button>
    <button data-vote-target="dislikeBtn" data-action="click->vote#vote" data-vote-type="dislike" class="cursor-pointer flex items-center gap-1}}">
        <i class="fas fa-thumbs-down"></i>
        <span data-vote-target="dislikeCount">{{ comment.dislikes }}</span>
    </button>
</div>

                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</template>