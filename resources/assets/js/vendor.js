window.Vue = require('vue');

import draggable from 'vuedraggable';
Vue.component('draggable', draggable);

import vSelect from 'vue-select';
Vue.component('v-select', vSelect);

import Popper from 'vue-popperjs';
Vue.component('popper', Popper);

var VueResource = require('vue-resource');
Vue.use(VueResource);

var vueDebounce = require('vue-debounce');
Vue.use(vueDebounce);