import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
    tailwindcss(),
  ],
  build: {
    // ! Production optimization
    minify: "terser", // Use Terser for aggressive minification
    terserOptions: {
      compress: {
        drop_console: true, // Remove console logs in production
        pure_funcs: ["console.log", "console.warn"], // Remove unused console
      },
    },
    // ! CSS optimization (remove lightningcss, use default)
    // cssMinify: removed - use default CSS minifier
    rollupOptions: {
      output: {
        // ! Code splitting strategy (reduce bundle size)
        manualChunks: {
          // Split vendor code into separate chunks
          vendor: ["axios"],
        },
        // ! Optimize chunk naming
        entryFileNames: "assets/js/[name].[hash].js",
        chunkFileNames: "assets/js/[name].[hash].js",
        assetFileNames: ({ name }) => {
          if (/\.css$/.test(name ?? "")) {
            return "assets/css/[name].[hash][extname]";
          } else if (/\.(woff|woff2|eot|ttf|otf)$/i.test(name ?? "")) {
            return "assets/fonts/[name].[hash][extname]";
          } else {
            return "assets/[name].[hash][extname]";
          }
        },
      },
    },
    // ! Enable source maps hanya di dev
    sourcemap: false,
    // ! Optimize asset handling
    assetsInlineLimit: 4096, // Inline assets < 4KB
    reportCompressedSize: true,
    chunkSizeWarningLimit: 1000, // Warn jika chunk > 1MB
  },
  // ! Server configuration untuk development
  server: {
    host: "127.0.0.1",
    port: 5173,
    hmr: {
      host: "127.0.0.1", // ! Match dengan server host
      port: 5173,
      protocol: "ws",
    },
  },
});
