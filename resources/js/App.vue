<script setup>
    import { ref, onMounted, computed } from 'vue';
    import { useRoute } from 'vue-router'
    const route = useRoute();
    const isHome = computed(() => route.path === '/');


</script>

<template>
    <header class="flex">
        <router-link to="/" class="">
            <img :src="isHome ? '/public/images/logow.png' : '../../public/images/logob.png'" alt="Logo" />
        </router-link>
        <nav class="">
            <div class="">
                <div>
                    <router-link to="/work" class="">Work</router-link>
                    <router-link to="/shorts" class="">Shorts</router-link>
                    <router-link to="/about" class="">About</router-link>
                    <router-link to="/contact" class="">Contact</router-link>
                </div>
            </div>
        </nav>
    </header>
    test
</template>