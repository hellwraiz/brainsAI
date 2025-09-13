
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const email = ref('')
const password = ref('')
const router = useRouter()

const login = async () => {
  try {
    // Get CSRF token first
    await axios.get('/sanctum/csrf-cookie')
    
    // Login
    await axios.post('/login', {
      email: email.value,
      password: password.value
    })
    
    // Redirect to admin
    router.push('/admin')
  } catch (error) {
    alert('Login failed')
  }
}
</script>


<template>
  <div class="container flex flex-col items-center">
    <div class="flex flex-col items-start">

      <h2>Admin Login</h2>
      <form @submit.prevent="login">
        <input class="rounded-sm border-2 mr-3" v-model="email" type="email" placeholder="Email" required />
        <input class="rounded-sm border-2 mr-3" v-model="password" type="password" placeholder="Password" required />
        <button type="submit">Login</button>
      </form>
    </div>
  </div>
</template>

<style scoped>

</style>