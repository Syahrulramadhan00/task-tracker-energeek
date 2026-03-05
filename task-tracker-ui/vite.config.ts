import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(),tailwindcss()],
  test: {
    environment: 'jsdom', // Wajib untuk simulasi browser di terminal
    globals: true,        // Mengizinkan penggunaan describe, it, expect tanpa import
  }
})
