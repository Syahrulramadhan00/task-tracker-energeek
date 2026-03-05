<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Semua Task</h1>
        <p class="text-sm text-gray-500">Lihat dan kelola seluruh task dari semua project</p>
      </div>
    </div>

    <div class="bg-white p-4 rounded-t-xl border border-gray-200 flex gap-4">
      <input 
        v-model="searchQuery" 
        @keyup.enter="fetchTasks"
        type="text" 
        placeholder="Cari nama task..." 
        class="border border-gray-300 rounded-md px-3 py-2 text-sm w-64 focus:ring-blue-500 focus:border-blue-500 outline-none"
      >
      <button @click="fetchTasks" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-md text-sm transition-colors">
        Cari
      </button>
    </div>

    <div class="bg-white border-x border-b border-gray-200 rounded-b-xl overflow-hidden shadow-sm">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase">
          <tr>
            <th class="px-6 py-3 border-b">Nama Task</th>
            <th class="px-6 py-3 border-b">Project</th>
            <th class="px-6 py-3 border-b">Status / Kategori</th>
            <th class="px-6 py-3 border-b">Due Date</th>
            <th class="px-6 py-3 border-b text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-if="isLoading">
            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Memuat data task...</td>
          </tr>
          <tr v-else-if="tasks.length === 0">
            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada task ditemukan.</td>
          </tr>
          <tr v-else v-for="task in tasks" :key="task.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <p class="font-medium text-gray-900">{{ task.title }}</p>
              <p class="text-xs text-gray-500 truncate max-w-xs mt-0.5">{{ task.description }}</p>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700 font-medium">
              {{ task.project?.name ?? 'Tanpa Project' }}
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                {{ task.category?.name ?? 'TODO' }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
              <span :class="{'text-red-500 font-semibold': isOverdue(task.due_date) && task.category?.name !== 'DONE'}">
                {{ formatDate(task.due_date) }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-3">
              <router-link v-if="task.project_id" :to="`/projects/${task.project_id}`" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Lihat Board
              </router-link>
              <button @click="handleDelete(task.id)" class="text-red-500 hover:text-red-700 text-sm font-medium">
                Hapus
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { taskService, type Task } from '../services/task.service';

const tasks = ref<Task[]>([]);
const isLoading = ref(true);
const searchQuery = ref('');

const fetchTasks = async () => {
  isLoading.value = true;
  try {
    tasks.value = await taskService.getTasks(searchQuery.value);
  } catch (error) {
    console.error('Gagal memuat daftar task', error);
  } finally {
    isLoading.value = false;
  }
};

const handleDelete = async (id: number) => {
  if (!confirm('Apakah Anda yakin ingin menghapus task ini?')) return;
  
  try {
    await taskService.deleteTask(id);
    await fetchTasks(); // Refresh tabel setelah dihapus
  } catch (error) {
    alert('Gagal menghapus task. Silakan coba lagi.');
  }
};

const formatDate = (dateString: string) => {
  if (!dateString) return '-';
  const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'short', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Fungsi kecil untuk mengecek apakah task sudah lewat tenggat waktu
const isOverdue = (dateString: string) => {
  if (!dateString) return false;
  return new Date(dateString).getTime() < new Date().setHours(0,0,0,0);
};

onMounted(() => {
  fetchTasks();
});
</script>