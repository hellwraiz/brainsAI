<script setup>

import { ref, inject, computed, onMounted, onUnmounted } from 'vue';

const content = inject('content');
const videos = computed(() => content.videos);

</script>

<style scoped>

.work {
        justify-items: center;
        margin: auto;
        padding: 0px 50px 50px;
        max-width: 1540px;
}

h1 {
  font-size: 2.4em;
  font-weight: 900;
  margin: 50px 0 30px;
  width: 60%;
  line-height: 1em;
}

</style>

<template>
    <div class="work">
        <h1>THE PROOF IS IN THE PIXELS. A PORTFOLIO OF AI-POWERED VISIONS MADE REAL.</h1> 
    </div>
    
</template>