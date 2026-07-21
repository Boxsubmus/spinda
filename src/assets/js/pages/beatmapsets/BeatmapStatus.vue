<script setup>
const props = defineProps({
  beatmapset: { type: Object, required: true },
  class: { type:String }
})

let color = "bg-zinc-700/30";
let label = "UNRANKED";

</script>

<template>
    <span class=" py-2 px-10 text-shadow-sm rounded-3xl font-bold basic-border
        inline-flex items-center justify-center"
        :class="color">
        <span class="">
            {{ label }}
        </span>
    </span>
</template>