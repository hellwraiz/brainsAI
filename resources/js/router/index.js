import { createRouter, createWebHistory } from 'vue-router'
import Home from '../components/Home.vue'
import Work from '../components/Work.vue'
import Shorts from '../components/Shorts.vue'
import About from '../components/About.vue'
import Contact from '../components/Contact.vue'

const routes = [
  { path: '/', component: Home },
  { path: '/work', component: Work },
  { path: '/shorts', component: Shorts },
  { path: '/about', component: About },
  { path: '/contact', component: Contact },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router