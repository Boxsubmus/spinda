<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '../layouts/AppLayout.vue';
import Paginate from '../components/Paginate.vue';
import CountryFlag from '../components/CountryFlag.vue';
import { routes } from '../../routes.js';
import DateTimeSpan from '../components/DateTimeSpan.vue';

defineOptions({
    layout: [AppLayout, { title: 'users' }]
});

const props = defineProps({
    users: { type: Array, required: true },
    pagination: { type: Object, required: true },
});

</script>

<template>
    <div class="flex flex-col gap-4">

        <div class="flex flex-col gap-2">

        <div v-for="user in users" :key="user.id">

            <div class="flex flex-col rounded-xl overflow-hidden  hover:bg-zinc-800 basic-outline">
                <div class="flex flex-row gap-4 p-4">
                    <img :src="user.avatarUrl" class="w-14 rounded" />
                    <div class="flex flex-col justify-between">
                        <div class="flex flex-row items-center gap-3">
                            <Link :href="routes.userShow(user.id)" class="text-xl">{{ user.username }}</Link>
                        </div>
                        <CountryFlag :flag-url="user.countryFlagUrl"/>
                    </div>
                    <div class="w-full">
                        
                    </div>
                    <div class="flex flex-row gap-4">
                        <div class="flex flex-col text-lg w-90 text-right justify-center">
                            <span v-if="!user.isOnline">Last seen <DateTimeSpan :dateTime="user.lastSeenAt ?? user.createdAt" /></span>
                            <span v-if="user.isOnline">Online!</span>
                            <span v-else>Offline</span>
                        </div>
                        <div class="flex items-center">
                            <span v-if="user.isOnline" class="bg-green-300 rounded-full w-8 h-8"></span>
                            <span v-else class="rounded-full w-8 h-8 border-3 border-gray-400"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        </div>

        <Paginate
            :pagination="pagination"
            base-url="/users"    
        />
    </div>
</template>