<script setup>

import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { routes } from '../../routes';
import { Link } from '@inertiajs/vue3';

const page = usePage()
const user = computed(() => page.props.auth.user)

</script>

<template>
    <div class="w-64 m-0
            flex flex-col
            text-white">

    <div class="m-6 flex flex-col grow">

        <div>
            <div class="text-3xl">Hyperspin</div>
            <p>spin to win!</p>
        </div>

        <div class="flex grow">
        </div>

        <div>
            <div v-if="user">
                <Link :href="routes.userShow(user.id)" class="flex flex-row items-center gap-3
                    rounded-lg p-2
                    hover:bg-zinc-800
                    cursor-pointer">
                
                    <img :src="user.avatarURL" :alt="user.username" class="w-12 rounded">
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