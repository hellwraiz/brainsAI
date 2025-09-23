<script setup>

import { ref, inject, computed, onMounted, nextTick } from 'vue';
import VideoModal from './VideoModal.vue';

const isHovered = ref(false);

const content = inject('content');
const videos = computed (() => content.shorts);
const length = computed(() => content.shorts.length);
const video = computed(() => length ? content.shorts[index.value] : null);
const index = ref(0);

const showNext = ref(null)
const isAnimating = ref(false)
const currentFrame = ref(null)
const updateIndexMobile = async (dir) => {

    if (isAnimating.value) return
    
    currentFrame.value = video.value.img_url;

    if (dir === 'r') {
        showNext.value = true;
        index.value = (index.value + 1) % length.value;
    } else if (dir === 'l') {
        showNext.value = false;
        index.value = (index.value - 1 + length.value) % length.value;
    }

    setTimeout(() => {
        isAnimating.value = true;
    }, 5)

    setTimeout(() => {
        showNext.value = null
        isAnimating.value = false
        currentFrame.value = false
    }, 1000)
}

const selectedVideo = ref(null);

const getColumnClass = (index) => {
    const column = index % 3;
    if (column === 0) return 'shift-down-120';
    if (column === 1) return 'shift-down-60';
    return ''; // column 2, no shift
};

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
    <div class="container page-transition" :class="{ 'page-enter-from': isEntering }" >
        <h1>{{ content.text['shorts title']}}</h1>
        <p>{{ content.text['shorts desc'] }}</p> 
        <div v-if="length > 0" class="videoGrid">
            <div class="relative" @click="selectedVideo = video" :class="getColumnClass(index)" v-for="(video, index) in videos" :key="index">
                <img :src="video.img_url" width="460px" style="aspect-ratio: 9/16; object-fit: cover;"/>
                <div class="work-overlay">
                    <span class="work-label">{{ video.title }}</span>
                </div>
            </div>
        </div>
        <div v-if="length > 0" class="video-display" >
            <div class="video-container">
                <img v-if="showNext" :src="video.img_url" :class="isAnimating && showNext ? 'go-left' : 'stay-right'" class="video-animation z-10"/>
                <img v-if="currentFrame" :src="currentFrame" :class="showNext ? (isAnimating ? 'scale' : '') : (isAnimating ? 'stay-right' : '')" class="video-animation z-9">
                <img @click="selectedVideo = video" :src="video.img_url" :class="!isAnimating && showNext === false ? 'scale-main' : (isAnimating && showNext === false ? 'scale-down' : '')" class="video-element"/>
                <div class="work-overlay">
                    <span class="work-label">{{ video.title }}</span>
                </div>
            </div>
            <div class="listIndicators">
                <button class="index-btn" @click="updateIndexMobile('l')"><img src="/public/images/arrowL.png" alt="left"></button>
                <div class="flex gap-[10px]">
                    <img v-for="(_, itemIndex) in content.shorts" :key="itemIndex" :src="itemIndex === index ? '/images/listItemActiveB.png' : '/images/listItemB.png'"/>
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
/* Pointer cursor on clickable elements */
span {
  cursor: url('/public/images/icons/cursors/neutral.svg') 25 36.25, pointer;
}

.videoGrid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-gap: 30px;
    justify-items: center;
    padding-bottom: 120px;
}
.videoGrid div {
    overflow: hidden;
}
.videoGrid img {
    transition: all 0.3s ease
}
.videoGrid > div:hover img {
    transform: scale(1.25);
}
.video-container:hover img {
    transform: scale(1.25);
}
.video-container:hover span {
    text-decoration: underline;
}
.videoGrid > div:hover span {
    text-decoration: underline;
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
    max-width: 300px;
	color: white;
	display: block;
	font-size: 20px;
    line-height: 1.4;
	font-variation-settings: "wdth" 125;
	font-weight: 700;
	line-height: 1;
	text-transform: uppercase;
	transition: all 0.3s ease;
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
.video-container {
    position: relative;
    overflow: hidden;
    width: 100%;
    aspect-ratio: 9/16;
    max-height: 573px;
    margin-bottom: 40px
}
.video-animation,
.video-element {
    position: absolute;
    inset: 0;
    margin: auto;
    object-fit: cover;
    aspect-ratio: 9/16;
    width: 100%;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.video-animation {
    transition: 1000ms 
}
.stay-right {
    transform: translateX(100%);
}
.scale {
    transition: all 1000ms cubic-bezier(0.4, 0, 0.2, 1);
    transform: scale(1.25);
}
.scale-main {
    transition: all 0ms;
    transform: scale(1.25);
}
.scale-down {
    transition: all 1000ms cubic-bezier(0.4, 0, 0.2, 1);
    transform: scale(1);
}
.go-left {
    transform: translateX(0)
}
.shift-down-120 {
  transform: translateY(120px);
}

.shift-down-60 {
  transform: translateY(60px);
}

.container > h1 {
    font-weight: 900;
    margin: 36px 0 20px;
    width: 65%;
	font-size: 34px;
	font-variation-settings: "wdth" 125;
	font-weight: var(--font-weight-bold);
	letter-spacing: 0;
	line-height: 1;
	text-transform: uppercase;
}
.container > p {
    margin-bottom: 70px;
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

    .work-overlay {
        padding: 20px;
    }
    .work-label {
        max-width: 218px;
        font-size: 14px;
        line-height: 1;
        font-size: 14px;
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