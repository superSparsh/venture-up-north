import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import AOS from 'aos'
import 'aos/dist/aos.css'
import { Splide, SplideSlide } from '@splidejs/vue-splide'
import '@splidejs/splide/dist/css/splide.min.css'


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast, {
                transition: 'Vue-Toastification__fade',
                maxToasts: 4,
                position: 'top-right',
                timeout: 3000,
                closeOnClick: true,
            });

        vueApp.mixin({
            mounted() {
                AOS.init({
                    once: true,         // animate only once
                    duration: 700,      // animation duration
                    easing: 'ease-out', // animation curve
                    offset: 50          // trigger offset from viewport
                });

                // Needed on route changes
                this.$nextTick(() => {
                    AOS.refresh()
                });
            }
        });

        vueApp.component('Splide', Splide)
        vueApp.component('SplideSlide', SplideSlide)

        return vueApp.mount(el);
    },
    progress: {
        color: '#8DA894',
    },
});
