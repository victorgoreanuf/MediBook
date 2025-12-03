import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [vue()],
    server: {
        host: true, // Needed for Docker to listen on 0.0.0.0
        port: 5173,
        watch: {
            usePolling: true // Needed for Docker volumes (especially on Windows/WSL/some M1 setups)
        },
        proxy: {
            // This tells Vite to intercept requests starting with /v1/api
            '/api/v1': {
                // 🚨 FIX: Use the Docker Compose service name ('nginx') as the hostname.
                // The Nginx container is listening on port 80 internally.
                target: 'http://nginx',

                changeOrigin: true,
                secure: false,

                // 💡 RECOMMENDED: Rewrite the path to remove the prefix
                // so Laravel sees the clean route (e.g., /users instead of /v1/api/users)
                // rewrite: (path) => path.replace(/^\/v1\/api/, ''),
            }
        }
    }
})