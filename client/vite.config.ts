/// <reference types="vitest" />
import { defineConfig } from "vite"
import react from "@vitejs/plugin-react"
import svgrPlugin from "vite-plugin-svgr"
import { resolve } from "path"

const production = process.env.NODE_ENV === "production"

export default defineConfig({
  mode: production ? "production" : "development",
  server: {
    hmr: !production,
    proxy: {},
  },
  build: {
    outDir: "build",
    minify: production ? "esbuild" : false,
    rollupOptions: {
      input: {
        main: resolve(__dirname, "index.html"),
        widget: resolve(__dirname, "widget/index.html"),
      },
    },
    manifest: true,
    sourcemap: true,
  },
  assetsInclude: ["**/bofh.json"],
  plugins: [
    react(),
    svgrPlugin({
      svgrOptions: {
        icon: true,
      },
    }),
  ],
  test: {
    globals: true,
    environment: "jsdom",
    setupFiles: "./app/src/__test__/setupTests.ts",
  },
})
