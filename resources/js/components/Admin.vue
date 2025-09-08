

<script setup>
import { ref, onMounted, inject, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'


const content = inject('content');

const router = useRouter()
const user = ref(null)
const activeTab = ref('video')

const showEditModalVid = ref(false)
const showEditModalImg = ref(false)
const isCreating = ref(false)
const editFormVid = ref({
  id: null,
  title: '',
  description: '',
  type: '',
  order: 0,
  file: null
})
const editFormImg = ref({
  id: null,
  order: 0,
  file: null
})

const tabs = [
  { id: 'video', label: 'Video' },
  { id: 'short', label: 'Short' },
  { id: 'image', label: 'Scroll Image' }
]

function fetchVideos() {
  axios.get('/videos').then(response => {
    content.videos = response.data
    console.log(content.videos)
  })
  axios.get('/reels').then(response => {
    content.shorts = response.data
    console.log(content.shorts)
  })
}

function fetchImages() {
  axios.get('/scrollImages').then(response => {
    content.images = response.data
  })
}

const logout = async () => {
  try {
    await axios.post('/logout')
    router.push('/')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

onMounted(async () => {
  try {
    const response = await axios.get('/user')
    user.value = response.data
  } catch (error) {
    router.push('/login')
  }
})

const changeOrder = async (obj, dir) => {
  let obj1 = content[activeTab.value + 's'][obj.order + (dir === 'l' ? -1 : 1)]

  if (activeTab.value === 'video' || activeTab.value === 'short') {
    editFormVid.value = {
      id: obj.id,
      title: obj.title,
      description: obj.description,
      type: obj.type,
      order: obj1.order,
      file: null
    }
    updateVideo()
    editFormVid.value = {
      id: obj1.id,
      title: obj1.title,
      description: obj1.description,
      type: obj1.type,
      order: obj.order,
      file: null
    }
    updateVideo()
  } else {
    editFormImg.value = {
      id: obj.id,
      order: obj1.order,
      file: null
    }
    updateImage()
    editFormImg.value = {
      id: obj1.id,
      order: obj.order,
      file: null
    }
    updateImage()
  }

}

const createContent = () => {
  console.log(activeTab.value)
  if (activeTab.value === 'video' || activeTab.value === 'short') {
    editFormVid.value = { id: null, title: '', description: '', type: activeTab, order: content[activeTab.value + 's'].length, file: null }
    showEditModalVid.value = true
  } else {
    editFormImg.value = { id: null, order: content.images.length, file: null }
    showEditModalImg.value = true
  }
  isCreating.value = true
}

const editContent = (content) => {
  if (activeTab.value === 'video' || activeTab.value === 'short') {
    editFormVid.value = {
      id: content.id,
      title: content.title,
      description: content.description,
      type: content.type,
      order: content.order,
      file: null
    }
    showEditModalVid.value = true
  } else {
    editFormImg.value = {
      id: content.id,
      order: content.order,
      file: null
    }
    showEditModalImg.value = true
  }
  isCreating.value = false
}

const closeModal = () => {
  showEditModalVid.value = false
  showEditModalImg.value = false
  isCreating.value = false
  editFormVid.value = { id: null, title: '', description: '', type: '', order: 0, file: null }
  editFormImg.value = { id: null, order: 0, file: null }
}

const handleFileChange = (event) => {
  if (activeTab.value === 'video' || activeTab.value === 'short') {
    editFormVid.value.file = event.target.files[0]
  } else {
    editFormImg.value.file = event.target.files[0]
  }
}

const updateVideo = async () => {
  try {
    const formData = new FormData()
    formData.append('title', editFormVid.value.title)
    formData.append('description', editFormVid.value.description)
    formData.append('type', editFormVid.value.type)
    if (editFormVid.value.file) {
      formData.append('video_file', editFormVid.value.file)
    }
    formData.append('order', editFormVid.value.order)

    
    if (isCreating.value) {
      if (editFormVid.value.type === 'video') {
        await axios.post('/videos', formData, {
          headers: { 'Content-Type': 'multipart/form-data'}
        })
      } else {
          await axios.post('/reels', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
      }
    } else {
      if (editFormVid.value.type === 'video') {
        await axios.post(`/videos/${editFormVid.value.id}?_method=PATCH`, formData, {
          headers: { 'Content-Type': 'multipart/form-data'}
        })
      } else {
          await axios.post(`/reels/${editFormVid.value.id}?_method=PATCH`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
      }
    }
    closeModal()
    fetchVideos()
  } catch (error) {
    console.error('Update failed:', error)
  }
}

const updateImage = async () => {

  try {
    const formData = new FormData()
    if (editFormImg.value.file) {
      formData.append('img_file', editFormImg.value.file)
    }
    formData.append('order', editFormImg.value.order)

    
    if (isCreating.value) {
      await axios.post('/scrollImages', formData, {
        headers: { 'Content-Type': 'multipart/form-data'}
      })
    } else {
      await axios.post(`/scrollImages/${editFormImg.value.id}?_method=PATCH`, formData, {
        headers: { 'Content-Type': 'multipart/form-data'}
      })
    }
    closeModal()
    fetchImages()
  } catch (error) {
    console.error('Update failed:', error)
  }
}

const deleteContent = async (ContentId) => {
  if (confirm('Are you sure you want to delete this video?')) {
    try {
      if (activeTab.value === 'video') {
        await axios.delete(`/videos/${ContentId}`)
      } else if (activeTab.value === 'short') {
        await axios.delete(`/reels/${ContentId}`)
      } else {
        await axios.delete(`/scrollImages/${ContentId}`)
      }
      fetchVideos()
      fetchImages()
    } catch (error) {
      console.error('Delete failed:', error)
    }
  }
}
</script>



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
      <div class="video-list" v-if="activeTab === 'video' || activeTab === 'short'" >
        <div class="content-item" v-for="video in content[activeTab + 's']" :key="video.id">
            
          <video style="height: 150px; object-fit: cover;" :style="[ activeTab === 'video' ? 'aspect-ratio: 16/9' : 'aspect-ratio: 9/16']" :src="video.content_url" controls></video>

          <div class="self-start flex flex-col gap-2">
            <h2>{{ video.title }}</h2>
            <p class="text-[#666]" >{{ video.description }}</p>
          </div>
          
          <div class="ml-auto flex-col flex gap-2 align-center justify-center">
            <button v-if="video.order > 0" @click="changeOrder(video, 'l')">
              <img src="/public/images/arrowU.png" class="arrow-btn" alt="up">
            </button>
            <button v-if="video.order < content[activeTab + 's'].length - 1" @click="changeOrder(video, 'r')">
              <img src="/public/images/arrowD.png" class="arrow-btn" alt="down">
            </button>
          </div>
          <button @click="editContent(video)" class=" bg-[#007bff] text-white btn">Edit</button>
          <button @click="deleteContent(video.id)" class="bg-[#dc3545] text-white btn">Delete</button>
          
        </div>
      </div>
      


      <!-- Displaying images-->
      <div class="image-list" v-if="activeTab === 'image'">
        <div class="content-item flex-col" v-for="image in content.images" :key="image.id">
          <img style="width: 161px; height: 207px; object-fit: cover;" :src="image.img_url"/>
          <div class="flex justify-around w-full">
            <button v-if="image.order > 0" @click="changeOrder(image, 'l')">
              <img src="/public/images/arrowL.png" class="arrow-btn" alt="left">
            </button>
            <button v-if="image.order < content[activeTab + 's'].length - 1" @click="changeOrder(image, 'r')">
              <img src="/public/images/arrowR.png" class="arrow-btn" alt="right">
            </button>
          </div>
          <div class="flex gap-2 justify-center">
            <button @click="editContent(image)" class="bg-[#007bff] text-white btn">Edit</button>
            <button @click="deleteContent(image.id)" class="bg-[#dc3545] text-white btn">Delete</button>
          </div>
        </div>
      </div>
      <button @click="createContent">Create new entry</button>
    </main>
  </div>


  <!-- ------------------------------------------ Edit Modal (for video) ---------------------------------------- -->
  <div v-if="showEditModalVid" class="modal-overlay" @click="closeModal">
    <div class="modal" @click.stop>
      <h2 v-if="isCreating">Add video</h2>
      <h2 v-else>Change video info</h2>
      <form @submit.prevent="updateVideo">
        <div>
          <label>Title:</label>
          <input v-model="editFormVid.title" type="text" required />
        </div>
        
        <div>
          <label>Description:</label>
          <textarea v-model="editFormVid.description" required></textarea>
        </div>
        
        <div>
          <label v-if="isCreating">Video to upload:</label>
          <label v-else>Change video(optional):</label>
          <input @change="handleFileChange" type="file" accept="video/*" />
        </div>
        
        <div class="modal-actions">
          <button type="button" @click="closeModal">Cancel</button>
          <button v-if="isCreating" type="submit">Add video</button>
          <button v-else type="submit">Update video</button>
        </div>
      </form>
    </div>
  </div>


  <!-- ------------------------------------------ Edit Modal (for image) ---------------------------------------- -->
  <div v-if="showEditModalImg" class="modal-overlay" @click="closeModal">
    <div class="modal" @click.stop>
      <h2 v-if="isCreating">Add image</h2>
      <h2 v-else>Change image info</h2>
      <form @submit.prevent="updateImage">
        
        <div>
          <label v-if="isCreating">Image to upload:</label>
          <label v-else>Change image(optional):</label>
          <input @change="handleFileChange" type="file" accept="image/*" />
        </div>
        
        <div class="modal-actions">
          <button type="button" @click="closeModal">Cancel</button>
          <button v-if="isCreating" type="submit">Add image</button>
          <button v-else type="submit">Update image info</button>
        </div>
      </form>
    </div>
  </div>
  
</template>

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





.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background: white;
  padding: 20px;
  border-radius: 8px;
  max-width: 500px;
  width: 90%;
  max-height: 80vh;
  overflow-y: auto;
}

.modal form > div {
  margin-bottom: 15px;
}

.modal label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.modal input, .modal textarea, .modal select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}
</style>