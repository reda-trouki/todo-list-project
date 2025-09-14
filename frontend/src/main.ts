import "./assets/main.css";
import "./plugins/axios"; // Initialize Axios configuration
import "./plugins/echo"; // Initialize Laravel Echo configuration

import { createApp } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./router";

const app = createApp(App);

app.use(createPinia());
app.use(router);

app.mount("#app");
