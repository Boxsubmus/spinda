<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '../layouts/AppLayout.vue';
import Paginate from '../components/Paginate.vue';
import CountryFlag from '../components/CountryFlag.vue';
import { routes } from '../../routes.js';

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
                        <Link :href="routes.userShow(user.id)" class="text-xl">{{ user.username }}</Link>
                        <CountryFlag :flag-url="user.countryFlagUrl"/>
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