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
            console.log("Fetched videos", res.data)
            res = await axios.get(`/shorts`)
            content.shorts = res.data;
            console.log("Fetched shorts", res.data)
            res = await axios.get(`/scrollImages`)
            content.images = res.data;
            console.log("Fetched images", res.data)
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

</script>

<style scoped>
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
</style>

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