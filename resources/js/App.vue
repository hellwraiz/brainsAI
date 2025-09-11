<script setup>
import { ref, onMounted, onUnmounted, computed, provide, reactive } from 'vue';
import { useRoute } from 'vue-router'
const route = useRoute();
const isHome = computed(() => route.path === '/');

const screenWidth = ref(0);

const content = reactive({
    videos: [],
    shorts: [],
    images: []
});

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
    updateWidth()
    window.addEventListener('resize', updateWidth)
});

provide('content', content);


function updateWidth() {
screenWidth.value = window.innerWidth
}

const isLargeScreen = computed(() => screenWidth.value >= 1390)


onUnmounted(() => {
window.removeEventListener('resize', updateWidth)
})

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
    console.log('Input URL:', url); // Debug log
    console.log('params received:')
    console.log('youtube', YtParams)
    console.log('vimeo', VmParams)

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
        
            console.log('Extracted YouTube ID:', id); // Debug log
        
            if (id) {
                let base = `https://www.youtube.com/embed/${id}`;
                const finalUrl = withParams(base, YtParams);
                console.log('Final embed URL:', finalUrl); // Debug log
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
            console.log('Extracted Vimeo ID:', id); // Debug log
    
                if (id) {
                    let base = `https://player.vimeo.com/video/${id}`;
                    const finalUrl = withParams(base, VmParams);
                    console.log('Final embed URL:', finalUrl); // Debug log
                    return finalUrl;
                }
        } catch (error) {
            console.error('Error parsing Vimeo URL:', error);
        }
    }

    // Fallback: return as-is
    console.log('Using fallback URL:', url); // Debug log
    return url;
}

function withParams(base, params) {
    const query = new URLSearchParams(params).toString();
    return query ? `${base}?${query}` : base;
}
provide('embedUrl', embedUrl);


</script>

<template>
    <header>
        <router-link class="logo" to="/">
            <img class="logo" :src="isHome ? '/images/logow.png' : '/images/logob.png'" alt="Logo" />
        </router-link>
        <nav :class="[isHome ? 'text-white' : 'text-black']">
            <router-link to="/work">Work</router-link>
            <router-link to="/shorts">Shorts</router-link>
            <router-link to="/about">About</router-link>
            <router-link to="/contact">Contact</router-link>
        </nav>
    </header>

    <router-view></router-view>
</template>

<style>
header {
    align-self: center;
    display: flex;
    flex-grow: 1;
    justify-content: space-between;
    margin: auto;
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
}

.video-background {
    position: relative;
    width: 100%;
    height: 100vh;
    overflow: hidden;
}


.container {
    justify-items: center;
    margin: auto;
    padding: 0px 50px 50px;
    max-width: 1540px;
}

@media (max-width: 1440px) {
    
header {
    display: flex;
    flex-grow: 1;
    justify-content: space-between;
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

}
</style>