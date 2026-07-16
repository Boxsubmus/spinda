<script setup>
import Sidebar from '../components/Sidebar.vue';

defineProps({
    title: String
})
</script>

<template>
  <div class="flex h-screen">

    <Sidebar/>

    <div class="flex flex-col grow min-h-0">

      <div class="flex flex-col my-4 mr-4 grow border-lg rounded-2xl basic-border min-h-0 overflow-hidden">
        
                        <div class="flex p-4 bg-zinc-800 basic-border-b shrink-0">
                    <h1 class="text-lg">{{ title }}</h1>
                </div>

                                <div class="bg-zinc-850 grow min-h-0 overflow-y-auto">
                    <div class="w-6xl m-auto my-4 px-4">
                        <slot />
                    </div>
                </div>

      </div>

    </div>


  </div>
</template>