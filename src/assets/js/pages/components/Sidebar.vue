<script setup>

import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { routes } from '../../routes';
import { Link } from '@inertiajs/vue3';

const page = usePage()
const user = computed(() => page.props.auth.user)

const data = {
        navMain: [
            {
                title: "home",
                url: "/",
                items: [
                    {
                        title: "download",
                        url: "/home/download"
                    }
                ]
            },
            {
                title: "maps",
                url: "/maps",
                items: [
                    {
                        title: "featured",
                        url: "/maps/featured",
                    },
                    {
                        title: "map listing",
                        url: "/maps",
                    }
                ]
            },
            {
                title: "rankings",
                url: "",
                items: [
                    {
                        title: "mapping",
                        url: "/rankings/mapping"
                    },
                    {
                        title: "kudos",
                        url: "/rankings/kudos"
                    }
                ]
            },
            {
                title: "community",
                items: [
                    {
                        title: "user listing",
                        url: "/users"
                    }
                ]
            },
            {
                title: "help",
                items: [
                    {
                        title: "rules",
                        url: "/rules"
                    },
                    {
                        title: "report bug",
                        url: "https://github.com/Boxsubmus/hyperspin-issues"
                    },
                    {
                        title: "editor tutorials",
                        url: "/tutorials"
                    }
                ]
            }
        ]
    };

</script>

<template>
    <div class="w-64 m-0
            flex flex-col
            text-white">

    <div class="m-6 flex flex-col grow">

        <div class="flex flex-col gap-4">

            <Link :href="routes.home()" class="hover:bg-zinc-800 flex flex-col rounded-lg p-3 py-2">
                <span class="text-3xl">Hyperspin!</span>
                <span>spin to win!</span>
            </Link>

            <div class="flex grow flex-col gap-8">
                <div v-for="group in data.navMain">
                    <div class="flex flex-col gap-0">
                        <div class="p-3 py-1">
                            <span class="text-gray-400">{{ group.title }}</span>
                        </div>
                        <div v-for="item in group.items" class="flex flex-col gap-0">
                            <Link :href="item.url" class="p-3 py-2 hover:bg-zinc-800 rounded-lg">
                                {{ item.title }}
                            </Link>
                        </div>

                    </div>
                </div>

            </div>


        </div>

        <div class="flex grow">
        </div>

        <div>
            <div v-if="user">
                <Link :href="routes.userShow(user.id)" class="flex flex-row items-center gap-3
                    rounded-lg p-2
                    hover:bg-zinc-800
                    cursor-pointer">
                
                    <img :src="user.avatarURL" :alt="user.username" class="w-10 rounded">
                    <div>{{ user.username }}</div>
                </Link>
            </div>
            <div v-else>
                <a href="/auth/steam">Log in with Steam</a>
            </div>
        </div>
    </div>
</div>
</template>