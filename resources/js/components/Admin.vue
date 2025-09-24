

<script setup>
import { ref, onMounted, inject, reactive, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'


const content = inject('content');
const extractFirstFrame = inject('extractFirstFrame');
const getVideoThumbnail = inject('getVideoThumbnail');

const router = useRouter()
const user = ref(null)
const activeTab = ref('videos')

const showEditModalVid = ref(false)
const showEditModalImg = ref(false)
const isCreating = ref(false)
const uploadingFile = ref(true)
const editFormVid = reactive({
  id: null,
  title: '',
  description: '',
  isVideo: true,
  isLocal: true,
  order: 0,
  file: null,
  file_url: '',
  img: null,
  img_url: ''
})
const editFormImg = reactive({
  id: null,
  order: 0,
  file: null
})
const editText = reactive({})
watch(() => content.text, (newText) => {
  Object.entries(newText).forEach(entry => editText[entry[0]] = entry[1])
}, { immediate: true })

const tabs = [
  { id: 'videos', label: 'Videos' },
  { id: 'shorts', label: 'Shorts' },
  { id: 'images', label: 'Scroll Images' },
  { id: 'text', label: 'Page Text'}
]

function fetchVideos() {
  axios.get('/videos').then(response => {
    content.videos = response.data
  })
  axios.get('/reels').then(response => {
    content.shorts = response.data
  })
}

function fetchImages() {
  axios.get('/scrollImages').then(response => {
    content.images = response.data
  })
}
function fetchText() {
  axios.get('/texts').then(response => {
    const textMap = {};
    response.data.forEach(item => {
        textMap[item.type] = item.content;
    });
    content.text = textMap
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
  let obj1 = content[activeTab.value][obj.order + (dir === 'l' ? -1 : 1)]

  if (activeTab.value === 'videos' || activeTab.value === 'shorts') {
    uploadingFile.value = obj.isLocal
    Object.assign(editFormVid, {
      value: obj.isLocal,
      id: obj.id,
      title: obj.title,
      description: obj.description,
      isVideo: obj.isVideo === 'true',
      isLocal: obj.isLocal === 'true',
      order: obj1.order,
      file: null,
      file_url: '',
      img: null,
      img_url: ''
    })
    await updateVideo()
    uploadingFile.value = obj1.isLocal
    Object.assign(editFormVid, {
      id: obj1.id,
      title: obj1.title,
      description: obj1.description,
      isVideo: obj1.isVideo === 'true',
      isLocal: obj1.isLocal === 'true',
      order: obj.order,
      file: null,
      file_url: '',
      img: null,
      img_url: ''
    })
    await updateVideo()
  } else {
    Object.assign(editFormImg, {
      id: obj.id,
      order: obj1.order,
      file: null
    })
    updateImage()
    Object.assign(editFormImg, {
      id: obj1.id,
      order: obj.order,
      file: null
    })
    updateImage()
  }

}

const createContent = () => {
  isCreating.value = true
  if (activeTab.value === 'videos' || activeTab.value === 'shorts') {
    Object.assign(editFormVid, {
      id: null,
      title: '',
      description: '',
      isVideo: (activeTab.value === 'videos'),
      isLocal: uploadingFile.value,
      order: content[activeTab.value].length,
      file: null,
      file_url: '',
      img: null,
      img_url: ''
    })
    showEditModalVid.value = true
  } else {
    Object.assign(editFormImg, {
      id: null,
      order: content.images.length,
      file: null
    })
    showEditModalImg.value = true
  }
}

const editContent = (content) => {
  if (activeTab.value === 'videos' || activeTab.value === 'shorts') {
    uploadingFile.value = content.isLocal
    Object.assign(editFormVid, {
      id: content.id,
      title: content.title,
      description: content.description,
      isVideo: content.isVideo === 'true',
      isLocal: uploadingFile.value,
      order: content.order,
      file: null,
      file_url: null,
      img: null,
      img_url: null
    })
    showEditModalVid.value = true
  } else {
    Object.assign(editFormImg, {
      id: content.id,
      order: content.order,
      file: null
    })
    showEditModalImg.value = true
  }
}

const closeModal = () => {
  showEditModalVid.value = false
  showEditModalImg.value = false
  isCreating.value = false
  Object.assign(editFormVid, { id: null, title: '', description: '', isVideo: true, isLocal:true, order: 0, file: null, file_url: '', img: null, img_url: '' })
  Object.assign(editFormImg, { id: null, order: 0, file: null })
  uploadingFile.value = true
}

const handleFileChange = async (event) => {
  if (activeTab.value === 'videos' || activeTab.value === 'shorts') {
    editFormVid.file = event.target.files[0]
    editFormVid.img = await extractFirstFrame(event.target.files[0])
  } else {
    editFormImg.file = event.target.files[0]
  }
}

const updateVideo = async () => {
  try {
    editFormVid.isLocal = uploadingFile.value
    const formData = new FormData()
    formData.append('title', editFormVid.title)
    formData.append('description', editFormVid.description)
    formData.append('isVideo', editFormVid.isVideo)
    formData.append('isLocal', editFormVid.isLocal)
    if (uploadingFile.value && editFormVid.file) {
      formData.append('video_file', editFormVid.file)
      formData.append('image_file', editFormVid.img)
    } else if (!uploadingFile.value && editFormVid.file_url) {
      formData.append('video_url', editFormVid.file_url)
      formData.append('image_url', getVideoThumbnail(editFormVid.file_url))
    }
    formData.append('order', editFormVid.order)

    showEditModalVid.value = false

    if (isCreating.value) {
      if (editFormVid.isVideo) {
        await axios.post('/videos', formData, {
          headers: { 'Content-Type': 'multipart/form-data'}
        })
      } else {
          await axios.post('/reels', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
      }
    } else {
      if (editFormVid.isVideo) {
        await axios.post(`/videos/${editFormVid.id}?_method=PATCH`, formData, {
          headers: { 'Content-Type': 'multipart/form-data'}
        })
      } else {
          await axios.post(`/reels/${editFormVid.id}?_method=PATCH`, formData, {
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
    if (editFormImg.file) {
      formData.append('img_file', editFormImg.file)
    }
    formData.append('order', editFormImg.order)

    showEditModalImg.value = false
    
    if (isCreating.value) {
      await axios.post('/scrollImages', formData, {
        headers: { 'Content-Type': 'multipart/form-data'}
      })
    } else {
      await axios.post(`/scrollImages/${editFormImg.id}?_method=PATCH`, formData, {
        headers: { 'Content-Type': 'multipart/form-data'}
      })
    }
    closeModal()
    fetchImages()
  } catch (error) {
    console.error('Update failed:', error)
  }
}

const updateText = async () => {
  try {
    for (let entry of Object.entries(editText)) {
      if (entry[1] != content.text[entry[0]]) {
        await axios.put(`/texts/${entry[0]}`, { content: entry[1] } )
      }
    }

    fetchText()
  } catch (error) {
    console.error('Update failed:', error)
  }
}

const deleteContent = async (ContentId) => {
  if (confirm('Are you sure you want to delete this video?')) {
    try {
      if (activeTab.value === 'videos') {
        await axios.delete(`/videos/${ContentId}`)
      } else if (activeTab.value === 'shorts') {
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

// --------------------------------------  EXTERNAL LINK STUFF ------------------------------------------------

const embedUrl = inject('embedUrl')


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
      <div class="video-list" v-if="activeTab === 'videos' || activeTab === 'shorts'" >
        <div class="content-item" v-for="video in content[activeTab]" :key="video.id">
            
          <video v-if="video.isLocal === 'true'" style="object-fit: cover;" :style="[ activeTab === 'videos' ? 'aspect-ratio: 16/9; height: 150px' : 'aspect-ratio: 9/16; height: 250px']" :src="video.content_url" controls></video>
          <iframe v-else style="object-fit: cover;" :style="[ activeTab === 'videos' ? 'aspect-ratio: 16/9; height: 150px' : 'aspect-ratio: 9/16; height: 250px']" :src="embedUrl(video.content_url)" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>

          <div class="self-start flex flex-col gap-2">
            <h2>{{ video.title }}</h2>
            <p class="text-[#666]" >{{ video.description }}</p>
            <p class="text-[14px] text-[#666]">Is this video local? {{ video.isLocal }}</p>
          </div>
          
          <div class="ml-auto flex-col flex gap-2 align-center justify-center" >
            <button v-if="video.order > 0" @click="changeOrder(video, 'l')">
              <img src="/public/images/arrowU.png" class="arrow-btn" alt="up">
            </button>
            <button v-if="video.order < content[activeTab].length - 1" @click="changeOrder(video, 'r')">
              <img src="/public/images/arrowD.png" class="arrow-btn" alt="down">
            </button>
          </div>
          <button @click="editContent(video)" class=" bg-[#007bff] text-white btn">Edit</button>
          <button @click="deleteContent(video.id)" class="bg-[#dc3545] text-white btn">Delete</button>
          
        </div>
      </div>
      


      <!-- Displaying images-->
      <div class="image-list" v-if="activeTab === 'images'">
        <div class="content-item flex-col" v-for="image in content.images" :key="image.id">
          <img style="width: 161px; height: 207px; object-fit: cover;" :src="image.img_url"/>
          <div class="flex justify-around w-full">
            <button v-if="image.order > 0" @click="changeOrder(image, 'l')">
              <img src="/public/images/arrowL.png" class="arrow-btn" alt="left">
            </button>
            <button v-if="image.order < content[activeTab].length - 1" @click="changeOrder(image, 'r')">
              <img src="/public/images/arrowR.png" class="arrow-btn" alt="right">
            </button>
          </div>
          <div class="flex gap-2 justify-center">
            <button @click="editContent(image)" class="bg-[#007bff] text-white btn">Edit</button>
            <button @click="deleteContent(image.id)" class="bg-[#dc3545] text-white btn">Delete</button>
          </div>
        </div>
      </div>
      <button v-if="activeTab != 'text'" @click="createContent">Create new entry</button>
      <div class="text-config" v-if="activeTab === 'text'">
        <div>
          <h2>Work page title</h2>
          <input v-model="editText['work title']" type="text"/>
        </div>
        <div>
          <h2>Work page description</h2>
          <textarea v-model="editText['work desc']"/>
        </div>
        <div>
          <h2>Shorts page title</h2>
          <input v-model="editText['shorts title']" type="text"/>
        </div>
        <div>
          <h2>Shorts page description</h2>
          <textarea v-model="editText['shorts desc']" />
        </div>
        <div>
          <h2>About main title</h2>
          <input v-model="editText['about main title']" type="text"/>
        </div>
        <div class="flex flex-row gap-[25px]">
          <div style="flex: 1;">
            <div>
              <h2>About left title</h2>
              <input v-model="editText['about left title']"/>
            </div>
            <div>
              <h2>About left description</h2>
              <textarea v-model="editText['about left desc']"/>
            </div>
          </div>
          <div style="flex: 1;">
            <div>
              <h2>About middle title</h2>
              <input v-model="editText['about middle title']"/>
            </div>
            <div>
              <h2>About middle description</h2>
              <textarea v-model="editText['about middle desc']"/>
            </div>
          </div>
          <div style="flex: 1;">
            <div>
              <h2>About right title</h2>
              <input v-model="editText['about right title']"/>
            </div>
            <div>
              <h2>About right description</h2>
              <textarea v-model="editText['about right desc']"/>
            </div>
          </div>
        </div>
        <div>
          <h2>Contact page title</h2>
          <input v-model="editText['contact title']" type="text"/>
        </div>
      </div>
      <button v-if="activeTab == 'text'" @click="updateText">Update text</button>
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

        <div class="admin-nav">
          <nav @click="uploadingFile = true" :class="{ active: uploadingFile}">Upload file</nav>
          <nav @click="uploadingFile = false" :class="{ active: !uploadingFile}">Use external link</nav>
        </div>

        <div>
          <div v-if="uploadingFile">
            <label v-if="isCreating">Video to upload:</label>
            <label v-else>Change video(optional):</label>
            <input v-if="isCreating" @change="handleFileChange" type="file" accept="video/*" required />
            <input v-else @change="handleFileChange" type="file" accept="video/*" />
          </div>
  
          <div v-else>
            <label v-if="isCreating">Video to upload:</label>
            <label v-else>Change video(optional):</label>
            <input v-if="isCreating" v-model="editFormVid.file_url" type="text" required />
            <input v-else v-model="editFormVid.file_url" type="text"/>
          </div>
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
.admin-nav nav,
.admin-nav button {
  padding: 10px 20px;
  border: none;
  background: #f0f0f0;
  cursor: pointer;
  font-weight: bold;
  border-radius: 5px;
}
.admin-nav nav.active,
.admin-nav button.active {
  background: #d0d0d0;
}
.admin-nav nav:not(.active):hover,
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

h2 {
  font-size: 20px;
  font-weight: 700;
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
  transition: all 0.3s ease;
}
.admin-content > button:hover {
  background-color: #1c791f;
}

.text-config,
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
.text-config > div {
  background-color: #f5f5f5;
  padding: 20px;
  border-radius: 8px;
}
.text-config input,
.text-config textarea {
  background-color: #d0d0d0;
  padding: 5px;
  border-radius: 4px;
  border: 1px solid black;
  width: 100%;
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