<script setup>

import { ref, inject, computed, onMounted, onUnmounted } from 'vue';

const isHovered = ref(false);

const content = inject('content');
//const videos = computed(() => content.videos);
const videos = computed (() => content.shorts);

const getColumnClass = (index) => {
  const column = index % 3;
  if (column === 0) return 'shift-down-120';
  if (column === 1) return 'shift-down-60';
  return ''; // column 2, no shift
};

</script>

<style scoped>

.container {
    justify-items: center;
    margin: auto;
    padding: 0px 50px 50px;
    max-width: 1540px;
}
    
.videoGrid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-gap: 30px;
    justify-items: center;
    padding-bottom: 120px;
}

.shift-down-120 {
  transform: translateY(120px);
}

.shift-down-60 {
  transform: translateY(60px);
}

.container > h1 {
    font-size: 2.4em;
    font-weight: 900;
    margin: 50px 0 50px;
    width: 60%;
    line-height: 1em;
}

</style>

<template>
    <div class="container">
        <h1>THE PROOF IS IN THE PIXELS. A PORTFOLIO OF AI-POWERED VISIONS MADE REAL.</h1> 
        <div class="videoGrid">
            <div v-for="(video, index) in videos" :key="index">
                <video 
                    :src="video.content_url"
                    width="460px"
                    :class="getColumnClass(index)"
                    style="aspect-ratio: 9/16; object-fit: cover;"
                ></video>
            </div>
        </div>
        <div style="display: flex; justify-content: center;">
            <button style="width: 654px; padding-top: 78px;" :class="videos.length % 2 == 1 ? 'shifted' : ''">
                <img :src="isHovered ? '/images/contactUsHover.png' : '/images/contactUs.png'" alt="watch button" @mouseenter="isHovered = true" @mouseleave="isHovered = false" />
            </button>
        </div>
    </div>
    
</template>