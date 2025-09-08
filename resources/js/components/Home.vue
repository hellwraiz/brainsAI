<script setup>
import { ref, inject, computed, onMounted, onUnmounted } from 'vue';
const isHovered = ref(false);
const index = ref(0);

const content = inject('content');
const length = computed(() => content.videos.length);
const video = computed(() => length ? content.videos[index.value] : null);
const backgroundVideo = computed(() => video ? video?.value?.content_url : null);

function handleScroll(event) {
  event.preventDefault()
  
  if (event.deltaY > 0) {
    index.value = (index.value + 1) % length.value;
  } else if (event.deltaY < 0) {
    index.value = (index.value - 1 + length.value) % length.value;
  }

  console.log("Scrolled to index:", index.value);
}

onMounted(() => {
    window.addEventListener('wheel', handleScroll, {passive: false});
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
  font-size: 3.6em;
  font-weight: 600;
  margin: 0;
  width: 40%;
}

h2 {
  margin: 10px 0 30px;
  width: 40%;
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
  flex-shrink: 0;
}

</style>

<template>
  <div v-if="length > 0">
    <div class="background-video">
      <video :key="backgroundVideo" :src="backgroundVideo" autoplay muted loop playsinline style="width: 100%; height:10 0%; object-fit: cover;" ></video>
    </div>
    <div class="videoElements">
      <div>
        <h1>{{ video.title }}</h1>
        <h2>{{ video.description }}</h2>
        <button>
          <img :src="isHovered ? '/images/watch hover.png' : '/images/watch.png'" alt="watch button" @mouseenter="isHovered = true" @mouseleave="isHovered = false" />
        </button>
      </div>
      <div class="listIndicators">
        <img v-for="(_, itemIndex) in content.videos" :key="itemIndex" :src="itemIndex === index ? '/images/listItemActive.png' : '/images/listItem.png'"/>
      </div>
    </div>
  </div>
  <div style="background-color: black;" v-else />
</template>