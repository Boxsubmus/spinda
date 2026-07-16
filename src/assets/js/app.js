import "../css/app.css";
import '@fortawesome/fontawesome-free/css/all.min.css';

/*
import './about-me-editor';

import { Application } from '@hotwired/stimulus';

const application = Application.start();

const controllers = import.meta.glob('../controllers/*_controller.js', { eager: true });
for (const path in controllers) {
    const name = path.match(/\.\/controllers\/(.+)_controller\.js$/)[1].replace(/_/g, '-');
    application.register(name, controllers[path].default);
}
*/

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./pages/**/*.vue', { eager: true })
    return pages[`./pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
})