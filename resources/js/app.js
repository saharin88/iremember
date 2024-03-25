import './bootstrap';
import {createApp} from 'vue'
import App from '@/layouts/App.vue'
import router from '@/routes/index'
import { i18nVue } from 'laravel-vue-i18n'

createApp(App)
    .use(i18nVue, {
        lang: 'uk',
        resolve: async lang => {
            const langs = import.meta.glob('../../lang/*.json');
            return await langs[`../../lang/${lang}.json`]();
        }
    })
    .use(router)
    .mount('#app')
