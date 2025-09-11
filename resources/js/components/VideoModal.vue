<script setup>
import { defineEmits, defineProps, computed, inject } from 'vue';

const { modelValue } = defineProps({
  modelValue: Object,
})
defineEmits(['update:modelValue'])

const videoStreamUrl = computed(() => `/videos/${modelValue.id}/stream`)

const YtParams = {
  controls: 1,        // Show all controls
  showinfo: 0,        // Hide video info
  rel: 0,             // Don't show related videos
  modestbranding: 0,  // Remove YouTube branding
  iv_load_policy: 0,  // Hide annotations (3 = hide all annotations)
  disablekb: 0,       // Disable keyboard controls (1 = disabled)
  fs: 1,              // Disable fullscreen button
  autoplay: 0,        // Don't autoplay
  mute: 0,            // Mute by default
  loop: 0,            // Don't loop
};

const VmParams = {
  controls: 1,        // Show controls
  title: 1,           // Hide title
  byline: 1,          // Hide author name
  portrait: 1,        // Hide author portrait
  autoplay: 0,        // Don't autoplay
  muted: 0,           // Mute by default (Vimeo uses 'muted' not 'mute')
  loop: 0,            // Don't loop
  background: 0,      // Don't enable background mode
  pip: 1,             // Disable picture-in-picture
  speed: 1,           // Hide playback speed controls
  keyboard: 1,        // Disable keyboard shortcuts
};

const embedUrl = inject('embedUrl')

</script>

<template>
        
    <div v-if="modelValue" class="fixed inset-0 bg-[#262626] flex items-center justify-center z-50" @click.stop>
        <button @click="$emit('update:modelValue', null)" class="exit">✕</button>
        <div class="flex flex-col" :class="modelValue.isVideo ? 'max-w-[900px] w-[900px]' : 'max-w-[400px]'">
            <video v-if="modelValue.isLocal === 'true'" :src="videoStreamUrl" controls autoplay class="object-contain" :class="modelValue.isVideo ? 'aspect-16/9' : 'aspect-9/16'"></video>
            <iframe v-else :src="embedUrl(modelValue.content_url, YtParams, VmParams)" frameborder="0" allow="fullscreen" allowfullscreen class="object-contain" :class="modelValue.isVideo ? 'aspect-16/9' : 'aspect-9/16'"></iframe>
            <h1>{{ modelValue.title }}</h1>
            <h2>{{ modelValue.description }}</h2>
        </div>
    </div>
</template>

<style scoped>
.exit{
    font-size: 32px;
    color: white;
    position: fixed;
    top: 50px;
    right: 50px;
    z-index: 51;
}
.exit:hover {
    opacity: 0.5;
}
h1 {
    padding: 40px 0px 20px;
    color: white;
    font-size: 3em;
    font-weight: 600;
    text-transform: uppercase;
    line-height: 1em;
}
h2 {
    color: white;
    white-space: pre-line;
    line-height: 1.2em;
}
</style>