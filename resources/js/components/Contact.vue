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

    <div class="h-screen flex justify-between flex-col" >
      <div class="containerr" >
          <h1>TALK TO THE BRAINS BEHIND MOZGI. GOT A WILD IDEA? WE’VE GOT WILDER SOLUTIONS.</h1>
          <div class="flex gap-5">
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
              <div class="flex gap-10">
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
  
      <footer class="bg-[#262626] pt-[55px] pb-[70px]">
          <div>
              <h1 class="text-white font-bold text-5xl m-0">CONTACT US</h1>
              <form @submit.prevent="handleSubmit" class="">
                  <div class="flex">
                      <div class="flex flex-col" >
                          <div class="flex">
                              <div>
                                  <label for="name" class="block text-sm font-medium mb-1">Name *</label>
                                  <input
                                  id="name"
                                  v-model="form.name"
                                  type="text"
                                  required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  :class="{ 'border-red-500': errors.name }"
                                  />
                                  <span v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</span>
                              </div>
              
                              <div>
                                  <label for="email" class="block text-sm font-medium mb-1">Email *</label>
                                  <input
                                  id="email"
                                  v-model="form.email"
                                  type="email"
                                  required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  :class="{ 'border-red-500': errors.email }"
                                  />
                                  <span v-if="errors.email" class="text-red-500 text-sm">{{ errors.email }}</span>
                              </div>
              
                              <div>
                                  <label for="phone" class="block text-sm font-medium mb-1">Phone</label>
                                  <input
                                  id="phone"
                                  v-model="form.phone"
                                  type="tel"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  :class="{ 'border-red-500': errors.phone }"
                                  />
                                  <span v-if="errors.phone" class="text-red-500 text-sm">{{ errors.phone }}</span>
                              </div>
                          </div>
          
                          <div class="flex">
                              <div>
                                  <label for="description" class="block text-sm font-medium mb-1">Description *</label>
                                  <textarea
                                  id="description"
                                  v-model="form.description"
                                  required
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  :class="{ 'border-red-500': errors.description }"
                                  placeholder="Tell us about your project..."
                                  ></textarea>
                                  <span v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</span>
                              </div>
              
                              <div>
                                  <label for="files" class="block text-sm font-medium mb-1">Files (optional)</label>
                                  <input
                                  id="files"
                                  @change="handleFileChange"
                                  type="file"
                                  multiple
                                  accept="image/*,.pdf,.doc,.docx"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  />
                                  <p class="text-xs text-gray-500 mt-1">Max 5MB per file. Images, PDFs, and Word documents only.</p>
                                  
                                  <!-- File List -->
                                  <div v-if="selectedFiles.length > 0" class="mt-2">
                                  <div v-for="(file, index) in selectedFiles" :key="index" class="flex items-center justify-between bg-gray-100 p-2 rounded mb-1">
                                      <span class="text-sm">{{ file.name }}</span>
                                      <button @click="removeFile(index)" type="button" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                                  </div>
                                  </div>
                              </div>
                          </div>
                      </div>
      
                      <!-- Submit Button -->
                      <button
                      type="submit"
                      :disabled="isSubmitting"
                      class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                      <span v-if="isSubmitting">Sending...</span>
                      <span v-else>Send Message</span>
                      </button>
                  </div>
  
                  <!-- Success/Error Messages -->
                  <div v-if="successMessage" class="bg-green-100 text-green-700 p-3 rounded-md">
                      {{ successMessage }}
                  </div>
                  <div v-if="errorMessage" class="bg-red-100 text-red-700 p-3 rounded-md">
                      {{ errorMessage }}
                  </div>
              </form>
          </div>
      </footer>
    </div>
</template>

<style scoped>

.containerr {
    justify-items: center;
    padding: 0px 50px 50px;
    max-width: 1540px;
    margin: 0px auto;
}

.containerr > h1 {
  font-size: 2.4em;
  font-weight: 900;
  margin: 50px 0 30px;
  width: 60%;
  line-height: 1em;
}

.containerr > div {
    display: flex;
    width: 80%;
    gap: 20px;
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

.info-value:hover {
    opacity: 0.5;
}

footer {
    background-color: #262626;
    padding: 55px 0px 20px;
    display: flex;
    width: 100%;
    align-items: flex-end;
}

footer > div {
    margin: auto;
    padding: 0px 50px 50px;
    width: 1540px;
}

footer h1 {
  font-size: 2.4em;
  font-weight: 900;
}

</style>