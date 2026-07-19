import "../css/app.css";
import '@fortawesome/fontawesome-free/css/all.min.css';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import axios from "axios";
import { router } from "@inertiajs/vue3";

createInertiaApp({
  progress: {
    // The delay after which the progress bar will appear, in milliseconds...
    delay: 0,
    // The color of the progress bar...
    color: "#76aebc",
    // Whether to include the default NProgress styles...
    includeCSS: true,
    // Whether the NProgress spinner will be shown...
    showSpinner: false,
  },
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