<script setup>
    import { ref, onMounted, onUnmounted, computed } from 'vue';
    import { useRoute } from 'vue-router'
    const route = useRoute();
    const isHome = computed(() => route.path === '/');

    const screenWidth = ref(0)

    function updateWidth() {
    screenWidth.value = window.innerWidth
    }

    onMounted(() => {
    updateWidth()
    window.addEventListener('resize', updateWidth)
    })

    onUnmounted(() => {
    window.removeEventListener('resize', updateWidth)
    })

    // Your breakpoint logic
    const isLargeScreen = computed(() => screenWidth.value >= 1390)


</script>

<style>
    .large {
        display: flex;
        justify-content: center;
    }
    .large > header {
        align-self: center;
        display: flex;
        flex-grow: 1;
        justify-content: space-between;
        margin: 50px 50px 0px;
        max-width: 1440px;
    }
    .logo {
        width: 257px;
    }
    .large > header nav {
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
    <div :class="isLargeScreen ? 'large' : 'small'">
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
    </div>
</template>