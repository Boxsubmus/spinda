<script setup>
import { useDateFormat, useTimeAgo } from '@vueuse/core';
import Tooltip from './Tooltip.vue';

const props = defineProps({
    dateTime: { type: Object, required: true },
});
</script>

<template>
    <Tooltip :text="useDateFormat(dateTime.date + dateTime.timezone, 'MMM-DD-YYYY HH:mm:ss (dddd)').value">
    <span class="cursor-default">
        {{ useTimeAgo(dateTime.date + dateTime.timezone) }}
    </span>
    </Tooltip>

</template>