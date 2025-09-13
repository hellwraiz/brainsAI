<script setup>
import { ref, inject, computed, onMounted, onUnmounted, watch, reactive } from 'vue';
import VideoModal from './VideoModal.vue';
const index = ref(0);

const content = inject('content');
const length = computed(() => content.videos.length);
const video = computed(() => length ? content.videos[index.value] : null);
const aspectRatio = ref(false)

function getId() {
  if (length.value > 0) {
    const url = video.value.content_url;
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
    if (video.src !== videoStreamUrl.value) {
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

const updateIndexMobile = ((dir) => {
  if (dir === 'r') {
    index.value = (index.value + 1) % length.value;
  } else if (dir === 'l') {
    index.value = (index.value - 1 + length.value) % length.value;
  }
})

const updateAspectRatio = (() => (aspectRatio.value = ((window.innerWidth/16)/(window.innerHeight/9))>=1))

onMounted(() => {
  window.addEventListener('wheel', handleScroll, {passive: false});
  window.addEventListener('resize', updateAspectRatio)
  aspectRatio.value = ((window.innerWidth/16)/(window.innerHeight/9))>=1
});

onUnmounted(() => {
    window.removeEventListener('wheel', handleScroll);
    window.removeEventListener('resize', updateAspectRatio)
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
    <video v-if="video.isLocal === 'true'" class="video-iframe fixed min-w-screen min-h-screen" :key="videoStreamUrl" :src="videoStreamUrl" preload="metadata" autoplay muted loop playsinline ></video>
    <iframe v-else-if="aspectRatio" class="video-iframe fixed w-screen" :src="embedUrl(video.content_url, YtParams, VmParams)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
    <div v-else class="iframe-container">
      <iframe class="video-iframe h-screen" :src="embedUrl(video.content_url, YtParams, VmParams)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
    </div>
    <div class="videoElements">
      <div class="videoText">
        <h1>{{ video.title }}</h1>
        <h2>{{ video.description }}</h2>
        <!--
          <button class="watch-btn" @click="selectedVideo = video">
            <img :src="isHovered ? '/images/watch hover.png' : '/images/watch.png'" alt="watch button" @mouseenter="isHovered = true" @mouseleave="isHovered = false" />
          </button>
        -->
          <button class="watch-btn" @click="selectedVideo = video">
            WATCH
          </button>
      </div>
      <div class="listIndicators">
        <button class="index-btn" @click="updateIndexMobile('l')"><img src="/public/images/arrowLW.png" alt="left"></button>
        <div class="flex gap-[10px]">
          <img v-for="(_, itemIndex) in content.videos" :key="itemIndex" :src="itemIndex === index ? '/images/listItemActive.png' : '/images/listItem.png'"/>
        </div>
        <button class="index-btn" @click="updateIndexMobile('r')"><img src="/public/images/arrowRW.png" alt="right"></button>
      </div>
    </div>
  </div>

  <VideoModal v-model="selectedVideo"/>

</template>

<style scoped>

/* Normal cursor everywhere */
video {
  cursor: url('/public/images/icons/cursors/white.svg') 25 36.25, auto !important;
}

.iframe-container {
  cursor: url('/public/images/icons/cursors/white.svg') 25 36.25, auto !important;
  position: fixed;
  min-width: 100vw;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  inset: 0;
  background-color: black;
  margin: auto;
}

.video-iframe {
  aspect-ratio: 16/9;
  inset: 0;
  margin: auto auto;
  object-fit: cover;
  background-color: black;
}

iframe {
  pointer-events: none;
}

.videoElements {
  cursor: url('/public/images/icons/cursors/white.svg') 25 36.25, auto !important;
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

.videoText {
  width: 40%;
}

h1 {
  font-size: 3.6em;
  font-weight: 600;
  margin: 0;
	font-size: 34px;
	font-variation-settings: "wdth" 125;
	font-weight: var(--font-weight-bold);
	letter-spacing: 0;
	line-height: 1;
	text-transform: uppercase;
}

.page-title {
	color: var(--color-text-primary);
	font-family: var(--font-family-primary);
}

h2 {
  margin: 10px 0 30px;
	font-variation-settings: "wdth" 125;
	font-weight: var(--font-weight-normal);
	line-height: 1.5;
	text-transform: uppercase;
  opacity: 0.9;
}

.watch-btn {
  background-color: white;
  color: black;
  border: none;
  padding: 5px 30px;
  font-size: 1.4em;
  border-radius: 100vw;
  max-width: 500px;
}

.watch-button {
	background: white;
	border-radius: 32px;
	color: black;
	font-optical-sizing: auto;
	font-size: 14px;
	font-style: normal;
	font-variation-settings: "wdth" 150;
	font-weight: var(--font-weight-semibold);
	padding: 13px 30px;
	text-transform: uppercase;
	transition: all 0.3s;
  line-height: 1.15;
}

.index-btn img {
  width: 32px;
  height: 32px;
}
.index-btn:hover {
  opacity: 50%;
}

.listIndicators {
  display: flex;
  flex-direction: row;
  flex-shrink: 0;
  align-items: center;
  justify-content: space-between;
  align-self: flex-end;
}

.listIndicators img {
  cursor: url('/public/images/icons/cursors/white.svg') 25 36.25, auto !important;
}

.listIndicators > button {
  display: none;
}

html, body {
  overflow: hidden;
  margin: 0;
  padding: 0;
}


@media (max-width: 1440px) {
.videoElements {
  flex-direction: column;
  justify-content: unset;
  align-items: center;
  gap: 50px;
}

.videoText {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.listIndicators > div > img {
  width: 16px;
  height: 16px;
}

.listIndicators {
  align-self: stretch;
}

.listIndicators > button {
  display: unset;
}

h1 {
  font-size: 20px;
  width: unset;
}

h2 {
  font-size: 13px;
  width: unset;
}
}


</style>