/// <reference types="vitest" />
import { defineConfig } from "vite"
import react from "@vitejs/plugin-react"
import svgrPlugin from "vite-plugin-svgr"
import { resolve } from "path"

const production = process.env.NODE_ENV === "production"

export default defineConfig({
  mode: production ? "production" : "development",
  server: {
    hmr: !production
  },
  build: {
    outDir: "build",
    minify: production ? "esbuild" : false,
    rollupOptions: {
      input: {
        main: resolve(__dirname, "index.html"),
        widget: resolve(__dirname, "widget/index.html")
      }
    }
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
