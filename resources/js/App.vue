<script setup>
import { ref, onMounted, onUnmounted, computed, provide, reactive, watch } from 'vue';
import { useRoute } from 'vue-router'
const route = useRoute();
const isHome = computed(() => route.path === '/');
const content = reactive({
    videos: [],
    shorts: [],
    images: []
});
const ACTIVATETHEBURGER = ref(false)

async function fetchVideos() {
    try {
        let res = await axios.get(`/videos`)
        content.videos = res.data;
        res = await axios.get(`/reels`)
        content.shorts = res.data;
        res = await axios.get(`/scrollImages`)
        content.images = res.data;
    } catch (e) {
        console.error(`Failed to load videos`, e)
    }
}

onMounted(async () => {
    fetchVideos()
});

provide('content', content);

// Functions for handling video thumbnails
const getVideoThumbnail = (url) => {
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
        let id;
        try {
            if (url.includes('v=')) {
                id = new URL(url).searchParams.get('v');
            } else if (url.includes('youtu.be')) {
                const urlObj = new URL(url);
                id = urlObj.pathname.substring(1);
                if (id.includes('/')) id = id.split('/')[0];
                if (id.includes('?')) id = id.split('?')[0];
            }
    
            if (id) {
                // hqdefault.jpg (480x360) - high quality, always available
                return `https://img.youtube.com/vi/${id}/hqdefault.jpg`;
            }
        } catch (error) {
    console.error('Error extracting YouTube thumbnail:', error);
        }
    }

    if (url.includes('vimeo.com')) {
        try {
            const urlObj = new URL(url);
            const id = urlObj.pathname.split('/').filter(segment => segment).pop();
    
            // For Vimeo, you'd need to make an API call to get the thumbnail
            // This is a simplified approach - you might want to implement proper Vimeo API calls
            return `https://vumbnail.com/${id}.jpg`;
        } catch (error) {
            console.error('Error extracting Vimeo thumbnail:', error);
        }
    }
};

provide('getVideoThumbnail', getVideoThumbnail);

const embedUrl = (url, YtParams = {}, VmParams = {}) => {

    if (!url) return '';

    // YouTube
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
        let id;
        try {
            if (url.includes('v=')) {
                // Handle youtube.com/watch?v=VIDEO_ID format
                id = new URL(url).searchParams.get('v');
            } else if (url.includes('youtu.be')) {
                // Handle youtu.be/VIDEO_ID format
                const urlObj = new URL(url);
                id = urlObj.pathname.substring(1); // Remove leading slash
                // Remove any additional path segments or query params
                if (id.includes('/')) {
                    id = id.split('/')[0];
                }
                if (id.includes('?')) {
                    id = id.split('?')[0];
                }
            }
        
            if (id) {
                let base = `https://www.youtube.com/embed/${id}`;
                const finalUrl = withParams(base, YtParams);
                return finalUrl;
            }
        } catch (error) {
            console.error('Error parsing YouTube URL:', error);
        }
    }

    // Vimeo
    if (url.includes('vimeo.com')) {
        try {
            const urlObj = new URL(url);
            const id = urlObj.pathname.split('/').filter(segment => segment).pop();
    
                if (id) {
                    let base = `https://player.vimeo.com/video/${id}`;
                    const finalUrl = withParams(base, VmParams);
                    return finalUrl;
                }
        } catch (error) {
            console.error('Error parsing Vimeo URL:', error);
        }
    }

    // Fallback: return as-is
    console.error('Using fallback URL:', url);
    return url;
}

function withParams(base, params) {
    const query = new URLSearchParams(params).toString();
    return query ? `${base}?${query}` : base;
}
provide('embedUrl', embedUrl);

</script>

