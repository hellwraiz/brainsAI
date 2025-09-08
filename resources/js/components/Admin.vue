

<script setup>
import { ref, onMounted, inject, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'


const content = inject('content');
const videos = computed (() => content.videos);
const shorts = computed (() => content.shorts);
const images = computed (() => content.images);

const router = useRouter()
const user = ref(null)
const activeTab = ref('videos')

const tabs = [
  { id: 'videos', label: 'Videos' },
  { id: 'shorts', label: 'Shorts' },
  { id: 'images', label: 'Scroll Images' }
]

console.log("Content in Admin.vue", content);

const logout = async () => {
  try {
    await axios.post('/logout')
    router.push('/')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

function createContent() {
  alert(`Create new content in ${activeTab.value} tab`)
}

function editContent(id) {
  alert(`Edit content with ID: ${id} in ${activeTab.value} tab`)
}

function deleteContent(id) {
  alert(`Delete content with ID: ${id} in ${activeTab.value} tab`)
}

onMounted(async () => {
  try {
    const response = await axios.get('/user')
    user.value = response.data
  } catch (error) {
    router.push('/login')
  }
})

const editVideo = (video) => {
  console.log('Edit video:', video)
  // We'll implement this later with a popup
}

const deleteVideo = async (videoId) => {
  if (confirm('Are you sure you want to delete this video?')) {
    try {
      await axios.delete(`/videos/${videoId}`)
      videos.value = videos.value.filter(v => v.id !== videoId)
    } catch (error) {
      console.error('Delete failed:', error)
    }
  }
}

const changeOrder = async (image, direction) => {
  console.log(`Change order of image ID ${image.id} to the ${direction}`)
}

</script>

<style scoped>

.admin-nav {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}
.admin-nav button {
  padding: 10px 20px;
  border: none;
  background: #f0f0f0;
  cursor: pointer;
  font-weight: bold;
  border-radius: 5px;
}
.admin-nav button.active {
  background: #d0d0d0;
}
.admin-nav button:not(.active):hover {
  background: #d0d0d0;
}

.admin-nav button.logout-btn {
  margin-left: auto;
  background: #ff5050;
  color: white;
}
.admin-nav button.logout-btn:hover {
  background: #f02020;
}

h1 {
  font-size: 2.4em;
  font-weight: 900;
  margin: 30px 0;
}

.admin-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.admin-content > button {
  margin-top: 20px;
  padding: 10px 15px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.video-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.image-list {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  justify-content: center;
  gap: 20px
}

.content-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px;
  border: 1px solid #ddd;
  background-color: #f5f5f5;
  border-radius: 8px;
}

.btn{
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  color: white;
  opacity: 0.8;
}
.btn:hover {
  opacity: 1;
}
.arrow-btn {
  padding: 8px;
  background: #fdfdfd;
  width: 50px;
  border-radius: 8px;
  cursor: pointer;
}
.arrow-btn:hover {
  background: #d0d0d0;
}



</style>

<template>
  <div class="container">
    <h1>Admin Dashboard</h1>
    <!-- Navigation tabs -->
    <nav class="admin-nav">
      <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="{ active: activeTab === tab.id }">
        {{ tab.label }}
      </button>
      <button @click="logout" class="logout-btn">Logout</button>
    </nav>

    <main class="admin-content">

      <!-- Displaying videos and shorts-->
      <div class="video-list" v-if="activeTab === 'videos' || activeTab === 'shorts'" >
        <div class="content-item" v-for="video in content[activeTab]" :key="video.id">
            
          <video style="height: 150px; object-fit: cover;" :style="[ activeTab === 'videos' ? 'aspect-ratio: 16/9' : 'aspect-ratio: 9/16']" :src="video.content_url" controls></video>

          <div class="self-start flex flex-col gap-2">
            <h2>{{ video.title }}</h2>
            <p class="text-[#666]" >{{ video.description }}</p>
          </div>
          
          <div class="ml-auto flex-col flex gap-2 align-center justify-center">
            <button @click="changeOrder(video, 'l')">
              <img src="/public/images/arrowU.png" class="arrow-btn" alt="up">
            </button>
            <button @click="changeOrder(video, 'r')">
              <img src="/public/images/arrowD.png" class="arrow-btn" alt="down">
            </button>
          </div>
          <button @click="editVideo(video)" class=" bg-[#007bff] text-white btn">Edit</button>
          <button @click="deleteVideo(video.id)" class="bg-[#dc3545] text-white btn">Delete</button>
          
        </div>
      </div>
      


      <!-- Displaying images-->
      <div class="image-list" v-if="activeTab === 'images'">
        <div class="content-item flex-col" v-for="image in images" :key="image.id">
          <img :src="image.img_url"/>
          <div class="flex justify-around w-full">
            <button @click="changeOrder(image, 'l')">
              <img src="/public/images/arrowL.png" class="arrow-btn" alt="left">
            </button>
            <button @click="changeOrder(image, 'r')">
              <img src="/public/images/arrowR.png" class="arrow-btn" alt="right">
            </button>
          </div>
          <div class="flex gap-2 justify-center">
            <button @click="editVideo(image)" class="bg-[#007bff] text-white btn">Edit</button>
            <button @click="deleteVideo(image.id)" class="bg-[#dc3545] text-white btn">Delete</button>
          </div>
        </div>
      </div>
      <button @click="createContent" >Create new entry</button>
    </main>
  </div>
</template>