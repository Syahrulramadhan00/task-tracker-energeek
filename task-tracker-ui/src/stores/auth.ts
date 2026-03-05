import { defineStore } from 'pinia';
import { authService, type User } from '../services/auth.service';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('access_token') || null as string | null,
    user: JSON.parse(localStorage.getItem('user') || 'null') as User | null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(email: string, password: string) {
      // Panggil API dari service layer
      const data = await authService.login({ email, password });
      
      this.token = data.access_token;
      this.user = data.user;
      
      // Simpan ke localStorage agar tidak logout saat halaman di-refresh
      localStorage.setItem('access_token', this.token);
      localStorage.setItem('user', JSON.stringify(this.user));
    },
    async logout() {
      try {
        // Panggil endpoint logout dari service untuk revoke token [cite: 360]
        await authService.logout(); 
      } finally {
        // Apapun yang terjadi (berhasil atau gagal di backend), 
        // pastikan token dihapus dari sisi frontend
        this.token = null;
        this.user = null;
        localStorage.removeItem('access_token');
        localStorage.removeItem('user');
      }
    }
  }
});