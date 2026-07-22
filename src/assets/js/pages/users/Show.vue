<script setup>
import AppLayout from '../layouts/AppLayout.vue';
import Markdown from '../components/Markdown.vue';
import BeatmapsetCard from '../components/BeatmapsetCard.vue';
import CountryFlag from '../components/CountryFlag.vue';
import MarkdownEditor from '../components/MarkdownEditor.vue';
import ActionButton from '../components/ActionButton.vue';
import DateTimeSpan from '../components/DateTimeSpan.vue';

import axios from 'axios';

import { useDateFormat, useTimeAgo } from '@vueuse/core';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({
    layout: [AppLayout, { title: 'user profile' }]
})

const props = defineProps({
    user: Object,
    myBeatmaps: Object,
    favoriteBeatmaps: Object
})

const page = usePage();
const aboutMeDraft = ref(props.user.aboutMe || '');
const isOwnProfile = computed(() => (page.props.auth?.user?.id === props.user.id) || page.props.auth?.user?.isAdmin);

const isEditing = ref(false);
const saving = ref(false);

function startEditing() {
    aboutMeDraft.value = props.user.aboutMe || '';
    isEditing.value = true;
}

function cancelEditing() {
    isEditing.value = false;
}

async function saveAboutMe() {
    saving.value = true;

    try {
        // Only send a post request when this changes lol
        if (props.user.aboutMe != aboutMeDraft.value)
        {
            const response = await axios.post(`/api/users/${props.user.id}/about`, { content: aboutMeDraft.value });
            // Update about me so it's immediately reflected
            props.user.aboutMe = response.data.aboutMe;
            isEditing.value = false;
        }
        else
        {
            isEditing.value = false;
        }
    } catch (e) {
        console.error('Post failed', e);
    } finally {
        saving.value = false;
    }
}

</script>

<template>

<div class="flex flex-col gap-4">
<div class="bg-zinc-800 rounded-2xl overflow-hidden shadow basic-border">
    <div
        class="h-64 bg-center bg-cover bg-[radial-gradient(circle_at_center,#1f2937,transparent_70%)]"
        style="background-image: url('https://pbs.twimg.com/profile_banners/1403615810610941954/1781162438/1500x500');">
    </div>

    <div class="flex flex-row gap-6 h-28 basic-border-t">

        <img class="rounded-2xl w-40 h-40 shadow-lg self-end mb-4 ml-4" :src="user.avatarUrl"/>

            <div class="x justify-between w-full items-center">

            <div class="my-auto text-shadow-xs flex flex-col gap-1">
                <div class="flex flex-row gap-3 items-center">
                    <span class="text-3xl leading-none align-baseline relative top-[-3px]">{{ user.username }}</span>

                    <div class="flex flex-row gap-1">
                    <div v-for="group in user.groups" >
                        <div class="rounded-full bg-zinc-900 p-0.5 px-4" :style="'color: #' + group.color + ';' ">
                            {{ group.displayName }}
                        </div>
                    </div>
                    </div>

                </div>
                <div class="flex flex-row gap-2">
                    <CountryFlag :flag-url="user.countryFlagUrl" class="top-[5px]"/>
                    <span class="text-lg">{{ user.countryName }}</span>
                </div>
            </div>

            <div class="flex gap-2 items-stretch h-full p-3">
                <div class="rounded-xl basic-border flex flex-col justify-center items-center min-w-28 bg-zinc-700/40">
                    <span class="text-white/80">Score</span>
                    <span class="font-semibold text-3xl">-</span>
                </div>
                <div class="rounded-xl basic-border flex flex-col justify-center items-center min-w-28 bg-zinc-700/40">
                    <span class="text-white/80">Mapping</span>
                    <span class="font-semibold text-3xl">{{ user.rank_mapping != null ? '#' + (user.rank_mapping) : '-' }}</span>
                </div>
                <div class="rounded-xl basic-border flex flex-col justify-center items-center min-w-28 bg-zinc-700/40">
                    <span class="text-white/80">Kudos</span>
                    <span class="font-semibold text-3xl">{{ user.rank_kudos != null ? '#' + (user.rank_kudos) : '-' }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="flex flex-row gap-4">

    <div class="flex flex-col">
        <div class="bg-zinc-800 rounded-2xl overflow-hidden shadow p-4 basic-border">
            <div class="p-2 flex flex-col gap-2 w-55">
                <div>
                    <div v-if="user.isOnline">
                        <span class="text-green-300 font-semibold">ONLINE</span>
                    </div>
                    <div v-else class="flex flex-col">
                        <span class="text-gray-300 font-semibold mr-2">OFFLINE</span>
                        <span v-if="user.lastSeenAt" class="text-white/60">Last seen
                            <DateTimeSpan :dateTime="user.lastSeenAt" />
                            </span>
                    </div>
                    <hr class="h-px text-white/40 mt-2">
                </div>
                <div class="x justify-between">
                    <span>
                    joined:
                    </span>
                    <DateTimeSpan :dateTime="user.createdAt" />
                </div>
                <div class="x justify-between">
                    <span>ranked score:</span>
                    0
                </div>
                <div class="x justify-between">
                    <span>mapping points:</span>
                    {{ user.mappingPoints }}
                </div>
                <div class="x justify-between">
                    <span>kudos:</span>
                    {{ user.kudos }}
                </div>
            </div>
        </div>
        <div class="flex grow"></div>
    </div>


    <div class="flex flex-col grow gap-4">

        <!-- about me! -->
        <div class="bg-zinc-800 rounded-2xl overflow-hidden shadow p-6 basic-border flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl">about me!</h1>
                <button
                    v-if="isOwnProfile && !isEditing"
                    @click="startEditing"
                    class="text-sm text-gray-400 hover:text-white cursor-pointer"
                >
                    <i class="fas fa-pen"></i> edit
                </button>
            </div>

            <div v-if="isEditing">
                <MarkdownEditor v-model="aboutMeDraft" placeholder="Write something about yourself... (Markdown supported)" />
                <div class="flex gap-2 mt-3">
                    
                    <form @submit.prevent="saveAboutMe">
                        <ActionButton
                                :label="saving ? 'Saving...' : 'Save'"
                                icon="fas fa-save"
                                type="submit"
                                :disabled="saving"
                            />
                    </form>
                    <form @submit.prevent="cancelEditing">
                        <ActionButton
                                label="Cancel"
                                icon="fas fa-times"
                                type="submit"
                                :disabled="saving"
                                class="bg-gray-500 hover:bg-gray-400"
                        />
                    </form>
                </div>
            </div>

            <div v-else>
                <div v-if="user.aboutMe">
                    <Markdown :source="user.aboutMe" />
                </div>
                <div v-else>
                    nothing here... :<
                </div>
            </div>
        </div>

        <div class="bg-zinc-800 rounded-2xl overflow-hidden shadow p-6 basic-border">
            <h1 class="text-2xl pb-4">maps</h1>

            <div>
                <h1 class="text-xl">my maps</h1>

                <div class="p-0 rounded-2xl mt-6">
                    <div class="grid grid-cols-1 gap-3">
                        <div v-for="beatmap in myBeatmaps">
                            <BeatmapsetCard
                                :beatmapset="beatmap"
                                />
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h1 class="text-xl">favorite maps</h1>

                <div class="p-0 rounded-2xl mt-6">
                    <div class="grid grid-cols-1 gap-3">
                        <div v-for="beatmap in favoriteBeatmaps">
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

</div>

</template>