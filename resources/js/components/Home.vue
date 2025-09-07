<script setup>
import { ref, inject, computed, onMounted, onUnmounted, watch } from 'vue';
const isHovered = ref(false);
const isActive = ref(false);
const videos = inject('videos');
const index = ref(0);

const length = computed(() => videos?.mains?.length || 0);
const main = computed(() => videos?.mains?.[index.value] || null);
const backgroundVideo = computed(() => '/storage/backgrounds/' + (main?.value?.background_video || null));


watch(() => videos.mains.length, (newLength) => {
  if (newLength === 0) {
    console.warn('No main videos available. Using default background video.');
  } else {
    console.log('Main videos loaded:', newLength);
  }
})

function handleScroll(event) {
  event.preventDefault()
  
  if (event.deltaY > 0) {
    index.value = (index.value + 1) % length.value;
  } else if (event.deltaY < 0) {
    index.value = (index.value - 1 + length.value) % length.value;
  }
}

onMounted(() => {
    window.addEventListener('wheel', handleScroll, {passive: false});
    console.log(videos)
});

onUnmounted(() => {
    window.removeEventListener('wheel', handleScroll);
});

</script>

<style scoped>
.background-video {
    position: fixed;
    top: 0;
    width: 100%;
    height: 100vh;
    z-index: -1;
    background-color: black;
}
.videoElements {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  padding: 0px 50px 50px;
  max-width: 1540px;
  margin: 0 auto;
  color: white;
}

h1 {
  font-size: 4em;
  font-weight: 600;
  margin: 0;
}

h2 {
  margin: 10px 0 30px;
}

button {
  background-color: white;
  color: black;
  border: none;
  padding: 5px 30px;
  font-size: 1.4em;
  border-radius: 100vw;
  max-width: 500px;
}

.listIndicators {
  display: flex;
  flex-direction: row;
  gap: 10px;
}

</style>

<template>
  <div v-if="videos?.mains?.length > 0">
    <div class="background-video">
      <video :key="backgroundVideo" :src="backgroundVideo" autoplay muted loop playsinline style="width: 100%; height:10 0%; object-fit: cover;" ></video>
    </div>
    <div class="videoElements">
      <div>
        <h1>{{ main.title }}</h1>
        <h2>{{ main.description }}</h2>
        <button>
          <img :src="isHovered ? '/images/watch hover.png' : '/images/watch.png'" alt="watch button" @mouseenter="isHovered = true" @mouseleave="isHovered = false" />
        </button>
      </div>
      <div class="listIndicators">
        <img v-for="(_, itemIndex) in videos.mains" :key="itemIndex" :src="itemIndex === index ? '/images/listItemActive.png' : '/images/listItem.png'"/>
      </div>
    </div>
  </div>
  <div style="background-color: black;" v-else>
  </div>
</template>