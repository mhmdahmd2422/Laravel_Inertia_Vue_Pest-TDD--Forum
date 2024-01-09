import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import {createInertiaApp, Head, Link} from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import AppLayout from "@/Layouts/AppLayout.vue";
import Shell from "@/Components/Shell.vue";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    resolve: async (name) => {
        const { default: page } = await resolvePageComponent( `./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue"));

        // if (page.layout === undefined) {
        //     page.layout = Shell;
        // }
        //
        // if (page.props?.layout === null) {
        //     page.layout = undefined;
        // }

        return page;
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('Link', Link)
            .component('Head', Head)
            .mount(el);
    },
    title: title => `My App - ${title}`,
    progress: {
        color: '#FFFFFF',
        showSpinner: true,
    }
});
