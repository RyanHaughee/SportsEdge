import './bootstrap';

// https://www.youtube.com/watch?v=Su8dWVrHdkc
import { createApp } from 'vue'
import ExampleComponent from '/resources/js/components/ExampleComponent.vue';
const app = createApp({
    components: {
        ExampleComponent,
    }
});
app.mount("#app");