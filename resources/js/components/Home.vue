<script setup>
import { ref, inject, computed, onMounted, onUnmounted, watch, reactive } from 'vue';
import VideoModal from './VideoModal.vue';
const isHovered = ref(false);
const index = ref(0);

const content = inject('content');
const length = computed(() => content.videos.length);
const video = computed(() => length ? content.videos[index.value] : null);


function getId() {
  console.log("Ok, we got here!")
  if (length.value > 0) {
  console.log("Ok, we got here! 1")
    const url = video.value.content_url;
    if (url.includes('youtube.com') || url.includes('youtu.be')) {

  console.log("Ok, we got here! 2")
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
      
          return id;
      } catch (error) {
          console.error('Error parsing YouTube URL:', error);
      }
    }
  }
  return ''
}
const selectedVideo = ref(null);
const videoStreamUrl = computed(() => `/videos/${video.value.id}/stream`)

watch(index, (newIndex, oldIndex) => {
  // Pause all videos except the current one
  document.querySelectorAll('video').forEach(video => {
    console.log('test test', video)
    if (video.src !== videoStreamUrl.value) {
      console.log("paused video with index ", video.src)
      video.pause()
    }
  })
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
});

onUnmounted(() => {
    window.removeEventListener('wheel', handleScroll);
});




const YtParams = reactive({
  controls: 0,
  modestbranding: 1,
  rel: 0,
  autoplay: 1,
  mute: 1,
  loop: 1,
  playlist: '', // Will be set dynamically
  disablekb: 1,
  fs: 0,
})


watch(video, () => {
  YtParams.playlist = getId()
  console.log(getId())
  console.log("this should have changed!!", YtParams)
}, { immediate: true })

const VmParams = {
  controls: 0,        // Show controls
  title: 0,           // Hide title
  byline: 0,          // Hide author name
  portrait: 0,        // Hide author portrait
  autoplay: 1,        // Don't autoplay
  muted: 1,           // Mute by default (Vimeo uses 'muted' not 'mute')
  loop: 1,            // Don't loop
  background: 1,      // Don't enable background mode
  pip: 0,             // Disable picture-in-picture
  speed: 0,           // Hide playback speed controls
  keyboard: 0,        // Disable keyboard shortcuts
};

const embedUrl = inject('embedUrl')


</script>

<template>
  <div v-if="length > 0">
    <div class="background-video">
      <video v-if="video.isLocal === 'true'" :key="videoStreamUrl" :src="videoStreamUrl" preload="metadata" autoplay muted loop playsinline style="width: 100%; height:10 0%; object-fit: cover;" ></video>
      <iframe v-else :src="embedUrl(video.content_url, YtParams, VmParams)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy" style="width: 100%; height: 100%; object-fit: cover;"></iframe>
    </div>
    <div class="videoElements">
      <div>
        <h1>{{ video.title }}</h1>
        <h2>{{ video.description }}</h2>
        <button @click="selectedVideo = video">
          <img :src="isHovered ? '/images/watch hover.png' : '/images/watch.png'" alt="watch button" @mouseenter="isHovered = true" @mouseleave="isHovered = false" />
        </button>
      </div>
      <div class="listIndicators">
        <img v-for="(_, itemIndex) in content.videos" :key="itemIndex" :src="itemIndex === index ? '/images/listItemActive.png' : '/images/listItem.png'"/>
      </div>
    </div>
  </div>

  <VideoModal v-model="selectedVideo"/>

</template>

<style scoped>
.background-video {
    position: fixed;
    top: 0;
    width: 100%;
    height: 100vh;
    z-index: -1;
    background-color: black;
    overflow: hidden;
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