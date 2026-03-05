import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import Login from '../pages/Login.vue';
import { createTestingPinia } from '@pinia/testing';

// Mock vue-router agar tidak error saat komponen memanggil useRouter()
vi.mock('vue-router', () => ({
  useRouter: () => ({
    push: vi.fn(),
  }),
}));

describe('Login.vue', () => {
  it('harus merender form login dengan input email dan password', () => {
    // 1. Mount (Render) komponen ke dalam memori
    const wrapper = mount(Login, {
      global: {
        // Suntikkan Pinia palsu untuk keperluan testing
        plugins: [createTestingPinia({ createSpy: vi.fn })], 
      },
    });

    // 2. Assert (Validasi) teks header sesuai mockup
    expect(wrapper.text()).toContain('Masuk ke akun kamu');

    // 3. Assert (Validasi) elemen form tersedia
    const emailInput = wrapper.find('input[type="email"]');
    const passwordInput = wrapper.find('input[type="password"]');
    const submitButton = wrapper.find('button[type="submit"]');

    expect(emailInput.exists()).toBe(true);
    expect(passwordInput.exists()).toBe(true);
    expect(submitButton.exists()).toBe(true);
  });
});