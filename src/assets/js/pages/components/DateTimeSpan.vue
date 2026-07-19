<script setup>
import { useDateFormat, useTimeAgo } from '@vueuse/core';

const props = defineProps({
    dateTime: { type: Object, required: true },
});
</script>

<template>
    <span :title="useDateFormat(dateTime.date + dateTime.timezone, 'MMM-DD-YYYY HH:mm:ss (dddd)').value">
        {{ useTimeAgo(dateTime.date + dateTime.timezone) }}
    </span>
</template>