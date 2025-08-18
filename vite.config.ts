import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import laravel from 'laravel-vite-plugin'
import path from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.tsx',
      ],
      refresh: true,
    }),
    react(),
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources/js'),
      '@components': path.resolve(__dirname, './resources/js/components'),
      '@hooks': path.resolve(__dirname, './resources/js/hooks'),
      '@lib': path.resolve(__dirname, './resources/js/lib'),
      '@types': path.resolve(__dirname, './resources/js/types'),
    },
  },
  server: {
    port: 3000,
    host: true,
  },
  build: {
    outDir: 'public/build',
    sourcemap: true,
  },
})
