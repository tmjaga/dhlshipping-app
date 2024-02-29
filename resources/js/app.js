import './bootstrap';
import { createApp } from "vue";
import App from "./components/app.vue";
import PrimeVue, { defaultOptions } from 'primevue/config';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import 'primevue/resources/themes/tailwind-light/theme.css';
import 'primevue/resources/primevue.min.css';

const app = createApp(App)
app.use(PrimeVue, {
    locale: {
        ...defaultOptions.locale,
        firstDayOfWeek: 1,
    }
});

app.use(Toast, {
    transition: "Vue-Toastification__bounce",
    maxToasts: 5,
    newestOnTop: true,
    position: "top-right",
    timeout: 5000,
    closeOnClick: true,
    pauseOnHover: true,
    hideProgressBar: true,
    closeButton: "button",
    icon: true,
    rtl: false
});
app.mount('#app')

