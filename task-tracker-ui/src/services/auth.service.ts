import api from '../lib/axios';

// Definisikan tipe data untuk payload (data yang dikirim)
export interface LoginPayload {
  email: string;
  password: string;
}

// Definisikan tipe data untuk response dari backend
export interface User {
  id: number;
  name: string;
  email: string;
  is_admin: boolean;
}

export interface LoginResponse {
  message: string;
  access_token: string;
  token_type: string;
  user: User;
}

export const authService = {
  /**
   * Mengirim request login menggunakan email & password
   */
  async login(payload: LoginPayload): Promise<LoginResponse> {
    const response = await api.post<LoginResponse>('/login', payload);
    return response.data;
  },

  /**
   * Mengirim request logout untuk menghapus token aktif (PAT)
   */
  async logout(): Promise<void> {
    await api.post('/logout');
  }
};