<template>
    <header :class="isHome ? 'white-cursor' : 'black-cursor'">
        <router-link class="logo" to="/">
            <img class="logo" :src="isHome ? '/images/logow.png' : '/images/logob.png'" alt="Logo" />
        </router-link>
        <img @click="ACTIVATETHEBURGER = true" width="32px" height="32px" :src="isHome ? '/images/icons/burgerW.svg' : '/images/icons/burgerB.svg'" alt="">
        <nav class="uppercase" :class="[isHome ? 'text-white' : 'text-black']">
            <router-link class="nav-link" :class="isHome ? 'after:bg-white' : 'after:bg-black'" to="/work">Work</router-link>
            <router-link class="nav-link" :class="isHome ? 'after:bg-white' : 'after:bg-black'" to="/shorts">Shorts</router-link>
            <router-link class="nav-link" :class="isHome ? 'after:bg-white' : 'after:bg-black'" to="/about">About</router-link>
            <router-link class="nav-link" :class="isHome ? 'after:bg-white' : 'after:bg-black'" to="/contact">Contact</router-link>
        </nav>
    </header>

    <div v-if="ACTIVATETHEBURGER" class="burger-menu" >
        <div class="flex justify-between self-stretch mb-[110px]">
            <img class="logo" src="/public/images/logob.png" alt="Logo" />
            <button @click="ACTIVATETHEBURGER = false" class="text-[32px] hover:opacite-50">✕</button>
        </div>
        <nav class="link-text flex flex-col items-center gap-[60px] text-[24px]">
            <router-link @click="ACTIVATETHEBURGER = false" to="/work">Work</router-link>
            <router-link @click="ACTIVATETHEBURGER = false" to="/shorts">Shorts</router-link>
            <router-link @click="ACTIVATETHEBURGER = false" to="/about">About</router-link>
            <router-link @click="ACTIVATETHEBURGER = false" to="/contact">Contact</router-link>
        </nav>
        <div class="mt-auto flex gap-[14px]">
            <a href="https://www.tiktok.com/@mozgi.ai" class="social-link" aria-label="TikTok" target="_blank">
                <img src="/public/images/icons/tiktokicon.svg" width="24" height="24" alt="TikTok">
            </a>
            <a href="https://www.youtube.com/@MozgiAI" class="social-link" aria-label="YouTube" target="_blank">
                <img src="/public/images/icons/youtubeicon.svg" width="24" height="24" alt="YouTube">
            </a>
            <a href="https://www.instagram.com/mozgi.ai" class="social-link" aria-label="Instagram" target="_blank">
                <img src="/public/images/icons/instagramicon.svg" width="24"  height="24" alt="Instagram">
            </a>
            <a href="https://www.facebook.com/share/1BJLkktYki" class="social-link" aria-label="Facebook" target="_blank">
                <img src="/public/images/icons/facebookicon.svg" width="24" height="24" alt="Facebook">
            </a>
        </div>
    </div>

    <router-view />
</template>

<style>
header {
    z-index: 999;
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 50px 50px 0px;
    max-width: 1540px;
}
.logo {
    width: 257px;
}
header nav {
    display: flex;
    gap: 50px;
    font-weight: 500;
    font-size: 18px;
    align-items: flex-start;
}

header > img {
    display: none;
}

.video-background {
    position: relative;
    width: 100%;
    height: 100vh;
    overflow: hidden;
}

.exit{
    font-size: 32px;
}
.exit:hover {
    opacity: 0.5;
}


.container {
    justify-items: center;
    margin: 0px auto;
    padding: 0px 50px 50px;
    max-width: 1540px;
}

.nav-link {
	font-size: 16px;
	font-variation-settings: "wdth" 112.5;
	font-weight: var(--font-weight-medium);
	letter-spacing: .3px;
	line-height: 18.4px;
	overflow: hidden;
	padding: 4px 0;
	position: relative;
	text-decoration: none;
	text-transform: none;
}
.nav-link::after {
	bottom: 0;
	content: "";
	height: 2px;
	left: 0;
	position: absolute;
	transform: translateX(-101%);
	transition: all ease 0.3s;
	width: 100%;
}
.nav-link:focus::after, .nav-link:hover::after {
	transform: translateX(0);
}
.nav-link.router-link-exact-active::after {
	opacity: .2;
	transform: translateX(0);
}

.burger-menu {
    background-color: white;
    position: fixed;
    width: 100vw;
    height: 100vh;
    inset: 0;
    padding: 14px 14px 100px;
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 999;
}

.link-text {
	font-size: 24px;
	font-variation-settings: "wdth" 112.5;
	font-weight: var(--font-weight-medium);
	line-height: .8;
	text-decoration: none;
	text-transform: uppercase;
	transition: color 0.3s;
}

.social-link {
    background-color: black;
    border-radius: 1000px;
    padding: 10px;
    margin-bottom: auto;
}

/* Normal cursor everywhere */
body {
  cursor: url('/public/images/icons/cursors/white.svg') 25 36.25, auto;
}

/* Pointer cursor on clickable elements */
a, button, img, video, iframe, footer, .clickable {
  cursor: url('/public/images/icons/cursors/neutral.svg') 25 36.25, pointer;
}

.white-cursor {
    cursor: url('/public/images/icons/cursors/white.svg') 25 36.25, pointer;
}

.black-cursor {
    cursor: url('/public/images/icons/cursors/black.svg') 25 36.25, pointer;
}

.neutral-cursor {
    cursor: url('/public/images/icons/cursors/neutral.svg') 25 36.25, pointer;
}


.page-transition {
  transition: opacity var(--transition-duration) ease, transform var(--transition-duration) ease;
}

.page-leave-active {
  transition: opacity var(--transition-duration) ease, transform var(--transition-duration) ease;
}

.page-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

@media (max-width: 1440px) {
    
header {
    margin-bottom: 36px;
    padding: 14px 14px 0px;
}

.logo {
    width: 173px;
}

.container {
    padding: 0px 14px;
}
}


@media (max-width: 768px) {
header img {
    display: unset;
}
header img:hover {
    opacity: 0.8;
}

header nav {
    display: none;
}
}
</style>