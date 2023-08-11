import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'
// filament css remove: error happen in AppServiceProvider Filament Vite setup
// 'resources/css/filament.css',
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',  'resources/js/app.js'],
            refresh: [...refreshPaths, 'app/Http/Livewire/**'],
        }),
    ],
})
