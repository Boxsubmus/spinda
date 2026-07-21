<script setup>
import { ref, computed } from 'vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import Markdown from './Markdown.vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: String,
});

const emit = defineEmits(['update:modelValue']);

const showPreview = ref(false);

const content = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const renderedHtml = computed(() => {
    return DOMPurify.sanitize(marked.parse(content.value || ''));
});
</script>

<template>
    <div class="border border-zinc-700 rounded-xl overflow-hidden">
        <div class="flex border-b border-zinc-700 bg-zinc-800">
            <button
                type="button"
                class="px-4 py-2 text-sm font-medium cursor-pointer hover:bg-zinc-850"
                :class="!showPreview ? 'bg-zinc-700 text-white' : 'text-zinc-400'"
                @click="showPreview = false"
            >
                Write
            </button>
            <button
                type="button"
                class="px-4 py-2 text-sm font-medium cursor-pointer hover:bg-zinc-850"
                :class="showPreview ? 'bg-zinc-700 text-white' : 'text-zinc-400'"
                @click="showPreview = true"
            >
                Preview
            </button>
        </div>

        <textarea
            v-if="!showPreview"
            v-model="content"
            rows="10"
            maxlength="2048"
            class="w-full bg-zinc-900 text-white p-3 focus:outline-none resize-y"
            :placeholder="props.placeholder"
            style="font-family: Inter;"
        ></textarea>

        <div v-else class="p-4 py-0">
            <Markdown :source="content"/>
        </div>

        <div class="text-right text-xs text-zinc-500 px-3 pb-2">
            {{ content.length }} / 2048
        </div>
    </div>
</template>