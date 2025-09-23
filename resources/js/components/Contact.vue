<script setup>

import { ref, reactive, watch, onMounted, nextTick, inject } from 'vue'
import axios from 'axios'

const content = inject('content');
const form = reactive({
  name: '',
  email: '',
  phone: '',
  description: ''
})
const selectedFiles = ref([])
const message = ref('')
const errors = reactive({})
const isValid = ref(false)

const handleFileChange = (event) => {
  const files = Array.from(event.target.files)
  selectedFiles.value = [...selectedFiles.value, ...files]
}

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
}

const clearMessages = () => {
  message.value = ''
  Object.keys(errors).forEach(key => delete errors[key])
}

const validateForm = () => {
  clearMessages()
  isValid.value = true

  if (!form.email.trim()) {
    errors.email = true
    message.value = "Please enter an email"
    isValid.value = false
  } else if (!/^[a-zA-Z0-9._%+-]+@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/.test(form.email)) {
    errors.email = true
    message.value = "Please enter a valid email"
    isValid.value = false
  }

  if (!form.name.trim()) {
    errors.name = true
    message.value = "Please enter a valid name"
    isValid.value = false
  }
}
watch(form, validateForm, { deep: true })

const getFileIcon = (filename) => {
  const ext = filename.split('.').pop().toLowerCase();
  const iconMap = {
    pdf: '/images/icons/fileExtensions/pdf.svg',
    word: '/images/icons/fileExtensions/word.svg',
    doc: '/images/icons/fileExtensions/word.svg',
    docx: '/images/icons/fileExtensions/word.svg',
    jpg: '/images/icons/fileExtensions/jpg.svg',
    jpeg: '/images/icons/fileExtensions/jpg.svg',
    png: '/images/icons/fileExtensions/png.svg',
    xl: '/images/icons/fileExtensions/xl.svg'
  };
  return iconMap[ext] || '/images/icons/fileExtensions/xl.svg';
};

// Handle form submission
const handleSubmit = async () => {
  validateForm()
  if (!isValid.value) return

  isValid.value = false
  clearMessages()

  try {
    // Create FormData for file upload
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('email', form.email)
    if (form.phone) {
      formData.append('phone', form.phone)
    }
    if (form.description) {
      formData.append('message', form.description)
    }
    
    // Append files
    selectedFiles.value.forEach((file, index) => {
      formData.append(`files[${index}]`, file)
    })

    // Send to your Laravel backend
    await axios.post('/submit-form', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    // Reset form
    Object.keys(form).forEach(key => form[key] = '')
    selectedFiles.value = []
    
    // Clear file input
    const fileInput = document.getElementById('files')
    if (fileInput) fileInput.value = ''

    setTimeout(() => {
      // Success
      isValid.value = true;
      message.value = 'Thank you! Your message has been sent successfully.'
    }, 100)

  } catch (error) {
    console.error('Contact form error:', error)
    
    if (error.response?.data?.errors) {
      console.error(error.response.data.errors)
    } else {
      message.value = 'Sorry, there was an error sending your message. Please try again at a later time.'
    }
  } finally {
    isValid.value = true
  }
}

const isEntering = ref(true);
onMounted( async () => {
  // To make sure that everything is loaded before starting the animation.
  await nextTick();

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      isEntering.value = false;
    });
  });
})

</script>

