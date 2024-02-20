import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import {createInertiaApp, Head, Link} from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import * as FaIcons from "oh-vue-icons/icons/fa";
import * as RemixIcons from "oh-vue-icons/icons/ri";
import {addIcons, OhVueIcon} from "oh-vue-icons";

const Fa = Object.values({ ...FaIcons });
const Ri = Object.values({ ...RemixIcons });
addIcons(...Fa, ...Ri);

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
            .component("v-icon", OhVueIcon)
            .mount(el);
    },
    title: title => `My App - ${title}`,
    progress: {
        color: '#FFFFFF',
        showSpinner: true,
    }
});
