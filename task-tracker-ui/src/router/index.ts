import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: () => import('../pages/Login.vue'),
      meta: { requiresAuth: false },
    },
    {
      // Parent route untuk semua halaman yang terproteksi
      path: '/',
      component: () => import('../layouts/MainLayout.vue'),
      meta: { requiresAuth: true }, // Terapkan meta ini di parent agar semua child ikut terlindungi
      children: [
        {
          // Redirect dari root '/' ke '/dashboard'
          path: '',
          redirect: '/dashboard',
        },
        {
          // Path child tidak perlu menggunakan slash '/' di awalnya
          path: 'dashboard',
          name: 'Dashboard',
          component: () => import('../pages/Dashboard.vue'),
        },
        // Nanti Anda bisa menambahkan rute untuk Project dan Task di sini
        {
          path: 'projects',
          name: 'Projects',
          component: () => import('../pages/Project.vue'),
        },
        {
          path: 'projects/:id',
          name: 'ProjectDetail',
          component: () => import('../pages/ProjectDetail.vue'),
        },
        {
          path: 'tasks',
          name: 'Tasks',
          component: () => import('../pages/Tasks.vue'),
        },
      ],
    },
  ],
});

// Navigation guard: redirect unauthenticated users to login
router.beforeEach((to) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: 'Login' };
  }

  // Redirect already-authenticated users away from login
  if (to.name === 'Login' && authStore.isAuthenticated) {
    return { name: 'Dashboard' };
  }
});

export default router;