import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/dist', // Đường dẫn đến thư mục đầu ra (có thể thay đổi theo ý bạn)
        emptyOutDir: true,      // Xóa thư mục đầu ra trước khi build
    },
});
