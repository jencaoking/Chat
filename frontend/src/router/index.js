import { createRouter, createWebHistory } from 'vue-router'
import { isLoggedIn, isAdmin } from '../utils/auth'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/Register.vue')
  },
  {
    path: '/chat',
    name: 'Chat',
    component: () => import('../views/Chat.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin',
    name: 'Admin',
    component: () => import('../views/Admin.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/',
    redirect: '/chat'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isLoggedIn()) {
    next('/login')
    return
  }
  
  if (to.meta.requiresAdmin && !isAdmin()) {
    next('/chat')
    return
  }
  
  if ((to.path === '/login' || to.path === '/register') && isLoggedIn()) {
    next('/chat')
    return
  }
  
  next()
})

export default router