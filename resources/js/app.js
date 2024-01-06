import './bootstrap';

// https://www.youtube.com/watch?v=Su8dWVrHdkc
import { createApp } from 'vue'
import SystemBuilder from '/resources/js/components/SystemBuilder.vue';
const app = createApp({
    components: {
        SystemBuilder,
    }
});
app.mount("#app");