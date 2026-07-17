<script setup>
const props = defineProps({
  beatmapset: { type: Object, required: true },
  class: { type:String }
})

let color = "bg-gray-500";
let label = "ARCHIVE";
if (props.beatmapset.featured)
{
    color = "bg-purple-400";
    label = "FEATURED";
}

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