<template>
  <div class="flex h-screen bg-gray-50 font-sans">
    <aside class="w-64 bg-[#1e2336] text-white flex flex-col justify-between">
      <div>
        <div class="p-6 border-b border-gray-700/50">
          <h2 class="text-2xl font-bold tracking-wide">
            Task<span class="text-blue-500">Tracker</span>
          </h2>
        </div>

        <nav class="mt-6 px-4 space-y-2">
          <router-link
            to="/dashboard"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
            active-class="bg-gray-800 text-blue-400"
            exact-active-class="bg-gray-800 text-blue-400"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Dashboard
          </router-link>
          
          <router-link
            to="/projects"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors"
            active-class="bg-gray-800 text-blue-400"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
            Project
          </router-link>

          <router-link
            to="/tasks"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors"
            active-class="bg-gray-800 text-blue-400"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            Task
          </router-link>
        </nav>
      </div>

      <div class="p-4 border-t border-gray-700/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg">
              {{ authStore.user?.name.charAt(0).toUpperCase() ?? 'U' }}
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-white">{{ authStore.user?.name ?? 'User' }}</p>
              <p class="text-xs text-gray-400">Administrator</p>
            </div>
          </div>
          <button @click="handleLogout" class="text-red-400 hover:text-red-300 transition-colors p-2" title="Keluar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
          </button>
        </div>
      </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
      <div class="flex-1 overflow-y-auto bg-gray-50 p-8">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

async function handleLogout() {
  await authStore.logout();
  router.push({ name: 'Login' });
}
</script>