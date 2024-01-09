import './bootstrap';

// https://www.youtube.com/watch?v=Su8dWVrHdkc
import { createApp } from 'vue'
import SystemBuilder from '/resources/js/components/SystemBuilder.vue';
import HighLowDropdown from '/resources/js/components/dropdowns/HighLowDropdown.vue';
import OptionDropdown from '/resources/js/components/dropdowns/OptionDropdown.vue';
const app = createApp({
    components: {
        SystemBuilder,
        HighLowDropdown,
        OptionDropdown
    }
});
app.mount("#app");