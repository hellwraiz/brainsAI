import { createRouter, createWebHistory } from 'vue-router'
import Home from '../components/Home.vue'
import Work from '../components/Work.vue'
import Shorts from '../components/Shorts.vue'
import About from '../components/About.vue'
import Contact from '../components/Contact.vue'
import Login from '../components/Login.vue'
import Admin from '../components/Admin.vue'

const routes = [
  { path: '/', component: Home },
  { path: '/work', component: Work },
  { path: '/shorts', component: Shorts },
  { path: '/about', component: About },
  { path: '/contact', component: Contact },
  { path: '/loginAdmin', component: Login },
  { path: '/admin', component: Admin, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  if (to.meta.requiresAuth) {
    try {
      // Check if user is authenticated
      const response = await axios.get('/user')
      const user = response.data
      
      // Check if user is admin
      if (user.is_admin) {
        next() // Allow access
      } else {
        next('/') // Redirect to home
      }
    } catch (error) {
      // Not authenticated, redirect to login
      next('/loginAdmin') // You'll need a login page
    }
  } else {
    next() // No auth required, proceed
  }
})

export default router