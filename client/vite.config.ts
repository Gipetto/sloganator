/// <reference types="vitest" />
import { defineConfig } from "vite"
import react from "@vitejs/plugin-react"
import svgrPlugin from "vite-plugin-svgr"

const production = process.env.NODE_ENV === "production"

export default defineConfig({
  mode: production ? "production" : "development",
  server: {
    hmr: !production
  },
  build: {
    outDir: "build",
    minify: production ? "esbuild" : false
  },
  assetsInclude: [
    "**/bofh.json"
  ],
  plugins: [
    react(),
    svgrPlugin({
      svgrOptions: {
        icon: true
      }
    })
  ],
  test: {
    globals: true,
    environment: "jsdom",
    setupFiles: "./src/__test__/setupTests.ts"
  }
})