<template>
  <div class="container page-transition" :class="{ 'page-enter-from': isEntering }" >
    <h1>{{ content.text['contact title'] }}</h1>
    <div class="flex flex-col desktop:flex-row gap-[20px] desktop:gap-5">
      <!-- Social Media Icons -->
      <div class="flex gap-[18px]">
        <a href="https://www.tiktok.com/@mozgi.ai" class="social-link" aria-label="TikTok" target="_blank">
          <img src="/public/images/icons/tiktokicon.svg" width="24" height="24" alt="TikTok">
        </a>
        <a href="https://www.youtube.com/@MozgiAI" class="social-link" aria-label="YouTube" target="_blank">
          <img src="/public/images/icons/youtubeicon.svg" width="24" height="24" alt="YouTube">
        </a>
        <a href="https://www.instagram.com/mozgi.ai" class="social-link" aria-label="Instagram" target="_blank">
          <img src="/public/images/icons/instagramicon.svg" width="24"  height="24" alt="Instagram">
        </a>
        <a href="https://www.facebook.com/share/1BJLkktYki" class="social-link" aria-label="Facebook" target="_blank">
          <img src="/public/images/icons/facebookicon.svg" width="24" height="24" alt="Facebook">
        </a>
      </div>

      <!-- Contact Info -->
      <div class="flex flex-col desktop:flex-row gap-[20px] desktop:gap-10">
        <div class="flex flex-col">
          <span class="info-label">ADDRESS</span>
          <address class="info-value not-italic leading-none" >Carrer de Larrard, 20, Gràcia, 08012 Barcelona<br>1830 Radius Drive Hollywood, FL 33020</address>
        </div>
        <div class="flex flex-col">
          <span class="info-label">EMAIL</span>
          <a href="mailto:contact@mozgi.ai" class="info-value">contact@mozgi.ai</a>
        </div>
      </div>
    </div>
      
  </div>
  
  <footer class="page-transition" :class="{ 'page-enter-from': isEntering }" >
    <div>
      <h1>CONTACT US</h1>
      <form @submit.prevent="handleSubmit" class="">
        <div class="contact-container">
          <div class="contact-form" >
            <div class="flex flex-col desktop:flex-row desktop:gap-[20px]">
              <div class="input-box-top">
                <label for="name" class="input-label">Name</label>
                <input id="name" v-model="form.name" type="text" class="input-field" :class="{ 'input-field-bad': errors.name }" placeholder="Your Name"/>
              </div>

              <div class="input-box-top">
                <label for="email" class="input-label">Email</label>
                <input id="email" v-model="form.email" type="email" class="input-field" :class="{ 'input-field-bad': errors.email }" placeholder="Your Email"/>
              </div>

              <div class="input-box-top">
                <label for="phone" class="input-label">Phone</label>
                <input id="phone" v-model="form.phone" type="tel" class="input-field" placeholder="Your Phone"/>
              </div>
            </div>

              <div class="flex flex-col desktop:flex-row desktop:gap-[34px] items-center">
                <div class="w-full desktop:w-[660px] relative">
                  <label for="description" class="input-label">Description</label>
                  <textarea id="description" v-model="form.description" rows=1 class="mb-[24px] desktop:mb-0 input-field input-field--alt max-h-[120px]" :class="{ 'border-red-500': errors.description }" placeholder="Tell us about your project..."></textarea>
                </div>

                <div class="flex flex-col self-stretch desktop:self-center ">
                  <label for="files" class="self-end cursor-pointer">
                    <img src="/public/images/send_materials.png" alt="Upload" class="w-[180px] hover:opacity-50">
                  </label>
                  <input id="files" class="hidden" @change="handleFileChange" type="file" multiple/>
                  
                  <!-- File List -->
                  <div v-if="selectedFiles.length > 0">
                    <div v-for="(file, index) in selectedFiles" :key="index" class=" text-white flex items-center py-[12px] gap-[10px] justify-between">
                      <img :src="getFileIcon(file.name)" class="w-10 h-10">
                      <div class="flex">
                        <span class="file-text">{{ file.name }}</span>
                        <button @click="removeFile(index)" type="button" class="w-[26px] p-[5px]">
                          <img class="close-img" src="/public/images/icons/close.svg" alt="">
                        </button>
                      </div>  
                    </div>
                  </div>
                </div>
              </div>
          </div>

          <!-- Submit Button -->
          <button type="submit" :class="isValid ? '' : 'invalid'" :disabled="!isValid" class="form-submit-btn-desk">
            <img src="/public/images/send_laptop.png" alt="send">
          </button>
          <button type="submit" :class="isValid ? '' : 'invalid'" :disabled="!isValid" class="form-submit-btn" aria-label="Send request">
            <img width="36" height="36" src="/public/images/icons/send.svg" alt="send">
            <span>SEND A REQUEST</span>
          </button>
        </div>
        <div id="form-messages" :class="isValid ? 'success' : 'failure'" class="form-messages">{{ message }}</div>
      </form>
    </div>
  </footer>
</template>

<style scoped>

.container {
  padding-bottom: 76px;
}

