<script setup>

import { ref, reactive } from 'vue'
import axios from 'axios'

// Form data
const form = reactive({
  name: '',
  email: '',
  phone: '',
  description: ''
})

// File handling
const selectedFiles = ref([])

// Form state
const isSubmitting = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const errors = reactive({})

// Handle file selection
const handleFileChange = (event) => {
  const files = Array.from(event.target.files)
  
  // Validate file size (5MB limit)
  const validFiles = files.filter(file => {
    if (file.size > 5 * 1024 * 1024) {
      errorMessage.value = `File "${file.name}" is too large. Maximum size is 5MB.`
      return false
    }
    return true
  })
  
  selectedFiles.value = [...selectedFiles.value, ...validFiles]
}

// Remove file from selection
const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
}

// Clear messages
const clearMessages = () => {
  successMessage.value = ''
  errorMessage.value = ''
  Object.keys(errors).forEach(key => delete errors[key])
}

// Validate form
const validateForm = () => {
  clearMessages()
  let isValid = true

  if (!form.name.trim()) {
    errors.name = 'Name is required'
    isValid = false
  }

  if (!form.email.trim()) {
    errors.email = 'Email is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
    isValid = false
  }

  if (!form.description.trim()) {
    errors.description = 'Description is required'
    isValid = false
  }

  return isValid
}

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
  if (!validateForm()) return

  isSubmitting.value = true
  clearMessages()

  try {
    // Create FormData for file upload
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('email', form.email)
    formData.append('phone', form.phone)
    formData.append('description', form.description)
    
    // Append files
    selectedFiles.value.forEach((file, index) => {
      formData.append(`files[${index}]`, file)
    })

    // Send to your Laravel backend
    const response = await axios.post('/contact', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    // Success
    successMessage.value = 'Thank you! Your message has been sent successfully.'
    
    // Reset form
    Object.keys(form).forEach(key => form[key] = '')
    selectedFiles.value = []
    
    // Clear file input
    const fileInput = document.getElementById('files')
    if (fileInput) fileInput.value = ''

  } catch (error) {
    console.error('Contact form error:', error)
    
    if (error.response?.data?.errors) {
      // Handle Laravel validation errors
      Object.assign(errors, error.response.data.errors)
    } else {
      errorMessage.value = 'Sorry, there was an error sending your message. Please try again.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
      <div class="container" >
          <h1>TALK TO THE BRAINS BEHIND MOZGI. GOT A WILD IDEA? WE’VE GOT WILDER SOLUTIONS.</h1>
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
  
      <footer>
          <div>
              <h1>CONTACT US</h1>
              <form @submit.prevent="handleSubmit" class="">
                  <div class="contact-container">
                      <div class="contact-form" >
                          <div class="flex flex-col desktop:flex-row desktop:gap-[20px]">
                              <div class="input-box-top">
                                  <label for="name" class="input-label">Name</label>
                                  <input id="name" v-model="form.name" type="text" required class="input-field" :class="{ 'border-red-500': errors.name }" placeholder="Your Name"/>
                                  <span v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</span>
                              </div>
              
                              <div class="input-box-top">
                                  <label for="email" class="input-label">Email</label>
                                  <input id="email" v-model="form.email" type="email" required class="input-field" :class="{ 'border-red-500': errors.email }" placeholder="Your Email"/>
                                  <span v-if="errors.email" class="text-red text-sm">{{ errors.email }}</span>
                              </div>
              
                              <div class="input-box-top">
                                  <label for="phone" class="input-label">Phone</label>
                                  <input id="phone" v-model="form.phone" type="tel" class="input-field" :class="{ 'border-red-500': errors.phone }" placeholder="Your Phone"/>
                                  <span v-if="errors.phone" class="text-red-500 text-sm">{{ errors.phone }}</span>
                              </div>
                          </div>
          
                          <div class="flex flex-col desktop:flex-row desktop:gap-[34px] items-center">
                              <div class="w-full desktop:w-[660px] relative">
                                  <label for="description" class="input-label">Description</label>
                                  <textarea id="description" v-model="form.description" required rows=1 class="mb-[24px] desktop:mb-0 input-field input-field--alt max-h-[120px]" :class="{ 'border-red-500': errors.description }" placeholder="Tell us about your project..."></textarea>
                                  <span v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</span>
                              </div>
              
                              <div class="flex flex-col self-stretch desktop:self-center ">
                                  <label for="files" class="self-end cursor-pointer">
                                    <img src="/public/images/send_materials.png" alt="Upload" class="w-[180px] hover:opacity-50">
                                  </label>
                                  <input id="files" class="hidden" @change="handleFileChange" type="file" multiple accept="image/*,.pdf,.doc,.docx"/>
                                  
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
                      <button type="submit" :disabled="isSubmitting" class="form-submit-btn-desk">
                      <img src="/public/images/send_laptop.png" alt="send">
                      </button>
                      <button type="submit" class="form-submit-btn" aria-label="Send request">
                        <img width="36" height="36" src="/public/images/icons/send.svg" alt="send">
                        <span>SEND A REQUEST</span>
                      </button>
                  </div>
  
                  <!-- Success/Error Messages -->
                  <div v-if="successMessage" class="bg-green-100 text-green-700 p-3 rounded-md">
                      {{ successMessage }}
                  </div>
                  <div v-if="errorMessage" class="bg-red-100 text-red-700 p-3 rounded-md">
                      {{ errorMessage }}
                  </div>
                  <div id="form-messages" class="form-messages"></div>
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
  margin: 50px 0 30px;
  width: 65%;
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
  border: 2px solid rgba(#fff, .2);
  padding: 11px 5px 12px 24px;
  font-size: 16px;
  letter-spacing: .3px;
  line-height: 1.5;
  font-variation-settings: "width" 112.5;
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
  height: 220px;
  width: 220px;
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
  margin-top: 0;
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
}

.form-submit-btn-desk {
  display: none;
}

.file-text {
  font-size: 14px;
}

}

</style>