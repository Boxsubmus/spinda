<script setup>
import { computed } from 'vue'
import MarkdownIt from 'markdown-it'
import DOMPurify from 'dompurify'

const props = defineProps({
  source: {
    type: String,
    default: '',
  },
})

const md = new MarkdownIt({
  html: false,       // disallow raw HTML in the markdown source
  linkify: true,      // auto-convert URLs to links
  breaks: true,        // convert \n to <br>
})

const renderedHtml = computed(() => {
  const rawHtml = md.render(props.source ?? '')
  return DOMPurify.sanitize(rawHtml)
})
</script>

<template>
  <div class="markdown-render" v-html="renderedHtml" />
</template>