import "../css/app.css";
import '@fortawesome/fontawesome-free/css/all.min.css';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import axios from "axios";
import { router } from "@inertiajs/vue3";

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

router.on('navigate', (event) => {
  const token = event.detail.page.props.csrfToken
  if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token
  }
})