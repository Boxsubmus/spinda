<script setup>
const props = defineProps({
    flagUrl: { type: String, required: true },
});
</script>

<template>
    <span class="flag-country" :style="{ 'background-image': 'url(' + flagUrl + ')' }">
    </span>
</template>