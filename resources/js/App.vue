<script setup>
    import { ref, onMounted, computed } from 'vue';
    import { useRoute } from 'vue-router'
    const route = useRoute();
    const isHome = computed(() => route.path === '/');


</script>

<template>
    <header class="flex flew-col">
        <router-link to="/">
            <img :src="isHome ? '/images/logow.png' : '/images/logob.png'" alt="Logo" />
        </router-link>
        <nav>
            <router-link to="/work">Work</router-link>
            <router-link to="/shorts">Shorts</router-link>
            <router-link to="/about">About</router-link>
            <router-link to="/contact">Contact</router-link>
        </nav>
    </header>
    test
</template>