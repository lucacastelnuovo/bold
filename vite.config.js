import { defineConfig, loadEnv } from "vite";
import laravel, { refreshPaths } from "laravel-vite-plugin";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), "");
    const host = new URL(env.APP_URL).host;

    return {
        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/js/app.js"],
                refresh: [...refreshPaths, "app/Livewire/**"],
                detectTls: host,
            }),
        ],
    };
});
