<script setup>

import { ref, inject, computed } from 'vue';
import VideoModal from './VideoModal.vue';

const isHovered = ref(false);

const content = inject('content');
const getVideoThumbnail = inject('getVideoThumbnail')
const videos = computed (() => content.videos);

const selectedVideo = ref(null);

</script>

<template>
    <div v-if="!selectedVideo" class="container">
        <h1>THE PROOF IS IN THE PIXELS. A PORTFOLIO OF AI-POWERED VISIONS MADE REAL.</h1> 
        <div class="videoGrid">
            <div @click="selectedVideo = video" v-for="(video, index) in videos" :key="index">
                <video v-if="video.isLocal === 'true'" preload="metadata" :src="`/videos/${video.id}/stream`" class="w-full desktop:w-[704px] aspect-16/9 object-cover" :class="index % 2 === 1 ? 'shifted' : ''"></video>
                <img v-else :src="getVideoThumbnail(video.content_url)" class="w-full desktop:w-[704px] aspect-16/9 object-cover" :class="index % 2 === 1 ? 'shifted' : ''" alt="Video thumbnail">
            </div>
            <a href="/contact" :class="videos.length % 2 == 1 ? 'shifted' : ''">
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
    grid-template-columns: 1fr 1fr;
    grid-gap: 32px;
    justify-items: center;
}

.shifted {
    transform: translateY(214px);
}

.videoGrid > a.shifted {
    transform: translateY(calc(396px - 161.5px)); /* half of 396px (video height) minus half of 161.5px (button height) */
}

.videoGrid > a {
    transform: translateY(18.25px); /* quarter of 396px (video height) minus half of 161.5px (button height) */
}

.container > h1 {
    font-size: 2.4em;
    font-weight: 900;
    margin: 50px 0 50px;
    width: 60%;
    line-height: 1em;
}

.container > a {
    display: none;
}

@media (max-width: 1440px) {
    
.videoGrid {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.container {
    padding-bottom: 30px;
}

.container > h1 {
    width: 100%;
	font-size: 20px;
	font-variation-settings: "wdth" 125;
	font-weight: var(--font-weight-bold);
	letter-spacing: 0;
	line-height: 1;
	text-transform: uppercase;
}


.shifted {
    transform: translateY(0px);
}

.videoGrid > a {
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