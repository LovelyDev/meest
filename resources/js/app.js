import '@babel/polyfill';
// require('./bootstrap');
window.Vue = require('vue');

// Contrib components.
import VueFlashMessage from 'vue-flash-message';
import axios from 'axios';
import store from 'Store';
import Vue2Filters from 'vue2-filters'
import VueNumeric from 'vue-numeric';
import velocity from 'velocity-animate';
import Element from 'element-ui';
import VueMaterial from 'vue-material';
import VueChatScroll from 'vue-chat-scroll'
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

// Custom components.
Vue.component('v-select', vSelect);
Vue.use(VueChatScroll);
Vue.use(VueMaterial);
Vue.use(Element);
Vue.use(Vue2Filters);
Vue.use(VueNumeric);
// Install BootstrapVue
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);
Vue.use(VueFlashMessage, {
    messageOptions: {
        pauseOnInteract: true,
        timeout: 5000,
    }
});

Vue.component(
  'passport-clients',
  require('./components/passport/Clients.vue').default
);

Vue.component(
  'passport-authorized-clients',
  require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
  'passport-personal-access-tokens',
  require('./components/passport/PersonalAccessTokens.vue').default
);

/**
 * Set the axios default headers.
 */
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Add axios in vue prototype so it's accessible in any child vue components
 */
Vue.prototype.$axios = axios;

/**
 * Auto-register base components.
 */
const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(
  key.split('/').pop().split('.')[0], files(key).default)
);

new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    store,
    components: {
    },
});
