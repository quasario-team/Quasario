import react from "@vitejs/plugin-react";
import { defineConfig } from "vite";

export default defineConfig({
  plugins: [react()],
  server: {
    port: 5173,
    // Every request that starts with /api is sent to Laravel (php artisan serve).
    // The browser sees one address only, so we do not need any CORS setup.
    proxy: {
      "/api": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
      },
      // Phase 2: the uploaded pictures (/storage/profiles/...) also live in Laravel.
      "/storage": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
      },
    },
  },
});
