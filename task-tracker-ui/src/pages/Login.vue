<template>
  <div class="min-h-screen bg-[#1e2336] flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8">
      
      <div class="mb-8">
        <h1 class="text-2xl font-bold mb-1">
          <span class="text-gray-800">Task</span><span class="text-blue-500">Tracker</span>
        </h1>
        <p class="text-gray-500 text-sm">Masuk ke akun kamu</p> </div>

      <form @submit.prevent="handleLogin" class="space-y-5">
        
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
          <input 
            v-model="form.email" 
            type="email" 
            placeholder="admin@energeek.id" 
            class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
            :class="{'border-red-500': errors.email}"
          >
          <span v-if="errors.email" class="text-xs text-red-500 mt-1 block">{{ errors.email[0] }}</span>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
          <input 
            v-model="form.password" 
            type="password" 
            placeholder="••••••••" 
            class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
            :class="{'border-red-500': errors.password}"
          >
          <span v-if="errors.password" class="text-xs text-red-500 mt-1 block">{{ errors.password[0] }}</span>
        </div>

        <div v-if="generalError" class="p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-md">
          {{ generalError }}
        </div>

        <button 
          type="submit" 
          :disabled="isLoading"
          class="w-full bg-[#4f7cf7] hover:bg-blue-600 text-white font-semibold py-2.5 rounded-md transition-colors disabled:opacity-50 mt-4">
          {{ isLoading ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  email: '',
  password: ''
});

// State untuk manajemen error yang jelas dan deskriptif [cite: 401]
const errors = ref<Record<string, string[]>>({});
const generalError = ref('');
const isLoading = ref(false);

const handleLogin = async () => {
  isLoading.value = true;
  errors.value = {};
  generalError.value = '';

  try {
    await authStore.login(form.email, form.password);
    
    // Redirect ke Dashboard setelah berhasil login
    router.push({ name: 'Dashboard' }); 
    
  } catch (err: any) {
    if (err.response?.status === 422) {
      // Error validasi dari Laravel (misal: format email salah)
      errors.value = err.response.data.errors; 
    } else if (err.response?.status === 401) {
      // Error kredensial salah
      generalError.value = err.response.data.message;
    } else {
      generalError.value = 'Terjadi kesalahan pada server. Silakan coba lagi.';
    }
  } finally {
    isLoading.value = false;
  }
};
</script>