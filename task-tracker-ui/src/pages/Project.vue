<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Project</h1>
        <p class="text-sm text-gray-500">Kelola semua project</p> </div>
      <button 
        @click="openModal('create')"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
      >
        + Tambah Project </button>
    </div>

    <div class="bg-white p-4 rounded-t-xl border border-gray-200 flex gap-4">
      <input 
        v-model="searchQuery" 
        @keyup.enter="fetchProjects"
        type="text" 
        placeholder="Cari project..." 
        class="border border-gray-300 rounded-md px-3 py-2 text-sm w-64 focus:ring-blue-500 focus:border-blue-500 outline-none"
      >
      <button @click="fetchProjects" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-md text-sm transition-colors">
        Cari
      </button>
    </div>

    <div class="bg-white border-x border-b border-gray-200 rounded-b-xl overflow-hidden shadow-sm">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase">
          <tr>
            <th class="px-6 py-3 border-b">Nama</th> <th class="px-6 py-3 border-b">Deskripsi</th> <th class="px-6 py-3 border-b">Status</th> <th class="px-6 py-3 border-b">Dibuat</th> <th class="px-6 py-3 border-b text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-if="isLoading">
            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Memuat data...</td>
          </tr>
          <tr v-else-if="projects.length === 0">
            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada project ditemukan.</td>
          </tr>
          <tr v-else v-for="project in projects" :key="project.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium text-gray-900">{{ project.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">{{ project.description }}</td>
            <td class="px-6 py-4">
              <span 
                class="px-2 py-1 text-xs font-semibold rounded-full"
                :class="project.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
              >
                {{ project.status === 'active' ? 'Active' : 'Archived' }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(project.created_at) }}</td>
            <td class="px-6 py-4 text-right space-x-2">
              <router-link :to="`/projects/${project.id}`" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Detail</router-link> <button @click="openModal('edit', project)" class="text-orange-500 hover:text-orange-700 text-sm font-medium">Edit</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="isModalOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
          <h2 class="text-xl font-bold">{{ modalMode === 'create' ? 'Tambah Project Baru' : 'Edit Project' }}</h2> <button @click="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        
        <form @submit.prevent="submitForm" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Project</label> <input v-model="form.name" type="text" placeholder="Masukkan nama project" class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500" :class="{'border-red-500': errors.name}">
            <span v-if="errors.name" class="text-xs text-red-500 mt-1 block">{{ errors.name[0] }}</span> </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label> <textarea v-model="form.description" rows="3" placeholder="Deskripsi project..." class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500" :class="{'border-red-500': errors.description}"></textarea>
            <span v-if="errors.description" class="text-xs text-red-500 mt-1 block">{{ errors.description[0] }}</span> </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label> <select v-model="form.status" class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500">
              <option value="active">Active</option> <option value="archived">Archived</option>
            </select>
          </div>

          <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium">Batal</button> <button type="submit" :disabled="isSubmitting" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium disabled:opacity-50">
              {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }} </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { projectService, type Project, type ProjectPayload } from '../services/project.service';

const projects = ref<Project[]>([]);
const isLoading = ref(true);
const searchQuery = ref('');

// Modal State
const isModalOpen = ref(false);
const modalMode = ref<'create' | 'edit'>('create');
const isSubmitting = ref(false);
const currentEditId = ref<number | null>(null);

const form = reactive<ProjectPayload>({
  name: '',
  description: '',
  status: 'active'
});
const errors = ref<Record<string, string[]>>({});

const fetchProjects = async () => {
  isLoading.value = true;
  try {
    projects.value = await projectService.getProjects(searchQuery.value);
  } catch (error) {
    console.error('Failed to fetch projects', error);
  } finally {
    isLoading.value = false;
  }
};

const openModal = (mode: 'create' | 'edit', project?: Project) => {
  modalMode.value = mode;
  errors.value = {};
  
  if (mode === 'edit' && project) {
    currentEditId.value = project.id;
    form.name = project.name;
    form.description = project.description;
    form.status = project.status;
  } else {
    currentEditId.value = null;
    form.name = '';
    form.description = '';
    form.status = 'active';
  }
  
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const submitForm = async () => {
  isSubmitting.value = true;
  errors.value = {};

  try {
    if (modalMode.value === 'create') {
      await projectService.createProject(form);
    } else if (modalMode.value === 'edit' && currentEditId.value) {
      await projectService.updateProject(currentEditId.value, form);
    }
    
    closeModal();
    fetchProjects(); // Refresh tabel
  } catch (err: any) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors; // Tampilkan pesan error per-field [cite: 410]
    } else {
      alert(err.response?.data?.message || 'Terjadi kesalahan sistem.');
    }
  } finally {
    isSubmitting.value = false;
  }
};

const formatDate = (dateString: string) => {
  const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'short', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

onMounted(() => {
  fetchProjects();
});
</script>