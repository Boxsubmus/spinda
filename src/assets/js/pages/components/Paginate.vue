<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    pagination: { type: Object, required: true },
    baseUrl: { type: String, required: true },
});

function goToPage(page) {
    if (page < 1 || page > props.pagination.lastPage)
        return;
    router.get(props.baseUrl, { page }, { preserveScroll: true, preserveState: true });
}
</script>

<template>
  <div class="flex gap-2 items-center justify-center mt-4">
    <button :disabled="pagination.currentPage === 1" @click="goToPage(pagination.currentPage - 1)" class="disabled:opacity-40">
      Prev
    </button>
    <span>Page {{ pagination.currentPage }} of {{ pagination.lastPage }}</span>
    <button :disabled="pagination.currentPage === pagination.lastPage" @click="goToPage(pagination.currentPage + 1)" class="disabled:opacity-40">
      Next
    </button>
  </div>
</template>