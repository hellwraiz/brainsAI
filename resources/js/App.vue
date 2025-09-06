<script setup>
    import { ref, onMounted, onUnmounted, computed, provide, reactive } from 'vue';
    import { useRoute } from 'vue-router'
    const route = useRoute();
    const isHome = computed(() => route.path === '/');
    
    const screenWidth = ref(0);
    
    const videos = reactive({
        mains: [],
        work: [],
        shorts: [],
    });

    async function fetchVideos(type) {
        try {
            const res = await axios.get(`/${type}`)
            videos[type] = res.data
        } catch (e) {
            console.error(`Failed to load ${type}`, e)
        }
    }

    onMounted(async () => {
        await Promise.all([
            fetchVideos('mains')
        ])
        updateWidth()
        window.addEventListener('resize', updateWidth)
    });

    provide('videos', videos);


    function updateWidth() {
    screenWidth.value = window.innerWidth
    }

    const isLargeScreen = computed(() => screenWidth.value >= 1390)


    onUnmounted(() => {
    window.removeEventListener('resize', updateWidth)
    })

</script>

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