<template>
  <div>
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-1">Dashboard</h1>
      <p class="text-gray-500">Selamat datang kembali, {{ authStore.user?.name ?? 'User' }}</p>
    </div>

    <div v-if="isLoading" class="text-gray-500 animate-pulse">Memuat data dashboard...</div>

    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between border-l-4 border-l-blue-500">
          <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Project Aktif</h3>
            <p class="text-4xl font-bold text-gray-800">{{ stats.active_projects_count }}</p>
          </div>
          <div class="p-4 bg-blue-50 text-blue-500 rounded-full">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between border-l-4 border-l-red-500">
          <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Task Belum Selesai</h3>
            <p class="text-4xl font-bold text-gray-800">{{ stats.incomplete_tasks_count }}</p>
          </div>
          <div class="p-4 bg-red-50 text-red-500 rounded-full">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
          <h2 class="text-lg font-bold text-gray-800">Task Mendekati Due Date</h2>
        </div>
        
        <div v-if="stats.upcoming_tasks.length === 0" class="p-6 text-center text-gray-500">
          Tidak ada task yang mendekati batas waktu.
        </div>

        <ul v-else class="divide-y divide-gray-100">
          <li v-for="task in stats.upcoming_tasks" :key="task.id" class="px-6 py-4 hover:bg-gray-50 transition-colors flex justify-between items-center">
            <div>
              <h4 class="text-md font-semibold text-gray-800">{{ task.title }}</h4>
              <p class="text-sm text-gray-500">{{ task.project?.name ?? 'Tidak ada project' }}</p>
            </div>
            <div class="text-right">
              <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                {{ task.category?.name ?? 'PENDING' }}
              </span>
              <p class="text-sm text-gray-500 mt-1">{{ formatDate(task.due_date) }}</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../lib/axios';

const authStore = useAuthStore();
const isLoading = ref(true);

const stats = ref({
  active_projects_count: 0,
  incomplete_tasks_count: 0,
  upcoming_tasks: [] as any[]
});

const fetchDashboardData = async () => {
  try {
    const response = await api.get('/dashboard');
    stats.value = response.data;
  } catch (error) {
    console.error('Gagal mengambil data dashboard', error);
  } finally {
    isLoading.value = false;
  }
};

const formatDate = (dateString: string) => {
  if (!dateString) return '-';
  const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'short', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

onMounted(() => {
  fetchDashboardData();
});
</script>