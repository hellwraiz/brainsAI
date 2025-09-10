<script setup>

import { ref, inject, computed, onMounted, onUnmounted } from 'vue';
import VideoModal from './VideoModal.vue';

const isHovered = ref(false);

const content = inject('content');
const videos = computed (() => content.videos);

const selectedVideo = ref(null);

</script>

<template>
    <div class="container">
        <h1>THE PROOF IS IN THE PIXELS. A PORTFOLIO OF AI-POWERED VISIONS MADE REAL.</h1> 
        <div class="videoGrid">
            <div @click="selectedVideo = video" v-for="(video, index) in videos" :key="index">
                <video :src="video.content_url" width="704px" :class="index % 2 === 1 ? 'shifted' : ''" style="aspect-ratio: 16/9; object-fit: cover;"></video>
            </div>
            <button :class="videos.length % 2 == 1 ? 'shifted' : ''">
            <img :src="isHovered ? '/images/contactUsHover.png' : '/images/contactUs.png'" alt="watch button" @mouseenter="isHovered = true" @mouseleave="isHovered = false" />
            </button>
        </div>
    </div>

    <VideoModal v-model="selectedVideo"/>
    
</template>



<style scoped>
.videoGrid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: 32px;
    justify-items: center;
}

.shifted {
    transform: translateY(214px);
}

button.shifted {
    transform: translateY(117.25px); /* half of 396px (video height) minus half of 161.5px (button height) */
}

button {
    transform: translateY(18.25px); /* quarter of 396px (video height) minus half of 161.5px (button height) */
}

.container > h1 {
    font-size: 2.4em;
    font-weight: 900;
    margin: 50px 0 50px;
    width: 60%;
    line-height: 1em;
}

</style>