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
        
    <div v-if="modelValue" class="fixed inset-0 bg-[#262626] z-1000 flex items-center justify-center"  @click.stop>
        <button @click="$emit('update:modelValue', null)" class="exit">✕</button>
        <div :class="modelValue.isVideo === 'true' ? 'flex flex-col-reverse' : 'short-container'" >
            <div class="flex flex-col items-center desktop:items-start">
                <h1>{{ modelValue.title }}</h1>
                <h2>{{ modelValue.description }}</h2>
            </div>
            <video v-if="modelValue.isLocal === 'true'" :src="videoStreamUrl" controls autoplay class="object-contain" :class="modelValue.isVideo === 'true' ? 'aspect-16/9 w-screen desktop:w-[900px]' : 'aspect-9/16 w-screen desktop:w-[400px]'"></video>
            <iframe v-else :src="embedUrl(modelValue.content_url, YtParams, VmParams)" frameborder="0" allow="fullscreen" allowfullscreen class="object-contain" :class="modelValue.isVideo === 'true' ? 'aspect-16/9 w-screen desktop:w-[900px]' : 'aspect-9/16 w-screen desktop:w-[400px]'"></iframe>
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

.short-container {
    display: grid;
    padding: 50px;
    grid-template-columns: repeat(3, 1fr);
    gap: 50px;
    align-items: end;
}

h1 {
    padding: 40px 0px 20px;
    color: white;
    font-size: 34px;
    text-transform: uppercase;
	font-variation-settings: "wdth" 125;
	font-weight: var(--font-weight-bold);
	letter-spacing: 0;
	line-height: 1;
	text-transform: uppercase;
}
h2 {
    color: white;
    white-space: pre-line;
    line-height: 1.5;
    font-size: 15px;
    max-width: 480px;
}
@media (max-width: 1440px) {

.exit {
    top: 15px;
    right: 15px;
}

.short-container {
    gap: 0px;
    display: flex;
    flex-direction: column-reverse;
    padding: 62px 14px 32px;
    align-items: center;
}

h1 {
    font-size: 20px;
    text-align: center;
}

h2 {
    font-size: 13px;
    max-width: 270px;
    text-align: center;
}

}
</style>