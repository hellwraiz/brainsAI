<script setup>

import { ref, inject, computed } from 'vue';
import VideoModal from './VideoModal.vue';

const isHovered = ref(false);

const content = inject('content');
const getVideoThumbnail = inject('getVideoThumbnail')
const videos = computed (() => content.shorts);
const length = computed(() => content.shorts.length);
const video = computed(() => length ? content.shorts[index.value] : null);
const index = ref(0);

const updateIndexMobile = ((dir) => {
  if (dir === 'r') {
    index.value = (index.value + 1) % length.value;
  } else if (dir === 'l') {
    index.value = (index.value - 1 + length.value) % length.value;
  }
})

const selectedVideo = ref(null);

const getColumnClass = (index) => {
    const column = index % 3;
    if (column === 0) return 'shift-down-120';
    if (column === 1) return 'shift-down-60';
    return ''; // column 2, no shift
};

</script>

<template>
    <div class="container">
        <h1>THE PROOF IS IN THE PIXELS. A PORTFOLIO OF AI-POWERED VISIONS MADE REAL.</h1> 
        <div class="videoGrid">
            <div @click="selectedVideo = video" v-for="(video, index) in videos" :key="index">
                <video v-if="video.isLocal === 'true'" preload="metadata" :src="`/videos/${video.id}/stream`" width="460px" :class="getColumnClass(index)" style="aspect-ratio: 9/16; object-fit: cover;"></video>
                <img v-else :src="getVideoThumbnail(video.content_url)" width="460px" :class="getColumnClass(index)" style="aspect-ratio: 9/16; object-fit: cover;" alt="Video thumbnail">
            </div>
        </div>
        <div class="video-display" >
            <video @click="selectedVideo = video" v-if="video.isLocal === 'true'" preload="metadata" :src="`/videos/${video.id}/stream`" class="video-element"></video>
            <img @click="selectedVideo = video" v-else :src="getVideoThumbnail(video.content_url)" class="video-element"alt="Video thumbnail">
            <div class="listIndicators">
                <button class="index-btn" @click="updateIndexMobile('l')"><img src="/public/images/arrowL.png" alt="left"></button>
                <div class="flex gap-[10px]">
                <img v-for="(_, itemIndex) in content.videos" :key="itemIndex" :src="itemIndex === index ? '/images/listItemActiveB.png' : '/images/listItemB.png'"/>
                </div>
                <button class="index-btn" @click="updateIndexMobile('r')"><img src="/public/images/arrowR.png" alt="right"></button>
            </div>
        </div>
        <div style="display: flex; justify-content: center;">
            <a href="/contact" style="width: 654px; padding-top: 78px;" :class="videos.length % 2 == 1 ? 'shifted' : ''">
                <img :src="isHovered ? '/images/contactUsHover.png' : '/images/contactUs.png'" alt="watch button" @mouseenter="isHovered = true" @mouseleave="isHovered = false" />
            </a>
        </div>
        <a href="/contact"><p>CONTACT US</p><img src="/public/images/arrowRW.png" style="width: 30px;" alt=""></a>
    </div>

    <VideoModal v-model="selectedVideo"/>
    
</template>


<style scoped>
    
.videoGrid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-gap: 30px;
    justify-items: center;
    padding-bottom: 120px;
}

.video-display {
    display: none;
}

.listIndicators {
  display: flex;
  flex-direction: row;
  flex-shrink: 0;
  align-items: center;
  justify-content: space-between;
  align-self: stretch;
}

.index-btn img {
  width: 24px;
  height: 24px;
}
.index-btn:hover {
  opacity: 50%;
}
.video-element {
    object-fit: cover;
    height: 573px;
    width: 100%;
    overflow: hidden;
    padding-bottom: 40px;
}

.shift-down-120 {
  transform: translateY(120px);
}

.shift-down-60 {
  transform: translateY(60px);
}

.container > h1 {
    font-weight: 900;
    margin: 50px 0 50px;
    width: 60%;
	font-size: 34px;
	font-variation-settings: "wdth" 125;
	font-weight: var(--font-weight-bold);
	letter-spacing: 0;
	line-height: 1;
	text-transform: uppercase;
}

.container > a {
    display: none;
}

@media (max-width: 1440px) {
    .container > h1 {
        width: unset;
        font-size: 20px;
    }

    .videoGrid {
        display: none;
    }

    .video-display {
        display: flex;
        flex-direction: column;
    }


    .listIndicators > div > img {
        width: 16px;
        height: 16px;
    }

    .container > div > a {
        display: none;
    }

    .container > a {
        margin-top: 30px;
        align-items: center;
        background: black;
        border-radius: 32px;
        color: white;
        display: flex;
        font-size: 20px;
        font-variation-settings: "wdth" 125;
        font-weight: var(--font-weight-bold);
        justify-content: space-between;
        padding: 18px 26px 18px 30px;
        transition: all 0.3s ease;
        width: 100%;
    }

    a:hover {
        background: rgba(0, 0, 0, 0.8);
    }
}

</style>