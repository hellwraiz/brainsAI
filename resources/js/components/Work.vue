<script setup>

import { ref, inject, computed, onMounted, nextTick } from 'vue';
import VideoModal from './VideoModal.vue';


const content = inject('content');
const videos = computed (() => content.videos);

const selectedVideo = ref(null);

const isEntering = ref(true);
onMounted( async () => {

    // To make sure that everything is loaded before starting the animation.
    await nextTick();

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            isEntering.value = false;
        });
    });
})

</script>

<template>
    <div v-if="!selectedVideo" class="container page-transition" :class="{ 'page-enter-from': isEntering }" >
        <h1>THE PROOF IS IN THE PIXELS. A PORTFOLIO OF AI-POWERED VISIONS MADE REAL.</h1> 
        <div class="videoGrid">
            <div class="relative" @click="selectedVideo = video" :class="index % 2 === 1 ? 'shifted' : ''" v-for="(video, index) in videos" :key="index">
                <img :src="video.img_url" class="w-full desktop:w-[704px] aspect-16/9 object-cover"></img>
                <div class="work-overlay">
                    <span class="work-label">{{ video.title }}</span>
                </div>
            </div>
            <a href="/contact" :class="videos.length % 2 == 1 ? 'shifted' : ''">
                <img src="/public/images/contactUs.png" class="hover:opacity-80" alt="contact button"/>
            </a>
        </div>
        <a href="/contact"><p>CONTACT US</p><img src="/public/images/arrowRW.png" style="width: 30px;" alt="contact button"></a>
    </div>

    <VideoModal v-model="selectedVideo"/>
    
</template>



<style scoped>
/* Pointer cursor on clickable elements */
span {
  cursor: url('/public/images/icons/cursors/neutral.svg') 25 36.25, pointer;
}

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

.videoGrid div {
    overflow: hidden;
}
.videoGrid img {
    transition: all 0.3s ease
}
.videoGrid > div:hover span {
    text-decoration: underline;
}
.videoGrid > div:hover > img {
    transform: scale(1.25);
}
.videoGrid > div:hover span {
    text-decoration: underline;
}
.videoGrid > div > img.shifted {
    transform: translateY(214px);
}
.work-overlay {
	background: linear-gradient(transparent,rgba(0,0,0,.7));
	bottom: 0;
	left: 0;
	padding: 40px;
	position: absolute;
	right: 0;
}
.work-label {
    max-width: 347px;
	color: white;
	display: block;
	font-size: 20px;
	font-variation-settings: "wdth" 125;
	font-weight: 700;
	line-height: 1;
	text-transform: uppercase;
	transition: all 0.3s ease;
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

.work-overlay {
    padding: 20px;
}
.work-label {
    max-width: 230px;
    font-size: 14px;
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