<script setup>
import ActionButton from './components/ActionButton.vue';

</script>

<template>

<div class="w-full h-full">
    <div class="bg-zinc-800 overflow-hidden shadow w-300 m-auto p-8">
        <div class="flex flex-col gap-8">

            <div class="flex flex-col">
                <span class="text-3xl">Sign in</span>
                <span class="text-white/60">(This will not work if you don't have an account registered in the game)</span>
            </div>

            <div class="w-60">
                <ActionButton
                label="Sign in with Steam"
                icon="fab fa-steam"
                url="/auth/steam"
                />
            </div>

        </div>

    </div>
</div>


</template>