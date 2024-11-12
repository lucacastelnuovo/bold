import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import flareSourcemapUploader from "@flareapp/vite";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), "");

    return {
        build: {
            sourcemap: true,
        },
        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/js/app.js"],
                refresh: true,
            }),
            flareSourcemapUploader({
                key: env.FLARE_PUBLIC_KEY,
                removeSourcemaps: true,
            }),
        ],
    };
});