.container > h1 {
  font-size: 34px;
  font-variation-settings: "wdth" 125;
  letter-spacing: 0;
  line-height: 1;
  font-weight: 900;
  margin: 36px 0 30px;
  width: 65%;
  text-transform: uppercase;
}

.container > div {
    display: flex;
    width: 80%;
    gap: 20px;
}

.contact-container {
  display: flex;
  gap: 145px;
  justify-content: space-between;
  align-items: center;
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 25px;
  margin: auto 0px 24px;
}

.social-link {
    background-color: black;
    border-radius: 1000px;
    padding: 10px;
    margin-bottom: auto;
}

.info-label {
    color: #262626;
    display: block;
    font-size: 10px;
    font-variation-settings: "wdth" 112.5;
    font-weight: 700;
    letter-spacing: 1px;
    line-height: 1.5;
    opacity: .5;
    text-transform: uppercase;
}

.form-messages {
  position: absolute;
  bottom: -30px;
  margin-top: 30px;
}
.success {
  color: green;
}
.failure {
  color: red;
}

.info-value:hover {
  opacity: 0.5;
}

footer {
  margin-top: auto;
  background-color: #262626;
  padding: 55px 50px 70px;
  display: flex;
  width: 100%;
  cursor: url('/public/images/icons/cursors/white.svg') 25 36.25, auto;
}

footer > div {
    margin: auto;
    width: 100%;
    max-width: 1540px;
}

form {
  position: relative;
}

footer h1 {
  color: white;
  line-height: 1;
  font-weight: 900;
  font-size: 34px;
}

.close-img:hover { content: url('/public/images/icons/closeRed.svg'); }


.input-field--alt {
  width: 660px;
}

.input-box-top {
  width: 280px;
  margin-bottom: 33px;
  position: relative;
}
.input-field {
  width: 100%;
  background-color: white;
  border-radius: 4px;
  border: 2px solid transparent;
  padding: 11px 5px 12px 24px;
  font-size: 16px;
  letter-spacing: .3px;
  line-height: 1.5;
  transition: all 0.3s ease;
  font-variation-settings: "width" 112.5;
}
.input-field:hover {
  cursor: url('/public/images/icons/cursors/neutral.svg') 25 36.25, pointer;
  opacity: 0.8;
}
.input-field-bad {
  border-color: red;
}

.input-label {
  color: #fff;
  display: block;
  font-size: 10px;
  position: absolute;
  top: -16px;
  left: 0px;
  text-transform: uppercase;
  font-variation-settings: "width" 112.5;
  font-weight: 500;
}

.form-submit-btn {
  display: none;
}
.form-submit-btn-desk {
  transition: all ease 0.3s;
  height: 220px;
  width: 220px;
}
.form-submit-btn-desk img:hover {
  content: url('/public/images/send_laptop_hover.png')
}
.form-submit-btn-desk.invalid {
  opacity: 0.7;
}
.form-submit-btn-desk.invalid img:hover {
  content: unset
}

.file-text {
	font-size: 15px;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
  text-align: right;
}


/* THIS STUFF IS ALL FOR MAKING IT WORK ON MOBILE */

@media (max-width: 1440px) {
.container > h1 {
  font-size: 20px;
  text-transform: uppercase;
  width: 100%;
}

.container {
  padding-bottom: 30px;
}

footer {
  margin-top: 0px;
  padding: 30px 14px 40px;
  display: flex;
  width: 100%;
}

footer h1 {
  font-size: 20px;
  margin-bottom: 39px;
}

.contact-container {
  flex-direction: column;
  gap: 20px;
}

.contact-form {
  width: 100%;
  gap: 0px
}

.input-box-top {
  width: 100%;
}

.form-submit-btn {
  background-color: white;
  color: black;
  gap: 14px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 32px;
  font-size: 20px;
	font-variation-settings: "wdth" 125;
	font-weight: var(--font-weight-bold);
	padding: 18px 26px 18px 30px;
	text-decoration: none;
	text-transform: uppercase;
  width: 100%;
  transition: all ease 0.3s;
}
.form-submit-btn:hover {
  opacity: 0.8;
}
.form-submit-btn.inactive {
  opacity: 0.7;
}

.form-submit-btn-desk {
  display: none;
}

.file-text {
  font-size: 14px;
}

}

</style>